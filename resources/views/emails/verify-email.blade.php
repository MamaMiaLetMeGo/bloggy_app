<x-mail::message>
# Verify Your Email Address

Hi {{ $user->name }},

Thanks for signing up! Please verify your email address by clicking the button below.

<x-mail::button :url="$verificationUrl">
Verify Email Address
</x-mail::button>

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}

<small>
If you're having trouble clicking the "Verify Email Address" button, copy and paste this URL into your web browser: {{ $verificationUrl }}
</small>
</x-mail::message>
