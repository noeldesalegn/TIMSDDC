<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                ];
            });
        
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
                ['amount' => 0, 'date' => now()->toDateString(), 'status' => 'No payments'],
            ]);
        }
        
        return view('taxpayer.dashboard', [
            'taxSummary' => $summary,
            'paymentHistory' => $paymentHistory,
            'recentNews' => $recentNews,
        ]);
    }

    public function summary()
    {
        $user = auth()->user();

        // Fetch or calculate the taxpayer's tax summary
        $taxSummary = TaxSummary::where('taxpayer_id', $user->id)
            ->latest()
            ->first();

        // If not found, create a new default summary (for demo)
        if (!$taxSummary) {
            // Example: Replace with real income data input later
            $annualIncome = 200000; // Example — this could be entered by the taxpayer
            $category = 'Business'; // Business / Employment / Rental

            // Tax rates
            $rates = [
                'Employment' => 0.10,
                'Business'   => 0.15,
                'Rental'     => 0.05,
            ];

            $taxRate = $rates[$category];
            $taxAmount = $annualIncome * $taxRate;

            $taxSummary = TaxSummary::create([
                'taxpayer_id' => $user->id,
                'tax_type' => $category,
                'category' => 'B', // Category A/B/C classification
                'taxable_income' => $annualIncome,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'tax_period' => now()->format('Y-m'),
                'status' => 'pending',
            ]);
        }

        // Now compute the display breakdown
        $breakdown = [
            [
                'category' => 'Employment Income',
                'amount' => 120000,
                'rate' => 0.10,
                'tax' => 120000 * 0.10,
            ],
            [
                'category' => 'Business Income',
                'amount' => 60000,
                'rate' => 0.15,
                'tax' => 60000 * 0.15,
            ],
            [
                'category' => 'Rental Income',
                'amount' => 20000,
                'rate' => 0.05,
                'tax' => 20000 * 0.05,
            ],
        ];

        $total_tax = collect($breakdown)->sum('tax');
        $due_date = Carbon::now()->addDays(30);

        return view('taxpayer.summary', compact('breakdown', 'total_tax', 'due_date'));
    }

    public function paymentForm(Request $request)
    {
        $summary = $this->calculateSummary();
        return view('taxpayer.payment', [
            'amountDue' => $summary['total_tax'],
            'dueDate' => $summary['due_date'],
        ]);
    }

    public function processPayment(Request $request)
    {
        $data = $request->validate([
        'tin' => ['required','string','min:6','max:32'],
        'bank_name' => ['required','string','max:100'],
        'account_number' => ['required','string','max:34'],
        'amount' => ['required','numeric','min:0'],
    ]);

    $user = auth()->user();
    $summary = $this->calculateSummary();

    // Create a payment record
    $payment = Payment::create([
        'user_id' => $user->id,
        'amount' => $data['amount'],
        'status' => 'completed',
        'reference' => strtoupper(Str::random(10)),
        'notes' => 'Paid via ' . $data['bank_name'],
    ]);

    // Update or create tax summary
    TaxSummary::updateOrCreate(
        ['taxpayer_id' => $user->id],
        [
            'tax_type' => 'Annual Tax',
            'tax_amount' => $summary['total_tax'],
            'status' => $data['amount'] >= $summary['total_tax'] ? 'paid' : 'pending',
            'payment_id' => $payment->id,
            'year' => now()->year,
        ]
    );

    // Build receipt data for display
    $receipt = [
        'reference' => $payment->reference,
        'tin' => $data['tin'],
        'bank_name' => $data['bank_name'],
        'account_number' => $data['account_number'],
        'amount' => (float) $data['amount'],
        'paid_at' => $payment->created_at,
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

    // Try to load from DB first
    $existing = \App\Models\TaxSummary::where('taxpayer_id', $user->id)
        ->latest()
        ->first();

    if ($existing) {
        return [
            'breakdown' => [
                ['category' => 'Total Declared Income', 'amount' => $existing->tax_amount / 0.15, 'rate' => 0.15, 'tax' => $existing->tax_amount],
            ],
            'total_tax' => round($existing->tax_amount, 2),
            'due_date' => now()->addDays(30)->toDateString(),
        ];
    }

    // Otherwise compute a default tax estimate
    $income = [
        ['category' => 'Employment', 'amount' => 120000, 'rate' => 0.10],
        ['category' => 'Business', 'amount' => 60000, 'rate' => 0.15],
        ['category' => 'Rental', 'amount' => 24000, 'rate' => 0.05],
    ];

    $breakdown = collect($income)->map(function ($row) {
        $row['tax'] = round($row['amount'] * $row['rate'], 2);
        return $row;
    })->all();

    $total = collect($breakdown)->sum('tax');
    return [
        'breakdown' => $breakdown,
        'total_tax' => round($total, 2),
        'due_date' => now()->addDays(30)->toDateString(),
    ];
}

}
