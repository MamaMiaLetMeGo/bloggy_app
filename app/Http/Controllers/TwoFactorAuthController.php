<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TwoFactorAuthController extends Controller
{
    private $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show()
    {
        $user = auth()->user();
        
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('profile.2fa', [
            'qrCode' => $qrCodeSvg,
            'secret' => $user->two_factor_secret,
            'enabled' => $user->two_factor_enabled,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        $valid = $this->google2fa->verifyKey(
            $user->two_factor_secret,
            $request->code
        );

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->two_factor_confirmed_at = now();
            $user->two_factor_recovery_codes = encrypt(json_encode(
                collect(range(1, 8))->map(fn () => Str::random(10))->all()
            ));
            $user->save();

            return back()->with('status', '2FA has been enabled.');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $user->two_factor_enabled = false;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return back()->with('status', '2FA has been disabled.');
    }

    public function showRecoveryCodes()
    {
        $user = auth()->user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('profile.2fa');
        }

        return view('profile.2fa-recovery-codes', [
            'recoveryCodes' => json_decode(decrypt($user->two_factor_recovery_codes)),
        ]);
    }

    public function regenerateRecoveryCodes()
    {
        $user = auth()->user();
        
        $user->two_factor_recovery_codes = encrypt(json_encode(
            collect(range(1, 8))->map(fn () => Str::random(10))->all()
        ));
        $user->save();

        return back()->with('status', 'Recovery codes regenerated.');
    }
}
