<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'subject',
        'message',
        'status',
        'response',
        'attachment_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
