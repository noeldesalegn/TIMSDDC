<?php

namespace App\Http\Controllers;


use App\Models\InterviewerAppointment;
use App\Models\InterviewerUpload;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InterviewerController extends Controller
{
    public function index( Request $request)
    {
        $user = $request->user();

        $filesUploadedToday = InterviewerUpload::where('user_id', $user->id)
            ->where('status', '!=', 'deleted')
            ->whereDate('created_at', Carbon::today())
            ->count();        $pendingUploads = request()->session()->get('pending_uploads', 0);

        // Get today's schedule
//        $todaySchedule = request()->session()->get('today_schedule', [
//            ['time' => '09:00', 'taxpayer' => 'John Doe', 'status' => 'Confirmed'],
//            ['time' => '10:30', 'taxpayer' => 'Jane Smith', 'status' => 'Pending'],
//            ['time' => '14:00', 'taxpayer' => 'Mike Johnson', 'status' => 'Confirmed'],
//        ]);

        $today = Carbon::today();
        $todaySchedule = InterviewerAppointment::whereDate('start_at', $today)
            ->where('interviewer_id', $user->id)
            ->with('taxpayer')
            ->orderBy('start_at')
            ->get()
            ->map(function ($appointment) {
                return [
                    'title' => $appointment->title,
                    'taxpayer' => $appointment->taxpayer->name ?? 'Unknown',
                    'time' => Carbon::parse($appointment->start_at)->format('g:i A'),
                    'status' => ucfirst($appointment->status),
                ];
            });

        // Get this month's schedule
        $monthSchedule = InterviewerAppointment::whereBetween('start_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->where('interviewer_id', $user->id)
            ->with('taxpayer')
            ->orderBy('start_at')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->title,
                    'taxpayer' => $appointment->taxpayer->name ?? 'Unknown',
                    'date' => Carbon::parse($appointment->start_at)->format('M d, Y'),
                    'time' => Carbon::parse($appointment->start_at)->format('g:i A'),
                    'status' => ucfirst($appointment->status),
                ];
            });

        // Get recent uploads
        $recentUploads = request()->session()->get('recent_uploads', [
            ['filename' => 'income_statement_001.pdf', 'date' => now()->toDateString(), 'status' => 'Processed'],
            ['filename' => 'income_statement_002.pdf', 'date' => now()->subDays(1)->toDateString(), 'status' => 'Pending'],
            ['filename' => 'income_statement_003.pdf', 'date' => now()->subDays(2)->toDateString(), 'status' => 'Processed'],
        ]);

        $taxpayers = User::where('role', 'taxpayer')->paginate(10);

        return view('interviewer.dashboard', [
            'filesUploadedToday' => $filesUploadedToday,
            'pendingUploads' => $pendingUploads,
            'todaySchedule' => $todaySchedule,
            'recentUploads' => $recentUploads,
            'taxpayers' => $taxpayers,
            'monthSchedule' => $monthSchedule,
        ]);
    }
    public function taxpayer(User $user)
    {
        // Ensure the user is a taxpayer
        if ($user->role !== 'taxpayer') {
            abort(404);
        }

        // Fetch taxpayer details, interviews, and uploads
        $uploads = $user->uploads()->orderBy('created_at', 'desc')->get();

        return view('interviewer.taxpayer.show', [
            'taxpayer' => $user,
            'uploads' => $uploads,
        ]);
    }
}
