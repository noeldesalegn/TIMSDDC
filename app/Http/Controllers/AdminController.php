<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Complaint;
use App\Models\TaxSummary;

class AdminController extends Controller
{
    public function index()
    {
        // Key statistics
        $totalTaxpayers = User::where('role', 'taxpayer')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $completedPayments = Payment::where('status', 'completed')->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalComplaints = Complaint::count();
        $unresolvedComplaints = Complaint::where('status', 'submitted')->count();

        // Tax summary overview
        $totalTaxDue = TaxSummary::where('status', 'pending')->sum('tax_amount');
        $totalTaxPaid = TaxSummary::where('status', 'paid')->sum('tax_amount');

        // Recent activity
        $recentTaxpayers = User::where('role', 'taxpayer')->latest()->take(5)->get();
        $recentPayments = Payment::with('user')->latest()->take(5)->get();
        $recentComplaintsList = Complaint::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'totalTaxpayers' => $totalTaxpayers,
            'pendingPayments' => $pendingPayments,
            'completedPayments' => $completedPayments,
            'totalRevenue' => $totalRevenue,
            'totalComplaints' => $totalComplaints,
            'unresolvedComplaints' => $unresolvedComplaints,
            'totalTaxDue' => $totalTaxDue,
            'totalTaxPaid' => $totalTaxPaid,
            'recentTaxpayers' => $recentTaxpayers,
            'recentPayments' => $recentPayments,
            'recentComplaintsList' => $recentComplaintsList,
        ]);
    }
}
