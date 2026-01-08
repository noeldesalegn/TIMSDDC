<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // TIN fields
            'tin' => ['nullable', 'string', 'max:50', 'unique:users,tin'],
            'tin_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'tin_status' => 'pending',
        ]);

        // Upload TIN document
        $tinPath = null;
        if ($request->hasFile('tin_document')) {
            $tinPath = $request->file('tin_document')
                ->store('tin_documents', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'taxpayer',

            'tin' => $request->tin,
            'tin_document' => $tinPath,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('taxpayer.dashboard');
    }
}
