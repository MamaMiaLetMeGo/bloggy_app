@component('mail::message')
# Login Code

Your login code is: **{{ $code }}**

This code will expire in 1 hour.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 