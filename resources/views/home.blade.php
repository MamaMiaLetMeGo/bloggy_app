@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <!-- Hero Section with Background Image and Gradient Overlay -->
    <div class="relative overflow-hidden bg-white h-[70vh]">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-bg.jpeg') }}" 
                 alt="Background" 
                 class="w-full h-full object-cover object-center"
            >
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-gray-800/80 animate-gradient mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-blue-950/20"></div>
        </div>

        <!-- Content -->
        <div class="relative h-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
                <!-- Latest Post Card -->
                @if($posts->isNotEmpty())
                <div class="w-full lg:w-1/2 xl:w-2/5 relative z-10">
                    <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl p-6 transform hover:scale-[1.02] transition-all duration-300">
                        <div class="flex flex-col h-full">
                            @if($posts->first()->featured_image)
                                <div class="relative h-48 md:h-56 rounded-lg overflow-hidden mb-4">
                                    <img src="{{ $posts->first()->featured_image_url }}" 
                                         alt="{{ $posts->first()->title }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endif
                            @if($posts->first()->categories->isNotEmpty())
                                <div class="flex items-center space-x-2 mb-2">
                                    @foreach($posts->first()->categories as $category)
                                        <a href="{{ route('categories.show', $category) }}" 
                                           class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                            {{ $category->name }}
                                        </a>
                                        @if(!$loop->last)
                                            <span class="text-gray-400">&bull;</span>
                                        @endif
                                    @endforeach
                                </div>
                                <a href="{{ route('posts.show', ['category' => $posts->first()->categories->first()->slug, 'post' => $posts->first()->slug]) }}" 
                                   class="block">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors">
                                        {{ $posts->first()->title }}
                                    </h2>
                                </a>
                            @else
                                <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $posts->first()->title }}</h2>
                            @endif
                            @php
                                $content = strip_tags($posts->first()->body_content);
                                $sentences = array_slice(preg_split('/(?<=[.!?])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY), 0, 3);
                                $preview = implode(' ', $sentences);
                            @endphp
                            <p class="text-gray-600 mb-4">
                                {{ $preview }}
                                @if($posts->first()->categories->isNotEmpty())
                                    <a href="{{ route('posts.show', ['category' => $posts->first()->categories->first()->slug, 'post' => $posts->first()->slug]) }}" 
                                       class="inline-flex items-center ml-1 text-blue-600 hover:text-blue-700 font-medium">
                                        Read more
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </p>
                            <div class="mt-auto">
                                <!-- Post Meta -->
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                                    <div class="flex items-center">
                                        @if($posts->first()->author->profile_photo_url)
                                            <img src="{{ $posts->first()->author->profile_photo_url }}" 
                                                 alt="{{ $posts->first()->author->name }}" 
                                                 class="h-6 w-6 rounded-full mr-2">
                                        @endif
                                        <span>{{ $posts->first()->author->name }}</span>
                                    </div>
                                    <span>{{ $posts->first()->published_date->format('M d, Y') }}</span>
                                    <span>{{ $posts->first()->reading_time }} min read</span>
                                </div>
                                <!-- Engagement Stats -->
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        <span>{{ $posts->first()->likes_count }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span>{{ $posts->first()->comments_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Hero Text -->
                <div class="text-center lg:text-right lg:ml-auto">
                    <p class="max-w-md mx-auto lg:ml-auto lg:mr-0 text-xl text-gray-200 sm:max-w-xl">
                        Writing feels good.
                    </p>
                    @if (Auth::check() && Auth::user()->isAdmin())
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Posts Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Recent Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts->skip(1) as $post)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    @if($post->featured_image)
                        <div class="h-48">
                            <img src="{{ $post->featured_image_url }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-6">
                        @if($post->categories->isNotEmpty())
                            <div class="flex items-center space-x-2 mb-2">
                                @foreach($post->categories as $category)
                                    <a href="{{ route('categories.show', $category) }}" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                        {{ $category->name }}
                                    </a>
                                    @if(!$loop->last)
                                        <span class="text-gray-400">&bull;</span>
                                    @endif
                                @endforeach
                            </div>
                            <a href="{{ route('posts.show', ['category' => $post->categories->first()->slug, 'post' => $post->slug]) }}" 
                               class="block">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                                    {{ $post->title }}
                                </h3>
                            </a>
                        @else
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $post->title }}</h3>
                        @endif
                        @php
                            $content = strip_tags($post->body_content);
                            $sentences = array_slice(preg_split('/(?<=[.!?])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY), 0, 3);
                            $preview = implode(' ', $sentences);
                        @endphp
                        <p class="text-gray-600 mb-4">
                            {{ $preview }}
                            @if($post->categories->isNotEmpty())
                                <a href="{{ route('posts.show', ['category' => $post->categories->first()->slug, 'post' => $post->slug]) }}" 
                                   class="inline-flex items-center ml-1 text-blue-600 hover:text-blue-700 font-medium">
                                    Read more
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        </p>
                        <!-- Post Meta -->
                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                @if($post->author->profile_photo_url)
                                    <img src="{{ $post->author->profile_photo_url }}" 
                                         alt="{{ $post->author->name }}" 
                                         class="h-6 w-6 rounded-full mr-2">
                                @endif
                                <span>{{ $post->author->name }}</span>
                            </div>
                            <span>{{ $post->published_date->format('M d, Y') }}</span>
                            <span>{{ $post->reading_time }} min read</span>
                        </div>
                        <!-- Engagement Stats -->
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $post->likes_count }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <span>{{ $post->comments_count }}</span>
                            </div>
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
