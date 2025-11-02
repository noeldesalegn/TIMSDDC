<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewerReport extends Model
{
    protected $fillable = [
        'user_id',
        'taxpayer_id',
        'title',
        'category',
        'body',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taxpayer()
    {
        return $this->belongsTo(User::class, 'taxpayer_id');
    }
}
