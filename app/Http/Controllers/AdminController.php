<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Complaint;

class AdminController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalTaxpayers = User::where('role', 'taxpayer')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $recentComplaintsCount = Complaint::orderBy('created_at', 'desc')->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount') ?? 0;
        
        // Get recent activity
        $recentTaxpayers = User::where('role', 'taxpayer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentPayments = Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentComplaintsList = Complaint::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', [
            'totalTaxpayers' => $totalTaxpayers,
            'pendingPayments' => $pendingPayments,
            'recentComplaints' => $recentComplaintsCount,
            'totalRevenue' => $totalRevenue,
            'recentTaxpayers' => $recentTaxpayers,
            'recentPayments' => $recentPayments,
            'recentComplaintsList' => $recentComplaintsList,
        ]);
    }
}
