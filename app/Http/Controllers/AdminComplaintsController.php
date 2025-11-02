<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\User;

class AdminComplaintsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString(); // submitted|in_progress|resolved|all
        $q = $request->string('q')->toString();
        $perPage = (int) $request->query('per_page', 10);

        $query = Complaint::query()->with('user');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('subject', 'like', "%$q%")
                    ->orWhere('message', 'like', "%$q%");
            });
        }

        // Priority-like sorting (submitted > in_progress > resolved), then latest
        $statusOrder = ['submitted' => 0, 'in_progress' => 1, 'resolved' => 2];
        $query->getQuery()->orders = null; // reset default orders
        $query->orderByRaw("CASE status WHEN 'submitted' THEN 0 WHEN 'in_progress' THEN 1 ELSE 2 END")
              ->orderBy('created_at', 'desc');

        $complaints = $query->paginate($perPage)->withQueryString();

        $analytics = [
            'total' => Complaint::count(),
            'submitted' => Complaint::where('status', 'submitted')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'analytics', 'status', 'q', 'perPage'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load('user');
        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:submitted,in_progress,resolved'],
            'response' => ['nullable', 'string', 'max:5000'],
        ]);

        $complaint->status = $data['status'];
        if (array_key_exists('response', $data)) {
            $complaint->response = $data['response'];
        }
        $complaint->save();

        return back()->with('success', 'Complaint updated.');
    }
}
