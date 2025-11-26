<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'processed_by',
        'tin',
        'bank_name',
        'account_number',
        'amount',
        'payment_method',
        'receipt_path',
        'status',
        'verified_at',
        'verified_by',
        'verification_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
