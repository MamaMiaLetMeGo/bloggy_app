@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center bg-gradient-to-b from-gray-50 to-white px-4 sm:px-6 lg:px-8">
    <div class="text-center">
        <!-- 404 Illustration -->
        <div class="mb-8">
            <svg class="mx-auto h-32 w-32 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 48 48" aria-hidden="true">
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M24 8C15.163 8 8 15.163 8 24s7.163 16 16 16s16-7.163 16-16S32.837 8 24 8z"
                />
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M16 24a2 2 0 104 0 2 2 0 00-4 0zM28 24a2 2 0 104 0 2 2 0 00-4 0z"
                />
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M15 32s2-3 9-3 9 3 9 3"
                />
            </svg>
        </div>

        <!-- Error Message -->
        <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl mb-4">
            Oops! Page not found
        </h1>
        <p class="text-lg text-gray-600 mb-8">
            The page you're looking for doesn't seem to exist.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
                onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('home') }}'; }"
                class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </button>
            <a 
                href="{{ route('home') }}" 
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Return Home
            </a>
        </div>

        <!-- Additional Help Text -->
        <p class="mt-8 text-sm text-gray-500">
            If you believe this is an error, please <a href="#" class="text-blue-600 hover:text-blue-500">contact support</a>.
        </p>
    </div>
</div>

@push('styles')
<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }

    svg {
        animation: float 3s ease-in-out infinite;
    }
</style>
@endpush
@endsection 