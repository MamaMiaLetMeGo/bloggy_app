<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Two-Factor Authentication') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if (!$enabled)
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Enable 2FA</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Scan the QR code below with your authenticator app and enter the verification code to enable 2FA.
                            </p>
                        </div>

                        <div class="mb-4">
                            {!! $qrCode !!}
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                Secret Key: {{ $secret }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('profile.2fa.enable') }}">
                            @csrf
                            <div>
                                <x-input-label for="code" value="Verification Code" />
                                <x-text-input id="code" type="text" name="code" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-primary-button>Enable 2FA</x-primary-button>
                            </div>
                        </form>
                    @else
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">2FA is Enabled</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Two-factor authentication is currently enabled for your account.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('profile.2fa.disable') }}">
                            @csrf
                            @method('DELETE')
                            <div class="mt-4">
                                <x-danger-button>Disable 2FA</x-danger-button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>