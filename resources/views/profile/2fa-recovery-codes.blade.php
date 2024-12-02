<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recovery Codes') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recovery Codes</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Store these recovery codes in a secure location. They can be used to recover access to your account if you lose your 2FA device.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        @foreach ($recoveryCodes as $code)
                            <div class="p-2 bg-gray-100 rounded font-mono text-sm">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('profile.2fa.recovery-codes.regenerate') }}" class="mt-4">
                        @csrf
                        <x-danger-button onclick="return confirm('Are you sure you want to regenerate your recovery codes?')">
                            {{ __('Regenerate Recovery Codes') }}
                        </x-danger-button>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('profile.2fa.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            &larr; Back to 2FA Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout> 