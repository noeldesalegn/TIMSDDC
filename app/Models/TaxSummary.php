<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxpayer_id',
        'tax_type',
        'category',
        'taxable_income',
        'tax_rate',
        'deductible',
        'tax_amount',
        'tax_period',
        'status',
    ];
    
    public function taxpayer()
    {
        return $this->belongsTo(User::class, 'taxpayer_id');
    }
}
