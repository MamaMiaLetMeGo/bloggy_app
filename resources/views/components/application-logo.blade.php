{{-- Replace the default logo with custom logo image --}}
<img src="{{ asset('images/logo.png') }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }} alt="Site Logo">
