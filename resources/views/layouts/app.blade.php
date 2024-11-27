<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Charles Gendron') }}</title>

        <!-- Production Assets -->
        @production
            <link rel="stylesheet" href="{{ asset('build/assets/app-2juYq1Hy.css') }}">
            <script src="{{ asset('build/assets/app-BjCBnTiP.js') }}" defer></script>
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endproduction

        <!-- Add Trix CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
            @include('layouts.footer')
        </div>

        <!-- Add Trix JS -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>

        @stack('scripts')

        @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <div x-show="open" 
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50">
                    @forelse(auth()->user()->notifications as $notification)
                        <a href="{{ url("/posts/{$notification->data['post_id']}#comment-{$notification->data['comment_id']}") }}"
                           class="block px-4 py-2 hover:bg-gray-100 {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900 font-semibold' }}">
                            <p class="text-sm">
                                {{ $notification->data['commenter_name'] }} mentioned you in a comment on "{{ $notification->data['post_title'] }}"
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </a>
                    @empty
                        <div class="px-4 py-2 text-gray-500 text-sm">
                            No notifications
                        </div>
                    @endforelse
                </div>
            </div>
        @endauth
    </body>
</html>
