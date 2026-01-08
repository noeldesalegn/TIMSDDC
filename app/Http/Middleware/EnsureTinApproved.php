<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTinApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (
            $user->role === 'taxpayer' &&
            (!$user->tin || $user->tin_status !== 'approved')
        ) {
            return redirect()->route('taxpayer.tin.form');
        }

        return $next($request);
    }
}

