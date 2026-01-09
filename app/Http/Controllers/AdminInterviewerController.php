<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InterviewerUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AdminInterviewerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'interviewer');

        // Search by name or email
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->query('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $interviewers = $query->withCount('uploads')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Get stats
        $stats = [
            'total' => User::where('role', 'interviewer')->count(),
            'active' => User::where('role', 'interviewer')->where('status', 'active')->count(),
            'inactive' => User::where('role', 'interviewer')->where('status', 'inactive')->count(),
        ];

        return view('admin.interviewers.index', compact('interviewers', 'stats'));
    }

    public function uploads(User $interviewer)
    {
        abort_if($interviewer->role !== 'interviewer', 404);

        $uploads = InterviewerUpload::with(['uploader', 'taxpayer'])
            ->where('user_id', $interviewer->id)
            ->latest()
            ->get()
            ->groupBy(fn ($u) => optional($u->taxpayer)->name ?? 'No Taxpayer');

        return view('admin.interviewers.show', compact('interviewer', 'uploads'));
    }

    public function viewUpload(InterviewerUpload $upload)
    {
        return response()->file(
            storage_path('app/public/' . $upload->path)
        );
    }

    public function destroy(User $interviewer)
    {
        abort_if($interviewer->role !== 'interviewer', 403);

        // Soft delete style (recommended)
        $interviewer->update(['status' => 'inactive']);

        return redirect()
            ->route('admin.interviewers.index')
            ->with('success', 'Interviewer disabled successfully.');
    }
    public function enable(User $interviewer)
    {
        // Ensure we are only touching interviewers
        abort_if($interviewer->role !== 'interviewer', 403);

        $interviewer->update(['status' => 'active']);

        return redirect()
            ->back()
            ->with('success', 'Interviewer enabled successfully.');
    }
}
