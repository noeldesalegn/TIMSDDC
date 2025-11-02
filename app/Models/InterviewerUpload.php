<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewerUpload extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'path',
        'mime',
        'size',
        'status',
        'meta',
    ];

    protected $casts = [
        'size' => 'integer',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
