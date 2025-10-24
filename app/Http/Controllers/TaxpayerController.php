<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxpayerController extends Controller
{
    public function index()
    {
        return view('taxpayer.dashboard');
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
        $complaints = $request->session()->get('taxpayer_complaints', []);
        return view('taxpayer.complaints', compact('complaints'));
    }

    public function submitComplaint(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required','string','max:150'],
            'message' => ['required','string','max:2000'],
        ]);

        $complaints = $request->session()->get('taxpayer_complaints', []);
        $complaints[] = [
            'id' => count($complaints) + 1,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'Submitted',
            'response' => null,
            'created_at' => now()->toDateTimeString(),
        ];
        $request->session()->put('taxpayer_complaints', $complaints);

        return redirect()->route('taxpayer.complaints')->with('success', 'Complaint submitted successfully.');
    }

    public function news(Request $request)
    {
        // Demo news items; replace with DB-backed content later
        $news = [
            [ 'id' => 1, 'title' => 'Tax Filing Deadline Reminder', 'body' => 'Remember to file your annual taxes by the end of next month.', 'date' => now()->subDays(2)->toDateString() ],
            [ 'id' => 2, 'title' => 'New Payment Channels', 'body' => 'We have added new bank partners for easier payments.', 'date' => now()->subWeek()->toDateString() ],
        ];
        $comments = $request->session()->get('taxpayer_comments', []); // [newsId => [comments...]]
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
