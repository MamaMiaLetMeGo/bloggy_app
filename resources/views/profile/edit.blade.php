@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ __('Edit Profile') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __("Manage your account settings and preferences.") }}
            </p>
        </div>

        <div class="space-y-6">
            <!-- Profile Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <!-- Back to Profile -->
        <div class="mt-6">
            <a href="{{ route('profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('Back to Profile') }}
            </a>
        </div>
    </div>
</div>
@endsection
