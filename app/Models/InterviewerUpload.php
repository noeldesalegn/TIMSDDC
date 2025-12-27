<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewerUpload extends Model
{
    protected $fillable = [
        'user_id',
        'taxpayer_id',
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

    public function taxpayer()
    {
        return $this->belongsTo(User::class, 'taxpayer_id');
    }
    // uploader (interviewer)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
