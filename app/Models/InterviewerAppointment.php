<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewerAppointment extends Model
{
    protected $fillable = [
        'user_id',
        'taxpayer_id',
        'title',
        'notes',
        'start_at',
        'end_at',
        'status',
        'location',
        'contact_phone',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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
