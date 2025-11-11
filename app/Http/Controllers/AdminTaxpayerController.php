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
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Payment verified successfully.');
    }

    public function rejectPayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update([
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('error', 'Payment rejected.');
    }
}
