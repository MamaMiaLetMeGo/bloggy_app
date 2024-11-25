@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Author Header -->
            <div class="p-6">
                <div class="flex items-center">
                    <img src="{{ $user->profile_image_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                         alt="{{ $user->name }}" 
                         class="h-16 w-16 rounded-full">
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        @if($user->bio)
                            <p class="mt-1 text-gray-500">{{ $user->bio }}</p>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Published Posts</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['published_posts'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Total Views</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_views'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500">Avg. Reading Time</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ round($stats['avg_reading_time']) }} min</p>
                    </div>
                </div>
            </div>

            <!-- Posts List -->
            @if($posts->count() > 0)
                <div class="border-t border-gray-200">
                    <div class="bg-gray-50 px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium text-gray-900">Published Posts</h2>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($posts as $post)
                            <li class="p-4">
                                <a href="{{ $post->url }}" class="block hover:bg-gray-50">
                                    <div class="flex items-center space-x-4">
                                        @if($post->featured_image)
                                            <img src="{{ Storage::url($post->featured_image) }}" 
                                                 alt="" 
                                                 class="h-16 w-16 object-cover rounded">
                                        @endif
                                        <div>
                                            <p class="text-lg font-semibold text-gray-900">{{ $post->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $post->published_date->format('M d, Y') }}
                                            </p>
                                            @if($post->excerpt)
                                                <p class="mt-1 text-sm text-gray-600">
                                                    {{ $post->excerpt }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No published posts yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection