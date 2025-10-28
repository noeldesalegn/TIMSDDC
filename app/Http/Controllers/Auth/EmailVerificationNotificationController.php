<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return $this->redirectToRoleBasedDashboard($user);
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
    
    protected function redirectToRoleBasedDashboard($user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'taxpayer') {
            return redirect()->route('taxpayer.dashboard');
        } elseif ($user->role === 'interviewer') {
            return redirect()->route('interviewer.dashboard');
        } else {
            return redirect()->intended(route('dashboard', absolute: false));
        }
    }
}
