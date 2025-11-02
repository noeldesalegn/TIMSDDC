<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\TaxSummary;
use App\Models\Complaint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportsController extends Controller
{
    protected function range(Request $request): array
    {
        $preset = $request->string('preset')->toString();
        $start = $request->date('start');
        $end = $request->date('end');

        if ($preset) {
            $today = now()->startOfDay();
            switch ($preset) {
                case 'today':
                    return [$today, now()];
                case 'week':
                    return [$today->copy()->startOfWeek(), now()];
                case 'month':
                    return [$today->copy()->startOfMonth(), now()];
                case 'year':
                    return [$today->copy()->startOfYear(), now()];
            }
        }

        $startDate = $start ? Carbon::parse($start)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $end ? Carbon::parse($end)->endOfDay() : now();
        return [$startDate, $endDate];
    }

    public function index(Request $request)
    {
        [$start, $end] = $this->range($request);
        $type = $request->query('type', 'tax_collection');
        $taxType = $request->query('tax_type');
        $category = $request->query('category');

        return view('admin.reports.index', [
            'type' => $type,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'taxType' => $taxType,
            'category' => $category,
        ]);
    }

    public function data(Request $request)
    {
        [$start, $end] = $this->range($request);
        $type = $request->query('type', 'tax_collection');
        $taxType = $request->query('tax_type');
        $category = $request->query('category');

        $cacheKey = 'reports:data:'.md5(json_encode([
            'type' => $type,
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String(),
            'taxType' => $taxType,
            'category' => $category,
        ]));

        $payload = Cache::remember($cacheKey, 60, function () use ($type, $start, $end, $taxType, $category) {
            if ($type === 'tax_collection' || $type === 'revenue') {
                $base = Payment::where('status', 'completed')
                    ->whereBetween('created_at', [$start, $end]);

                $series = (clone $base)
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();

                $total = (float) $base->sum('amount');
                return [
                    'kind' => 'agg',
                    'series' => $series,
                    'total' => round($total, 2),
                ];
            }

            if ($type === 'compliance') {
                $query = TaxSummary::query()
                    ->whereBetween('created_at', [$start, $end]);
                if ($taxType) {
                    $query->where('tax_type', $taxType);
                }
                if ($category) {
                    $query->where('category', $category);
                }
                $paid = (int) (clone $query)->where('status', 'paid')->count();
                $pending = (int) (clone $query)->where('status', 'pending')->count();
                $overdue = (int) (clone $query)->where('status', 'overdue')->count();
                return [
                    'kind' => 'compliance',
                    'paid' => $paid,
                    'pending' => $pending,
                    'overdue' => $overdue,
                    'total' => $paid + $pending + $overdue,
                ];
            }

            return ['kind' => 'error'];
        });

        if (($payload['kind'] ?? '') === 'agg') {
            return response()->json([
                'series' => $payload['series'],
                'total' => $payload['total'],
            ]);
        }

        if (($payload['kind'] ?? '') === 'compliance') {
            return response()->json([
                'paid' => $payload['paid'],
                'pending' => $payload['pending'],
                'overdue' => $payload['overdue'],
                'total' => $payload['total'],
            ]);
        }

        return response()->json(['error' => 'Unsupported report type'], 400);
    }

    public function export(Request $request): StreamedResponse
    {
        [$start, $end] = $this->range($request);
        $type = $request->query('type', 'tax_collection');
        $taxType = $request->query('tax_type');
        $category = $request->query('category');

        $filename = 'report_'.$type.'_'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($type, $start, $end, $taxType, $category) {
            $out = fopen('php://output', 'w');
            if ($type === 'tax_collection' || $type === 'revenue') {
                fputcsv($out, ['Period','Total Amount']);
                $query = Payment::where('status', 'completed')
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as total")
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                foreach ($query as $row) {
                    fputcsv($out, [$row->period, number_format((float)$row->total, 2, '.', '')]);
                }
            } elseif ($type === 'compliance') {
                fputcsv($out, ['Status','Count']);
                $query = TaxSummary::whereBetween('created_at', [$start, $end]);
                if ($taxType) $query->where('tax_type', $taxType);
                if ($category) $query->where('category', $category);
                $counts = [
                    'paid' => (clone $query)->where('status', 'paid')->count(),
                    'pending' => (clone $query)->where('status', 'pending')->count(),
                    'overdue' => (clone $query)->where('status', 'overdue')->count(),
                ];
                foreach ($counts as $status => $count) {
                    fputcsv($out, [$status, $count]);
                }
            } else {
                fputcsv($out, ['Unsupported report type']);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
