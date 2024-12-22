<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
        </x-slot>

        <!-- Welcome Message -->
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Create an Account</h2>
            <p class="text-gray-600">Join our community today</p>
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" class="text-sm font-medium text-gray-700" />
                <x-text-input id="name" 
                    class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
                <x-text-input id="email" 
                    class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                <x-text-input id="password"
                    class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700" />
                <x-text-input id="password_confirmation"
                    class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password_confirmation" 
                    required />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div>
                <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700">
                    {{ __('Register') }}
                </x-primary-button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-4">
                <span class="text-sm text-gray-600">Already have an account?</span>
                <a href="{{ route('login.form') }}" class="text-sm text-blue-600 hover:text-blue-800 ml-1">Sign in here</a>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
