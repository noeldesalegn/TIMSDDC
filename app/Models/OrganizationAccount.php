<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tin',
        'account_number',
        'balance',
    ];
}
