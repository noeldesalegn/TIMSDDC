<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return $this->redirectToRoleBasedDashboard($user, '?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectToRoleBasedDashboard($user, '?verified=1');
    }
    
    protected function redirectToRoleBasedDashboard($user, $queryParams = ''): RedirectResponse
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard') . $queryParams;
        } elseif ($user->role === 'taxpayer') {
            return redirect()->route('taxpayer.dashboard') . $queryParams;
        } elseif ($user->role === 'interviewer') {
            return redirect()->route('interviewer.dashboard') . $queryParams;
        } else {
            return redirect()->intended(route('dashboard', absolute: false) . $queryParams);
        }
    }
}
