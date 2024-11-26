@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <!-- Hero Section with Background Image and Gradient Overlay -->
    <div class="relative overflow-hidden bg-white">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-bg.jpg') }}" 
                 alt="Background" 
                 class="w-full h-full object-cover"
            >
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-gray-800/80 animate-gradient mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-blue-950/20"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <p class="mt-6 max-w-md mx-auto text-xl text-gray-200 sm:max-w-3xl">
                    Writing feels good.
                </p>
                @auth
                    <div class="mt-8">
                        <a href="{{ route('admin.posts.create') }}" 
                           class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-blue-600/90 hover:bg-blue-700 transition-colors duration-300"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create New Post
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Featured/Latest Post -->
    @if($posts->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="grid md:grid-cols-2 items-center">
                    @if($posts->first()->featured_image)
                        <div class="h-72 md:h-96">
                            <img src="{{ $posts->first()->featured_image_url }}" 
                                 alt="{{ $posts->first()->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-8 md:p-12">
                        <div class="uppercase tracking-wide text-sm text-blue-600 font-semibold">Latest Post</div>
                        <a href="{{ $posts->first()->url }}" class="block mt-2">
                            <h2 class="text-2xl font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                {{ $posts->first()->title }}
                            </h2>
                        </a>
                        <p class="mt-4 text-gray-500 line-clamp-3">{{ $posts->first()->excerpt }}</p>
                        <div class="mt-6 flex items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ $posts->first()->author->profile_image_url }}" 
                                     alt="{{ $posts->first()->author->name }}" 
                                     class="h-10 w-10 rounded-full">
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $posts->first()->author->name }}</p>
                                <div class="flex space-x-4 text-sm text-gray-500">
                                    <span>{{ $posts->first()->published_date->format('M d, Y') }}</span>
                                    <span>{{ $posts->first()->reading_time }} min read</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Posts Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Recent Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts->skip(1) as $post)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    @if($post->featured_image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ $post->featured_image_url }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    <div class="p-6">
                        @if($post->categories->isNotEmpty())
                            <div class="text-xs text-blue-600 font-semibold tracking-wide uppercase mb-2">
                                {{ $post->categories->first()->name }}
                            </div>
                        @endif
                        <a href="{{ $post->url }}" class="block">
                            <h3 class="text-xl font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-300">
                                {{ $post->title }}
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="{{ $post->author->profile_image_url }}" 
                                     alt="{{ $post->author->name }}" 
                                     class="h-8 w-8 rounded-full">
                                <span class="ml-2 text-sm text-gray-600">{{ $post->author->name }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $post->reading_time }} min read</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .animate-gradient {
        background: linear-gradient(-45deg, 
            rgba(17, 24, 39, 0.8), /* gray-900 */
            rgba(31, 41, 55, 0.8), /* gray-800 */
            rgba(17, 24, 39, 0.8), /* gray-900 */
            rgba(31, 41, 55, 0.8)  /* gray-800 */
        );
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
    }
</style>
@endpush
@endsection
