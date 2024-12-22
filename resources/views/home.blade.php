@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <!-- Hero Section with Background Image and Gradient Overlay -->
    <div class="relative h-[600px] sm:h-[700px] md:h-[600px] overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-bg.jpeg') }}" 
                 alt="Hero Background" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
        </div>

        <div class="relative h-full container mx-auto px-4">
            <!-- Cards Container -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between h-full">
                <!-- Latest Post Card -->
                <div class="w-full md:w-7/12 lg:w-1/2 pt-8 sm:pt-12 md:pt-0">
                    <div class="bg-white/3 backdrop-blur-md rounded-2xl p-1 shadow-2xl">
                        <div class="bg-white/40 rounded-xl p-6 sm:p-8 pb-8 sm:pb-10">
                            @if($posts->first()->featured_image)
                                <div class="relative h-48 -mt-12 -mx-4 mb-6 rounded-xl overflow-hidden shadow-lg">
                                    <img src="{{ $posts->first()->featured_image_url }}" 
                                         alt="{{ $posts->first()->title }}"
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                </div>
                            @endif
                            @if($posts->first()->categories->isNotEmpty())
                                <div class="flex items-center space-x-2 mb-4">
                                    @foreach($posts->first()->categories as $category)
                                        <a href="{{ route('categories.show', $category) }}" 
                                           class="text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-1 rounded-full transition-colors">
                                            {{ $category->name }}
                                        </a>
                                        @if(!$loop->last)
                                            <span class="text-gray-300">&bull;</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <a href="{{ route('posts.category.show', ['category' => $posts->first()->categories->first()->slug, 'post' => $posts->first()->slug]) }}" 
                               class="block group">
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors">
                                    {{ $posts->first()->title }}
                                </h2>
                            </a>

                            @php
                                $content = strip_tags($posts->first()->body_content);
                                $sentences = array_slice(preg_split('/(?<=[.!?])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY), 0, 3);
                                $preview = implode(' ', $sentences);
                            @endphp
                            <p class="text-gray-700 mb-6 line-clamp-3">{{ $preview }}</p>

                            <!-- Post Meta -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700 gap-4">
                                <div class="flex flex-wrap items-center gap-4">
                                    @if($posts->first()->author->profile_photo_url)
                                        <img src="{{ $posts->first()->author->profile_photo_url }}" 
                                             alt="{{ $posts->first()->author->name }}" 
                                             class="h-8 w-8 rounded-full ring-2 ring-white">
                                    @endif
                                    <span class="font-medium">{{ $posts->first()->author->name }}</span>
                                    <span>{{ $posts->first()->published_date->format('M d, Y') }}</span>
                                    <span>{{ $posts->first()->reading_time }} min read</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                        <span>{{ $posts->first()->likes_count }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 15a2 2 0 0 1-2 2h-2.5a2 2 0 0 0-1.5.67l-1.5 1.67a2 2 0 0 1-3 0l-1.5-1.67A2 2 0 0 0 7.5 17H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z"/>
                                        </svg>
                                        <span>{{ $posts->first()->comments_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Newsletter Card -->
                <div class="w-full md:w-4/12 lg:w-1/3 mt-8 md:mt-0">
                    <div class="bg-white/3 backdrop-blur-md rounded-2xl p-1 shadow-2xl">
                        <div class="bg-white/40 rounded-xl p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Never Miss a Post</h3>
                            <p class="text-gray-700 text-sm mb-4">Join our newsletter and stay updated with the latest content</p>
                            
                            <form id="newsletter-form" method="POST" action="{{ route('newsletter.subscribe') }}" class="space-y-3">
                                @csrf
                                <div class="relative">
                                    <input type="email" 
                                           name="email" 
                                           placeholder="Enter your email address" 
                                           class="w-full pl-4 pr-24 py-3 bg-white/70 border-0 rounded-xl focus:ring-2 focus:ring-blue-500 placeholder-gray-400">
                                    <button type="submit" 
                                            class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-1.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                        Subscribe
                                    </button>
                                </div>
                                <div class="text-red-500 text-sm hidden" id="newsletter-error"></div>
                                <div class="text-green-500 text-sm hidden" id="newsletter-success"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Posts Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Recent Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts->skip(1) as $post)
                <div class="bg-white/3 backdrop-blur-md rounded-2xl p-1 shadow-2xl">
                    <div class="bg-white/40 rounded-xl p-6">
                        @if($post->featured_image)
                            <div class="relative h-48 -mt-12 -mx-4 mb-6 rounded-xl overflow-hidden shadow-lg">
                                <img src="{{ $post->featured_image_url }}" 
                                     alt="{{ $post->title }}"
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                            </div>
                        @endif
                        @if($post->categories->isNotEmpty())
                            <div class="flex items-center space-x-2 mb-4">
                                @foreach($post->categories as $category)
                                    <a href="{{ route('categories.show', $category) }}" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-1 rounded-full transition-colors">
                                        {{ $category->name }}
                                    </a>
                                    @if(!$loop->last)
                                        <span class="text-gray-300">&bull;</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('posts.category.show', ['category' => $post->categories->first()->slug, 'post' => $post->slug]) }}" 
                           class="block group">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors">
                                {{ $post->title }}
                            </h3>
                        </a>

                        @php
                            $content = strip_tags($post->body_content);
                            $sentences = array_slice(preg_split('/(?<=[.!?])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY), 0, 3);
                            $preview = implode(' ', $sentences);
                        @endphp
                        <p class="text-gray-600 mb-6 line-clamp-3">{{ $preview }}</p>

                        <!-- Post Meta -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-500 gap-4">
                            <div class="flex flex-wrap items-center gap-4">
                                @if($post->author->profile_photo_url)
                                    <img src="{{ $post->author->profile_photo_url }}" 
                                         alt="{{ $post->author->name }}" 
                                         class="h-8 w-8 rounded-full ring-2 ring-white">
                                @endif
                                <span class="font-medium">{{ $post->author->name }}</span>
                                <span>{{ $post->published_date->format('M d, Y') }}</span>
                                <span>{{ $post->reading_time }} min read</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span>{{ $post->likes_count }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 15a2 2 0 0 1-2 2h-2.5a2 2 0 0 0-1.5.67l-1.5 1.67a2 2 0 0 1-3 0l-1.5-1.67A2 2 0 0 0 7.5 17H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10z"/>
                                    </svg>
                                    <span>{{ $post->comments_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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

    @push('scripts')
    <script>
        document.getElementById('newsletter-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const errorDiv = document.getElementById('newsletter-error');
            const successDiv = document.getElementById('newsletter-success');
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Reset messages
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            
            try {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Subscribing...';
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin', 
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                if (response.status === 401) {
                    throw new Error('Please refresh the page and try again.');
                }
                
                const data = await response.json();
                
                if (response.ok) {
                    successDiv.textContent = data.message;
                    successDiv.classList.remove('hidden');
                    form.reset();
                } else {
                    const errors = data.errors || { email: [data.message || 'Failed to subscribe. Please try again.'] };
                    throw new Error(errors.email?.[0] || 'Failed to subscribe. Please try again.');
                }
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Subscribe';
            }
        });
    </script>
    @endpush
@endsection
