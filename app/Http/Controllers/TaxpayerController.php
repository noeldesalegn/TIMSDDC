<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\TaxSummary;
use Carbon\Carbon;

class TaxpayerController extends Controller
{
    public function index()
    {
        $summary = $this->calculateSummary();

        // Get payment history from database
        $user = auth()->user();
        $paymentHistory = $user->payments()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'amount' => $payment->amount,
                    'date' => $payment->created_at->toDateString(),
                    'status' => ucfirst($payment->status),
                    'verification_status' => ucfirst($payment->verification_status ?? 'pending'),
                ];
            });

        // Calculate payment status counts
        $paymentStatusCounts = [
            'total' => $user->payments()->count(),
            'pending' => $user->payments()->where('verification_status', 'pending')->count(),
            'verified' => $user->payments()->where('verification_status', 'verified')->count(),
            'rejected' => $user->payments()->where('verification_status', 'rejected')->count(),
            'completed' => $user->payments()->where('status', 'completed')->count(),
        ];

        // Get recent news from database
        $recentNews = News::orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'excerpt' => substr($news->body, 0, 100) . '...',
                    'date' => $news->created_at->toDateString(),
                ];
            })
            ->toArray();

        // Fallback if no data
        if ($paymentHistory->isEmpty()) {
            $paymentHistory = collect([
                ['amount' => 0, 'date' => now()->toDateString(), 'status' => 'No payments', 'verification_status' => 'N/A'],
            ]);
        }

        return view('taxpayer.dashboard', [
            'taxSummary' => $summary,
            'paymentHistory' => $paymentHistory,
            'paymentStatusCounts' => $paymentStatusCounts,
            'recentNews' => $recentNews,
        ]);
    }

    public function summary()
    {
        $summary = $this->calculateSummary();

        return view('taxpayer.summary', [
            'breakdown' => $summary['breakdown'],
            'total_tax' => $summary['total_tax'],
            'due_date' => $summary['due_date']
        ]);
    }

    public function paymentForm(Request $request)
    {
        $user = auth()->user();
        $summary = $this->calculateSummary();
        return view('taxpayer.payment', [
            'amountDue' => $summary['total_tax'],
            'dueDate' => $summary['due_date'],
            'tin' => $user->tin,
        ]);
    }

    public function processPayment(Request $request)
    {
        $data = $request->validate([
            'tin' => ['required','string','min:6','max:32'],
            'bank_name' => ['required','string','max:100'],
            'account_number' => ['required','string','max:34'],
            'amount' => ['required','numeric','min:0'],
            'payment_method' => ['nullable','string','in:bank_transfer,mobile_banking,card'],
            'receipt_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $user = auth()->user();

        $summary = $this->calculateSummary();

        // Handle optional file upload
        $receiptPath = null;
        if ($request->hasFile('receipt_photo')) {
            $receiptPath = $request->file('receipt_photo')->store('receipts', 'public');
        }

    // Create a payment record
    $reference = strtoupper(Str::random(10));
        $payment = Payment::create([
            'user_id' => $user->id,
            'tin' => $data['tin'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'] ?? 'bank_transfer',
            'receipt_path' => $receiptPath,
            'status' => 'pending',
        ]);

    // Update or create tax summary aligned with current schema
    TaxSummary::updateOrCreate(
        [
            'taxpayer_id' => $user->id,
            'tax_period' => now()->format('Y-m'),
        ],
        [
            'tax_type' => 'Business',
            'tax_amount' => $summary['total_tax'],
            'status' => $data['amount'] >= $summary['total_tax'] ? 'paid' : 'pending',
            'payment_id' => $payment->id,
        ]
    );

    // Build receipt data for display
    $receipt = [
        'reference' => $reference,
        'tin' => $data['tin'],
        'bank_name' => $data['bank_name'],
        'account_number' => $data['account_number'],
        'amount' => $data['amount'],
        'paid_at' => $payment->created_at,
        'receipt_path' => $receiptPath,
    ];

    return redirect()->route('taxpayer.payment')
        ->with('success', 'Payment processed successfully.')
        ->with('payment_receipt', $receipt);
    }

    public function complaints(Request $request)
    {
        $user = auth()->user();
        $complaints = Complaint::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'category' => $complaint->category ?? 'other',
                    'subject' => $complaint->subject,
                    'message' => $complaint->message,
                    'status' => $complaint->status,
                    'response' => $complaint->response,
                    'created_at' => $complaint->created_at,
                ];
            })
            ->toArray();

        return view('taxpayer.complaints', compact('complaints'));
    }

    public function submitComplaint(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'in:technical,calculation,service,payment,other'],
            'subject' => ['required','string','max:150'],
            'message' => ['required','string','max:2000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $complaint = Complaint::create([
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'submitted',
            'category' => $data['category'],
        ]);

        return redirect()->route('taxpayer.complaints')
            ->with('success', 'Complaint submitted successfully. Reference: #' . $complaint->id);
    }

    public function news(Request $request)
    {
        // Get news from database
        $news = News::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($newsItem) {
                return [
                    'id' => $newsItem->id,
                    'title' => $newsItem->title,
                    'body' => $newsItem->body,
                    'date' => $newsItem->created_at->toDateString(),
                ];
            })
            ->toArray();

        // Get comments from session (will be moved to database later)
        $comments = $request->session()->get('taxpayer_comments', []);

        return view('taxpayer.news', compact('news', 'comments'));
    }

    public function submitComment(Request $request, int $newsId)
    {
        $data = $request->validate([
            'comment' => ['required','string','max:1000'],
        ]);

        $comments = $request->session()->get('taxpayer_comments', []);
        $comments[$newsId] = $comments[$newsId] ?? [];
        $comments[$newsId][] = [
            'author' => auth()->user()->name,
            'message' => $data['comment'],
            'created_at' => now()->toDateTimeString(),
        ];
        $request->session()->put('taxpayer_comments', $comments);

        return redirect()->route('taxpayer.news')->with('success', 'Comment posted.');
    }

    protected function calculateSummary(): array
    {
        $user = auth()->user();

        // Get all pending tax summaries
        $summaries = TaxSummary::where('taxpayer_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $breakdown = $summaries->map(function ($summary) {
            // Determine if rate is decimal (0.15) or percentage (15)
            $rate = $summary->tax_rate;
            if ($rate > 1) {
                $rate = $rate / 100;
            }

            return [
                'category' => $summary->tax_type . ($summary->category ? ' (' . $summary->category . ')' : ''),
                'amount' => $summary->taxable_income,
                'rate' => $rate,
                'tax' => $summary->tax_amount,
            ];
        })->toArray();

        $total = collect($breakdown)->sum('tax');

        return [
            'breakdown' => $breakdown,
            'total_tax' => round($total, 2),
            'due_date' => now()->addDays(30)->toDateString(),
        ];
    }
    public function tinForm()
    {
        $user = auth()->user();

        // Redirect if already approved
        if ($user->tin_status === 'approved') {
            return redirect()->route('taxpayer.dashboard');
        }

        return view('taxpayer.tin', compact('user'));
    }

    public function tinSubmit(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'tin' => ['required', 'string', 'max:50', 'unique:users,tin,' . $user->id],
            'tin_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        // Delete old document if exists
        if ($user->tin_document) {
            Storage::disk('public')->delete($user->tin_document);
        }

        $path = $request->file('tin_document')
            ->store('tin_documents', 'public');

        $user->update([
            'tin' => $data['tin'],
            'tin_document' => $path,
            'tin_status' => 'pending',
            'tin_verified_at' => null,
            'tin_verified_by' => null,
            'tin_rejection_reason' => null,
        ]);

        return redirect()
            ->route('taxpayer.tin.form')
            ->with('success', 'TIN submitted successfully. Awaiting admin approval.');
    }
}
