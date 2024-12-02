<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please enter one of your recovery codes to login.') }}
    </div>

    <form method="POST" action="{{ route('2fa.recovery') }}">
        @csrf

        <div>
            <x-input-label for="recovery_code" :value="__('Recovery Code')" />
            <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" required autofocus />
            <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-primary-button>
                {{ __('Recover Access') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('2fa.challenge') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Use an authentication code') }}
            </a>
        </div>
    </form>
</x-guest-layout> 