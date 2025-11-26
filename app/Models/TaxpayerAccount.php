<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxpayerAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
        'last_payment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lastPayment()
    {
        return $this->belongsTo(Payment::class, 'last_payment_id');
    }
}
