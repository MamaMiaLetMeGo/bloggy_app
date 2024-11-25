@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                    Welcome to Our Blog
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Discover insightful articles, stories, and updates from our community.
                </p>
                @auth
                    <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                        <div class="rounded-md shadow">
                            <a href="{{ route('admin.posts.create') }}" 
                            class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10"
                            >
                                Create New Post
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Latest Posts Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest Posts</h2>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @forelse($posts as $post)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    @if($post->featured_image)
                        <img src="{{ $post->featured_image_url }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <a href="{{ $post->url }}" class="hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                        </h3>
                        <div class="text-sm text-gray-500 mb-4 flex items-center justify-between">
                            <div class="flex items-center">
                                @if($post->author->profile_image)
                                    <img src="{{ $post->author->profile_image_url }}" 
                                         alt="{{ $post->author->name }}" 
                                         class="w-6 h-6 rounded-full mr-2">
                                @endif
                                <span>{{ $post->author->name }}</span>
                            </div>
                            <span>{{ $post->reading_time }} min read</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $post->published_date->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500 text-lg">No posts available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection