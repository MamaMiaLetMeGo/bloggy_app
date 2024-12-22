<x-mail::message>
# New Message from {{ $data['name'] }}

**From:** {{ $data['name'] }} ({{ $data['email'] }})

**Message:**
{{ $data['message'] }}

<x-mail::panel>
You can reply directly to this email to respond to {{ $data['name'] }}.
</x-mail::panel>

<x-mail::button :url="config('app.url')">
Visit Website
</x-mail::button>

Best regards,<br>
{{ config('app.name') }}

<small style="color: #718096;">This email was sent from your contact form at {{ config('app.url') }}</small>
</x-mail::message>