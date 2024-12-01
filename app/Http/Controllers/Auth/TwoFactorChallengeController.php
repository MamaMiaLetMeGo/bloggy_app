<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Notifications\SecurityAlert;

class TwoFactorChallengeController extends Controller
{
    public function create()
    {
        return view('auth.2fa-challenge');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            session(['2fa.confirmed' => true]);
            
            // Notify about successful 2FA verification
            $user->notify(new SecurityAlert(
                'Successful 2FA Login',
                'Your account was successfully accessed using two-factor authentication.',
                'Review Activity',
                route('profile.security'),
                'info'
            ));

            return redirect()->intended();
        }

        return back()->withErrors(['code' => 'The provided code was invalid.']);
    }

    public function showRecoveryForm()
    {
        return view('auth.2fa-recovery');
    }

    public function recovery(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = auth()->user();
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes));

        if (in_array($request->recovery_code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [$request->recovery_code]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();

            session(['2fa.confirmed' => true]);

            // Notify about recovery code usage
            $user->notify(new SecurityAlert(
                'Recovery Code Used',
                'A recovery code was used to access your account. Please generate new recovery codes.',
                'Generate New Codes',
                route('profile.2fa.recovery-codes'),
                'warning'
            ));

            return redirect()->intended();
        }

        return back()->withErrors(['recovery_code' => 'The provided recovery code was invalid.']);
    }
}
