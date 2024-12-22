<x-mail::message>
# Verify Your Contact Form Submission

Hi {{ $name }},

Please verify your contact form submission with the following information:

**Name:** {{ $name }}
**Email:** {{ $email }}
@if($message)
**Message:** {{ $message }}
@else
**Message:** No message provided
@endif

Click the button below to confirm your submission and send your message to Charlie.

<x-mail::button :url="$verificationUrl">
Verify Contact Form
</x-mail::button>

If you didn't submit this contact form, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}

<small>
This verification link will expire in 1 hour. If you need a new link, please submit the contact form again.
</small>
</x-mail::message>
