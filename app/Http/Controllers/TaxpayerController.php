<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Complaint;

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
        $summary = $this->calculateSummary();
        return view('taxpayer.summary', $summary);
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

        $receipt = [
            'reference' => strtoupper(Str::random(10)),
            'tin' => $data['tin'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'amount' => (float) $data['amount'],
            'paid_at' => now()->toDateTimeString(),
        ];

        // Store last payment in session as a placeholder (no DB persistence yet)
        $request->session()->put('taxpayer_last_payment', $receipt);

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
        // Demo calculation; replace with real data later
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
