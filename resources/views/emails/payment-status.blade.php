<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        .status-verified {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .details {
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .details-row:last-child {
            border-bottom: none;
        }
        .details-label {
            color: #64748b;
            font-weight: 500;
        }
        .details-value {
            color: #1e293b;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #64748b;
            font-size: 14px;
        }
        .message {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .message-verified {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
        }
        .message-rejected {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tax Information Management System</h1>
            <p style="color: #64748b; margin-top: 10px;">Payment Status Update</p>
        </div>

        <p>Dear {{ $payment->user->name ?? 'Taxpayer' }},</p>

        <p>We are writing to inform you about an update to your payment status.</p>

        <div style="text-align: center; margin: 25px 0;">
            <span class="status-badge {{ $statusType === 'verified' ? 'status-verified' : 'status-rejected' }}">
                {{ $statusType === 'verified' ? '✓ Payment Verified' : '✗ Payment Rejected' }}
            </span>
        </div>

        <div class="details">
            <div class="details-row">
                <span class="details-label">Payment ID</span>
                <span class="details-value">#{{ $payment->id }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Amount</span>
                <span class="details-value">ETB {{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Payment Method</span>
                <span class="details-value">{{ ucfirst($payment->payment_method ?? 'N/A') }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Status</span>
                <span class="details-value">{{ ucfirst($payment->status) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Verification Status</span>
                <span class="details-value">{{ ucfirst($payment->verification_status) }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Date Processed</span>
                <span class="details-value">{{ $payment->verified_at ? \Carbon\Carbon::parse($payment->verified_at)->format('M d, Y H:i') : 'N/A' }}</span>
            </div>
        </div>

        @if($statusType === 'verified')
            <div class="message message-verified">
                <strong>Congratulations!</strong> Your payment has been successfully verified and processed. 
                You can view your payment history and receipt in your TIMS dashboard.
            </div>
        @else
            <div class="message message-rejected">
                <strong>Important:</strong> Your payment could not be verified. This may be due to incorrect 
                payment details or documentation issues. Please contact our support team or visit your 
                nearest tax office for assistance.
            </div>
        @endif

        <div class="footer">
            <p>If you have any questions, please contact our support team.</p>
            <p><strong>Tax Information Management System (TIMS)</strong></p>
            <p style="font-size: 12px; color: #94a3b8;">
                This is an automated message. Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
