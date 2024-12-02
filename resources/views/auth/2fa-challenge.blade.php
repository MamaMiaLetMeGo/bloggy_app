<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please enter your authentication code to login.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Authentication Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('2fa.recovery') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Use a recovery code') }}
            </a>
        </div>
    </form>
</x-guest-layout> 