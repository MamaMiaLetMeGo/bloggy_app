<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please enter the verification code sent to your email.') }}
    </div>

    @if (session('message'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.code.verify') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Verification Code -->
        <div>
            <x-input-label for="code" :value="__('Verification Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> 