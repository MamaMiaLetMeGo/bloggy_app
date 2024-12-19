<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
        </x-slot>

        <!-- Welcome Message -->
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back!</h2>
            <p class="text-gray-600">Please sign in to your account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
                <x-input id="email" 
                    class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus />
            </div>

            <!-- Password -->
            <div>
                <x-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                <x-input id="password" 
                    class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" 
                        type="checkbox" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                        name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:text-blue-800" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div>
                <x-button class="w-full justify-center bg-blue-600 hover:bg-blue-700">
                    {{ __('Sign in') }}
                </x-button>
            </div>

            <!-- Register Link -->
            <div class="text-center mt-4">
                <span class="text-sm text-gray-600">Don't have an account?</span>
                <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:text-blue-800 ml-1">Register here</a>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
