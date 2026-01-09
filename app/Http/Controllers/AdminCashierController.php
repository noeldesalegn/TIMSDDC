<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminCashierController extends Controller
{
    /**
     * Display list of all cashiers with search, filter, and pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'cashier');

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

        // Add payment counts
        $query->withCount([
            'payments as processed_payments_count' => function ($q) {
                $q->whereColumn('processed_by', 'users.id');
            }
        ]);

        // Get total processed amount for each cashier
        $cashiers = $query->latest()->paginate(10)->withQueryString();

        // Get stats
        $stats = [
            'total' => User::where('role', 'cashier')->count(),
            'active' => User::where('role', 'cashier')->where('status', 'active')->count(),
            'inactive' => User::where('role', 'cashier')->where('status', 'inactive')->count(),
        ];

        return view('admin.cashiers.index', compact('cashiers', 'stats'));
    }

    /**
     * Show cashier details with their processed payments.
     */
    public function show(User $cashier)
    {
        abort_if($cashier->role !== 'cashier', 404);

        $payments = Payment::where('processed_by', $cashier->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_processed' => Payment::where('processed_by', $cashier->id)->count(),
            'total_amount' => Payment::where('processed_by', $cashier->id)->sum('amount'),
            'completed' => Payment::where('processed_by', $cashier->id)->where('status', 'completed')->count(),
            'pending' => Payment::where('processed_by', $cashier->id)->where('status', 'pending')->count(),
        ];

        return view('admin.cashiers.show', compact('cashier', 'payments', 'stats'));
    }

    /**
     * Disable a cashier (set status to inactive).
     */
    public function destroy(User $cashier)
    {
        abort_if($cashier->role !== 'cashier', 403);

        $cashier->update(['status' => 'inactive']);

        return redirect()
            ->route('admin.cashiers.index')
            ->with('success', 'Cashier disabled successfully.');
    }

    /**
     * Enable a cashier (set status to active).
     */
    public function enable(User $cashier)
    {
        abort_if($cashier->role !== 'cashier', 403);

        $cashier->update(['status' => 'active']);

        return redirect()
            ->back()
            ->with('success', 'Cashier enabled successfully.');
    }
}
