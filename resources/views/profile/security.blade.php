<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Security Settings') }}
        </h2>
    </x-slot>

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Two-Factor Authentication Status -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Two-Factor Authentication</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            @if(auth()->user()->two_factor_enabled)
                                Two-factor authentication is currently enabled.
                                <a href="{{ route('profile.2fa.show') }}" class="text-indigo-600 hover:text-indigo-900">
                                    Manage 2FA settings
                                </a>
                            @else
                                Two-factor authentication is not enabled.
                                <a href="{{ route('profile.2fa.show') }}" class="text-indigo-600 hover:text-indigo-900">
                                    Enable 2FA
                                </a>
                            @endif
                        </p>
                    </div>

                    <!-- Recent Security Activity -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Recent Security Activity</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Here you can view recent security-related activities on your account.
                        </p>
                        <!-- Add security activity log here -->
                    </div>

                    <!-- Active Sessions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Active Sessions</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Manage and logout from your active sessions on other browsers and devices.
                        </p>
                        <!-- Add active sessions management here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout> 