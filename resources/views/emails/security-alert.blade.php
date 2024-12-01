<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Security Alert</title>
    <style>
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }
            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 16px !important;
            }
            .container {
                padding: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; width: 100%; background-color: #f6f6f6;">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
            <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;">
                <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">
                    <!-- Security Icon -->
                    <div style="text-align: center; margin-bottom: 20px;">
                        🔒
                    </div>

                    <!-- Alert Header -->
                    <table role="presentation" style="border-collapse: separate; width: 100%; background: #ffffff; border-radius: 3px;">
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                                <h1 style="color: #2d3748; font-size: 24px; font-weight: bold; margin: 0 0 20px;">{{ $title }}</h1>
                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0 0 15px;">{{ $message }}</p>
                                
                                @if($actionText && $actionUrl)
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; width: 100%; box-sizing: border-box;">
                                    <tbody>
                                        <tr>
                                            <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                                <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; color: #ffffff; background-color: #3869d4; border: solid 1px #3869d4; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize;">{{ $actionText }}</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endif

                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 20px 0 0;">If you did not initiate this action, please secure your account immediately.</p>
                                
                                <hr style="border: 0; border-bottom: 1px solid #f6f6f6; margin: 20px 0;">
                                
                                <p style="font-family: sans-serif; font-size: 12px; color: #666;">This is an automated security alert from {{ config('app.name') }}. Please do not reply to this email.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        </tr>
    </table>
</body>
</html> 