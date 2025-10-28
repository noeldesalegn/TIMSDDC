<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectToRoleBasedDashboard($request->user());
        }
        
        return view('auth.verify-email');
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
