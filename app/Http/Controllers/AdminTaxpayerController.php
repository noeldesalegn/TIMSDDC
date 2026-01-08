<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Complaint;
use App\Models\TaxSummary;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTaxpayerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', ''); // verified|unverified|all
        $perPage = (int) $request->query('per_page', 10);

        $query = User::query()->where('role', 'taxpayer');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        if ($status === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($status === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        $taxpayers = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.taxpayers.index', compact('taxpayers', 'q', 'status', 'perPage'));
    }

    public function show(User $user)
    {
        abort_unless($user->role === 'taxpayer', 404);

        $payments = Payment::where('user_id', $user->id)->latest()->paginate(10);
        $summaries = TaxSummary::where('taxpayer_id', $user->id)->latest()->paginate(10);
        $complaints = Complaint::where('user_id', $user->id)->latest()->paginate(10);

        return view('admin.taxpayers.show', compact('user', 'payments', 'summaries', 'complaints'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'taxpayer', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'verify' => ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if ($request->boolean('verify')) {
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
        }
        $user->save();

        return back()->with('success', 'Taxpayer updated successfully.');
    }
    public function approveTin(User $user)
    {
        $user->update(['tin_status' => 'approved',
            'tin_verified_at' => now(),
            'tin_verified_by' => auth()->id(),]);
        return back()->with('success', 'TIN approved');
    }

    public function rejectTin(User $user)
    {
        $user->update(['tin_status' => 'rejected']);
        return back()->with('success', 'TIN rejected');
    }

    public function bulkVerify(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
            'action' => ['required', 'string', 'in:verify,unverify'],
        ]);

        $ids = $data['ids'];
        if ($data['action'] === 'verify') {
            User::whereIn('id', $ids)->where('role', 'taxpayer')->update(['email_verified_at' => now()]);
        } else {
            User::whereIn('id', $ids)->where('role', 'taxpayer')->update(['email_verified_at' => null]);
        }

        return back()->with('success', 'Bulk action applied.');
    }

    public function export(Request $request): StreamedResponse
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $query = User::query()->where('role', 'taxpayer');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }
        if ($status === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($status === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        $filename = 'taxpayers_export_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Name','Email','Verified','Created At']);
            $query->orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $u) {
                    fputcsv($out, [
                        $u->id,
                        $u->name,
                        $u->email,
                        $u->email_verified_at ? 'yes' : 'no',
                        optional($u->created_at)->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
    public function payments()
    {
        $payments = Payment::with('user')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.taxpayers.payments', compact('payments'));
    }
    public function verifyPayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update([
            'verification_status' => 'verified',
            'status' => 'paid',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Payment verified successfully and marked as paid.');
    }

    public function rejectPayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update([
            'verification_status' => 'rejected',
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('error', 'Payment rejected.');
    }

    public function taxcalc()
    {
        $summaries = TaxSummary::with('taxpayer')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.tax.index', compact('summaries'));
    }
    public function calculateTax(Request $request, TaxSummary $summary)
    {
        $data = $request->validate([
            'taxable_income' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'deductible' => 'nullable|numeric|min:0',
            'tax_period' => 'nullable|string|max:255',
            'category' => 'nullable|in:A,B,C',
        ]);

        $income = (float)$data['taxable_income'];
        $rate = (float)$data['tax_rate'];
        $deductible = (float)($data['deductible'] ?? 0);

        // Calculate tax
        $taxBeforeDeduct = $income * ($rate / 100);
        $taxAmount = max($taxBeforeDeduct - $deductible, 0);

        $summary->update([
            'taxable_income' => $income,
            'tax_rate' => $rate,
            'deductible' => $deductible,
            'tax_amount' => $taxAmount,
            'tax_period' => $data['tax_period'],
            'category' => $data['category'],
            'status' => 'pending', // after calculation
        ]);

        return back()->with('success', 'Tax calculation successful.');
    }
    public function edit(TaxSummary $summary)
    {
        return view('admin.tax.edit', compact('summary'));
    }
    public function createTaxSummary(User $user)
    {
        abort_unless($user->role === 'taxpayer', 404);

        return view('admin.tax.create', compact('user'));
    }
    public function storeTaxSummary(Request $request, User $user)
    {
        abort_unless($user->role === 'taxpayer', 404);

        $data = $request->validate([
            'tax_type' => 'required|in:Employment,Business,Rental',
            'category' => 'nullable|in:A,B,C',
            'tax_period' => 'required|string|max:255',
        ]);

        TaxSummary::create([
            'taxpayer_id' => $user->id,
            'tax_type' => $data['tax_type'],
            'category' => $data['category'],
            'tax_period' => $data['tax_period'],
            'taxable_income' => 0,
            'tax_rate' => 0,
            'deductible' => 0,
            'tax_amount' => 0,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('admin.tax.index')
            ->with('success', 'Tax summary created successfully.');
    }


    public function verify(TaxSummary $summary)
    {
        // Optional: update status field
        $summary->status = 'paid'; // or 'verified' if you add that
        $summary->save();

        return back()->with('success', 'Tax summary marked as verified.');
    }


}
