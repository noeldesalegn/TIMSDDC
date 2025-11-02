<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewerReport;
use App\Models\User;

class InterviewerReportsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $perPage = (int) $request->query('per_page', 10);

        $query = InterviewerReport::where('user_id', $user->id);
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('body', 'like', "%$q%");
            });
        }
        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate($perPage)->withQueryString();
        return view('interviewer.reports.index', compact('reports', 'q', 'status', 'perPage'));
    }

    public function create()
    {
        return view('interviewer.reports.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'category' => ['nullable','string','max:50'],
            'body' => ['required','string'],
            'taxpayer_id' => ['nullable','integer','exists:users,id'],
            'taxpayer_email' => ['nullable','email'],
            'status' => ['nullable','string','in:draft,submitted,approved,rejected'],
        ]);

        $taxpayerId = $data['taxpayer_id'] ?? null;
        if (!$taxpayerId && !empty($data['taxpayer_email'])) {
            $taxpayer = User::where('email', $data['taxpayer_email'])->where('role','taxpayer')->first();
            $taxpayerId = $taxpayer?->id;
        }

        $report = InterviewerReport::create([
            'user_id' => $user->id,
            'taxpayer_id' => $taxpayerId,
            'title' => $data['title'],
            'category' => $data['category'] ?? null,
            'body' => $data['body'],
            'status' => $data['status'] ?? 'draft',
        ]);

        return redirect()->route('interviewer.reports.show', $report)->with('success', 'Report saved.');
    }

    public function show(InterviewerReport $report)
    {
        abort_if(auth()->id() !== $report->user_id, 403);
        $report->load('taxpayer');
        return view('interviewer.reports.show', compact('report'));
    }

    public function update(Request $request, InterviewerReport $report)
    {
        abort_if(auth()->id() !== $report->user_id, 403);
        $data = $request->validate([
            'title' => ['sometimes','string','max:255'],
            'category' => ['sometimes','nullable','string','max:50'],
            'body' => ['sometimes','string'],
            'status' => ['sometimes','string','in:draft,submitted,approved,rejected'],
        ]);
        $report->fill($data)->save();
        return back()->with('success', 'Report updated.');
    }
}
