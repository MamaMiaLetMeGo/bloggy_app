@component('mail::message')
# New Message from {{ $data['name'] }}

**From:** {{ $data['name'] }} ({{ $data['email'] }})

**Message:**
{{ $data['message'] }}

@component('mail::panel')
You can reply directly to this email to respond to {{ $data['name'] }}.
@endcomponent

@component('mail::button', ['url' => config('app.url')])
Visit Website
@endcomponent

Best regards,<br>
{{ config('app.name') }}

<small style="color: #718096;">This email was sent from your contact form at {{ config('app.url') }}</small>
@endcomponent