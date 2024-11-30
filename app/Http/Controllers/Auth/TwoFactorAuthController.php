<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TwoFactorAuthController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function setup()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_secret) {
            $secretKey = $this->twoFactorService->generateSecretKey();
            $qrCodeSvg = $this->twoFactorService->generateQrCode($user, $secretKey);
            
            session(['2fa_secret' => $secretKey]);
            
            return view('auth.2fa.setup', compact('qrCodeSvg', 'secretKey'));
        }
        
        return redirect()->route('profile.show')
            ->with('error', '2FA is already set up.');
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $secretKey = session('2fa_secret');
        
        if (!$secretKey) {
            return back()->with('error', 'Invalid session.');
        }

        if ($this->twoFactorService->verify($secretKey, $request->code)) {
            $user = Auth::user();
            $user->update([
                'two_factor_secret' => $secretKey,
                'two_factor_enabled' => true,
                'two_factor_confirmed_at' => now(),
            ]);
            
            session()->forget('2fa_secret');
            
            return redirect()->route('profile.show')
                ->with('status', '2FA has been enabled.');
        }

        return back()->with('error', 'Invalid verification code.');
    }

    public function challenge()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa:user:id');
        $user = User::findOrFail($userId);

        if ($this->twoFactorService->verify($user->two_factor_secret, $request->code)) {
            Auth::login($user);
            session()->forget('2fa:user:id');
            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Invalid verification code.');
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return redirect()->route('profile.show')
            ->with('status', '2FA has been disabled.');
    }
}
