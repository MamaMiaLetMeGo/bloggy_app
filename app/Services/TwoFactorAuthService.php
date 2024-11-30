<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthService
{
    private $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecretKey()
    {
        return $this->google2fa->generateSecretKey();
    }

    public function generateQrCode($user, $secretKey)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );

        return $writer->writeString($qrCodeUrl);
    }

    public function verify($secret, $code)
    {
        return $this->google2fa->verifyKey($secret, $code);
    }
} 