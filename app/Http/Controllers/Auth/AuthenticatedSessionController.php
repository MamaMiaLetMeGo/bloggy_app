<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\LoginAttemptService;

class AuthenticatedSessionController extends Controller
{
    protected $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            
            $user = Auth::user();
            
            if ($user->two_factor_enabled) {
                Auth::logout();
                $request->session()->put('2fa:user:id', $user->id);
                return redirect()->route('2fa.challenge');
            }

            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::HOME);

        } catch (\Exception $e) {
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                $needsCode = $this->loginAttemptService->handleFailedAttempt($user);
                
                if ($needsCode) {
                    return redirect()->route('login.code', ['email' => $user->email])
                        ->with('status', 'We\'ve sent you a login code via email.');
                }
            }

            return back()->withErrors([
                'email' => __('auth.failed'),
            ]);
        }
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
