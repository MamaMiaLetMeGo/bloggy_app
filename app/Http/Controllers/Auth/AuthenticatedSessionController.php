<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\LoginAttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\LoginVerificationCode;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private LoginAttemptService $loginAttemptService
    ) {}

    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
        } catch (\Exception $e) {
            $this->loginAttemptService->recordFailedAttempt($request->ip());
            
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                $needsCode = $this->loginAttemptService->handleFailedAttempt($user);
                
                if ($needsCode) {
                    $code = Str::random(6);
                    session(['verification_code' => $code, 'verification_email' => $user->email]);
                    
                    try {
                        Mail::to($user)->send(new LoginVerificationCode($code));
                        \Log::info('Verification code email sent', ['email' => $user->email]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send verification code email', [
                            'email' => $user->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    return redirect()->route('login.code', ['email' => $user->email])
                        ->with('message', 'Please check your email for a verification code.');
                }
            }

            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->withInput($request->except('password'));
        }
    }

    public function showCodeForm(Request $request)
    {
        if (!session('verification_code')) {
            return redirect()->route('login');
        }

        return view('auth.verify-code', [
            'email' => $request->email
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'email' => 'required|email'
        ]);

        if (
            $request->code === session('verification_code') &&
            $request->email === session('verification_email')
        ) {
            session()->forget(['verification_code', 'verification_email']);
            
            // Get the user
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                // Clear failed attempts
                $this->loginAttemptService->clearFailedAttempts($user);
                
                // Log the user in
                Auth::login($user);
                
                // Regenerate session
                $request->session()->regenerate();
                
                \Log::info('User logged in after verification', ['email' => $user->email]);
                
                return redirect()->intended(RouteServiceProvider::HOME)
                    ->with('message', 'Login successful!');
            }
        }

        return back()->withErrors([
            'code' => 'The verification code is invalid.'
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
