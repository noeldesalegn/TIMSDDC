<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterviewerController extends Controller
{
    public function index()
    {
        // Get upload stats from session
        $filesUploadedToday = request()->session()->get('files_uploaded_today', 0);
        $pendingUploads = request()->session()->get('pending_uploads', 0);
        
        // Get today's schedule
        $todaySchedule = request()->session()->get('today_schedule', [
            ['time' => '09:00', 'taxpayer' => 'John Doe', 'status' => 'Confirmed'],
            ['time' => '10:30', 'taxpayer' => 'Jane Smith', 'status' => 'Pending'],
            ['time' => '14:00', 'taxpayer' => 'Mike Johnson', 'status' => 'Confirmed'],
        ]);
        
        // Get recent uploads
        $recentUploads = request()->session()->get('recent_uploads', [
            ['filename' => 'income_statement_001.pdf', 'date' => now()->toDateString(), 'status' => 'Processed'],
            ['filename' => 'income_statement_002.pdf', 'date' => now()->subDays(1)->toDateString(), 'status' => 'Pending'],
            ['filename' => 'income_statement_003.pdf', 'date' => now()->subDays(2)->toDateString(), 'status' => 'Processed'],
        ]);
        
        return view('interviewer.dashboard', [
            'filesUploadedToday' => $filesUploadedToday,
            'pendingUploads' => $pendingUploads,
            'todaySchedule' => $todaySchedule,
            'recentUploads' => $recentUploads,
        ]);
    }
}
