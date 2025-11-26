<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\TaxpayerAccount;
use App\Models\TaxSummary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierPaymentController extends Controller
{
    /**
     * Simple JSON dashboard summary for the cashier.
     */
    public function dashboard(Request $request)
    {
        $cashierId = Auth::id();

        $totalProcessedToday = Payment::whereDate('created_at', now()->toDateString())
            ->whereNotNull('processed_by')
            ->sum('amount');

        $myProcessedCount = Payment::where('processed_by', $cashierId)->count();

        $payload = [
            'cashierId' => $cashierId,
            'totalProcessedToday' => (float) $totalProcessedToday,
            'myProcessedCount' => $myProcessedCount,
        ];

        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return view('cashier.dashboard', $payload);
    }

    /**
     * Verify taxpayer identity and outstanding tax using TIN.
     */
    public function verifyTaxpayer(Request $request)
    {
        $tin = trim((string) $request->query('tin', ''));

        if ($tin === '') {
            return response()->json([
                'error' => 'TIN is required',
            ], 422);
        }

        // Prefer explicit TIN on the user record
        $taxpayer = User::where('role', 'taxpayer')
            ->where('tin', $tin)
            ->first();

        // Fallback: resolve by payments table TIN if user.tin is not populated yet
        if (!$taxpayer) {
            $payment = Payment::where('tin', $tin)->latest('created_at')->first();
            if ($payment) {
                $taxpayer = $payment->user;
            }
        }

        if (!$taxpayer) {
            return response()->json([
                'error' => 'Taxpayer not found for the provided TIN',
            ], 404);
        }

        $account = TaxpayerAccount::firstOrCreate(
            ['user_id' => $taxpayer->id],
            [
                'balance' => 0,
            ]
        );

        $summary = TaxSummary::where('taxpayer_id', $taxpayer->id)
            ->latest('created_at')
            ->first();

        $dueAmount = null;
        if ($summary) {
            $totalPaid = Payment::where('user_id', $taxpayer->id)
                ->where('status', 'completed')
                ->sum('amount');
            $dueAmount = max((float) $summary->tax_amount - (float) $totalPaid, 0.0);
        }

        $recentPayments = Payment::with('processedBy')
            ->where('user_id', $taxpayer->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'taxpayer' => [
                'id' => $taxpayer->id,
                'name' => $taxpayer->name,
                'email' => $taxpayer->email,
                'tin' => $taxpayer->tin,
            ],
            'account' => [
                'account_number' => $account->account_number,
                'balance' => (float) $account->balance,
            ],
            'tax_summary' => $summary,
            'due_amount' => $dueAmount,
            'recent_payments' => $recentPayments,
        ]);
    }

    /**
     * Filtered payment history for the cashier.
     */
    public function viewPaymentHistory(Request $request)
    {
        $cashierId = Auth::id();

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = Payment::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by TIN (from payments table or user record)
        $tin = trim((string) $request->query('tin', ''));
        if ($tin !== '') {
            $query->where(function ($q) use ($tin) {
                $q->where('tin', $tin)
                    ->orWhereHas('user', function ($userQuery) use ($tin) {
                        $userQuery->where('tin', $tin);
                    });
            });
        }

        // Filter by status (pending, completed, refunded, etc.)
        $status = (string) $request->query('status', '');
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        // Date range filters
        $from = $request->date('from');
        $to = $request->date('to');
        if ($from) {
            $query->where('created_at', '>=', $from->startOfDay());
        }
        if ($to) {
            $query->where('created_at', '<=', $to->endOfDay());
        }

        // Optional: restrict to payments processed by the logged-in cashier
        $onlyMine = $request->boolean('only_mine');
        if ($onlyMine) {
            $query->where('processed_by', $cashierId);
        }

        $payments = $query->paginate($perPage)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($payments);
        }

        return view('cashier.payments.index', [
            'payments' => $payments,
            'filters' => [
                'tin' => $tin,
                'status' => $status,
                'from' => $from ? $from->toDateString() : null,
                'to' => $to ? $to->toDateString() : null,
            ],
            'onlyMine' => $onlyMine,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Placeholder for form display - actual Blade UI will be implemented in Phase 4.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Cashier payment form UI not implemented yet.',
            ], 501);
        }

        $prefillTin = (string) $request->query('tin', '');

        return view('cashier.payments.create', [
            'prefillTin' => $prefillTin,
        ]);
    }

    /**
     * Process a payment on behalf of a taxpayer.
     */
    public function processPayment(Request $request)
    {
        $data = $request->validate([
            'tin' => ['required', 'string', 'min:6', 'max:32'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:34'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'in:bank_transfer,mobile_banking,card'],
        ]);

        $cashierId = Auth::id();

        // Resolve taxpayer by TIN (user record first, then fallback via payments table)
        $taxpayer = User::where('role', 'taxpayer')
            ->where('tin', $data['tin'])
            ->first();

        if (!$taxpayer) {
            $paymentTin = Payment::where('tin', $data['tin'])->latest('created_at')->first();
            if ($paymentTin) {
                $taxpayer = $paymentTin->user;
            }
        }

        if (!$taxpayer) {
            return response()->json([
                'error' => 'Taxpayer not found for the provided TIN',
            ], 404);
        }

        $summary = TaxSummary::where('taxpayer_id', $taxpayer->id)
            ->latest('created_at')
            ->first();

        $payment = null;

        DB::transaction(function () use (&$payment, $taxpayer, $cashierId, $data, $summary) {
            $payment = Payment::create([
                'user_id' => $taxpayer->id,
                'processed_by' => $cashierId,
                'tin' => $data['tin'],
                'bank_name' => $data['bank_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'status' => 'completed',
                'verification_status' => 'pending',
            ]);

            // Update taxpayer account ledger
            $account = TaxpayerAccount::firstOrCreate(
                ['user_id' => $taxpayer->id],
                ['balance' => 0]
            );
            $account->balance = (float) $account->balance + (float) $data['amount'];
            $account->last_payment_id = $payment->id;
            $account->save();

            // Update tax summary status based on total paid vs tax_amount
            if ($summary) {
                $paidBefore = Payment::where('user_id', $taxpayer->id)
                    ->where('status', 'completed')
                    ->where('id', '!=', $payment->id)
                    ->sum('amount');

                $remainingBefore = max((float) $summary->tax_amount - (float) $paidBefore, 0.0);

                $paidAfter = $paidBefore + (float) $payment->amount;
                $remainingAfter = max((float) $summary->tax_amount - $paidAfter, 0.0);

                $summary->status = $remainingAfter <= 0.0 ? 'paid' : 'pending';
                $summary->save();

                if ($remainingAfter > 0.0) {
                    $this->handleInsufficientPayment($payment, $remainingBefore);
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Payment processed successfully.',
                'payment' => $payment->load(['user', 'processedBy']),
            ], 201);
        }

        return redirect()
            ->route('cashier.payments.receipt', $payment)
            ->with('success', 'Payment processed successfully.');
    }

    /**
     * Generate a JSON representation of a payment receipt.
     */
    public function generateReceipt(Request $request, Payment $payment)
    {
        $payment->load(['user', 'processedBy']);

        $reference = 'PAY-' . str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT);

        $receipt = [
            'id' => $payment->id,
            'reference' => $reference,
            'amount' => (float) $payment->amount,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'tin' => $payment->tin ?? optional($payment->user)->tin,
            'taxpayer' => [
                'id' => optional($payment->user)->id,
                'name' => optional($payment->user)->name,
                'email' => optional($payment->user)->email,
            ],
            'processed_by' => $payment->processedBy ? [
                'id' => $payment->processedBy->id,
                'name' => $payment->processedBy->name,
            ] : null,
            'created_at' => optional($payment->created_at)->toDateTimeString(),
        ];

        if ($request->wantsJson()) {
            return response()->json($receipt);
        }

        return view('cashier.payments.receipt', [
            'payment' => $payment,
            'receipt' => $receipt,
        ]);
    }

    /**
     * Mark a payment as refunded and adjust taxpayer account/tax summary.
     */
    public function processRefunds(Request $request, Payment $payment)
    {
        if ($payment->status !== 'completed') {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Only completed payments can be refunded.',
                ], 422);
            }

            return redirect()->back()->withErrors('Only completed payments can be refunded.');
        }

        $taxpayer = $payment->user;
        $summary = null;
        if ($taxpayer) {
            $summary = TaxSummary::where('taxpayer_id', $taxpayer->id)
                ->latest('created_at')
                ->first();
        }

        DB::transaction(function () use ($payment, $taxpayer, $summary) {
            // Update payment status
            $payment->status = 'refunded';
            $payment->save();

            // Adjust taxpayer account balance
            if ($taxpayer) {
                $account = TaxpayerAccount::where('user_id', $taxpayer->id)->first();
                if ($account) {
                    $account->balance = max((float) $account->balance - (float) $payment->amount, 0.0);
                    $account->last_payment_id = $payment->id;
                    $account->save();
                }
            }

            // Recalculate tax summary status
            if ($summary && $taxpayer) {
                $paid = Payment::where('user_id', $taxpayer->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                $remaining = max((float) $summary->tax_amount - (float) $paid, 0.0);
                $summary->status = $remaining <= 0.0 ? 'paid' : 'pending';
                $summary->save();
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Payment refunded successfully.',
                'payment' => $payment,
            ]);
        }

        return redirect()
            ->route('cashier.payments.index')
            ->with('success', 'Payment refunded successfully.');
    }

    /**
     * Handle an insufficient payment case (underpayment).
     *
     * For now this simply leaves the tax summary in a pending state; this method
     * exists so that additional logging/notifications can be added without
     * changing the main payment flow.
     */
    protected function handleInsufficientPayment(Payment $payment, float $dueAmount): void
    {
        // Hook for future enhancements: log underpayments, notify admin, etc.
        // The core logic already keeps the tax summary in a non-paid state.
    }
}
