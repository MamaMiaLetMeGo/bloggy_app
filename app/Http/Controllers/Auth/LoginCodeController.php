<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginAttemptService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginCodeController extends Controller
{
    protected $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    public function show(Request $request)
    {
        if (!$request->email) {
            return redirect()->route('login');
        }

        return view('auth.login-code', [
            'email' => $request->email
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($this->loginAttemptService->verifyLoginCode($user, $request->code)) {
            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'code' => 'The provided code is invalid or has expired.',
        ]);
    }
}
