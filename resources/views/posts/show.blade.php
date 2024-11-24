@extends('layouts.app')

@section('meta')
<meta property="og:title" content="{{ $post->title }}" />
<meta property="og:description" content="{{ $post->excerpt }}" />
@if($post->featured_image)
    <meta property="og:image" content="{{ $post->featured_image_url }}" />
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <article class="bg-white rounded-lg shadow-lg overflow-hidden tinymce-content">
                @auth
                    @can('update', $post)
                        <div class="absolute top-4 right-4 space-x-2">
                            <a 
                                href="{{ route('admin.posts.edit', $post) }}" 
                                class="inline-flex items-center px-4 py-2 bg-white bg-opacity-90 rounded-md text-sm font-medium text-gray-700 hover:bg-opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Edit Post
                            </a>
                        </div>
                    @endcan
                @endauth
        <div class="p-8">
            {{-- Breadcrumb and Category Info --}}
            @if($post->breadcrumb || $post->categories->isNotEmpty())
                <div class="mb-8">
                    {{-- Breadcrumb --}}
                    <nav class="text-sm text-gray-500 mb-4">
                        <a href="{{ route('posts.index') }}" class="hover:text-gray-700">Posts</a>
                        <span class="mx-2">/</span>
                        @if($post->categories->isNotEmpty())
                            <a href="{{ route('posts.category', $post->categories->first()) }}" class="hover:text-gray-700">
                                {{ $post->categories->first()->name }}
                            </a>
                            <span class="mx-2">/</span>
                        @endif
                        <span>{{ $post->breadcrumb }}</span>
                    </nav>
                </div>
            @endif

            {{-- Title and Meta --}}
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
            
            <div class="flex items-center space-x-4 mb-8">
                <a href="{{ $post->author->author_url }}" class="flex items-center text-gray-700 hover:text-blue-600">
                    <img 
                        src="{{ $post->author->profile_image_url }}" 
                        alt="{{ $post->author->name }}" 
                        class="w-10 h-10 rounded-full mr-3"
                    >
                    <div>
                        <div class="font-medium">{{ $post->author->name }}</div>
                        @if($post->published_date)
                            <div class="text-sm text-gray-500">
                                Published on {{ $post->published_date->format('F j, Y') }}
                            </div>
                        @endif
                    </div>
                </a>
                <div class="text-gray-500 text-sm">
                    {{ $post->reading_time }} min read
                </div>
            </div>

            {{-- Video --}}
            @if($post->video_url)
                <div class="mb-8 rounded-lg overflow-hidden">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe 
                            src="{{ $post->video_url }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen 
                            class="w-full h-full"
                        ></iframe>
                    </div>
                </div>
            @endif

            {{-- Content --}}
            <div class="prose prose-lg max-w-none">
                {!! $post->body_content !!}
            </div>

            {{-- Share and Navigation --}}
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Share:</span>
                        <a 
                            href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" 
                            target="_blank"
                            class="text-gray-400 hover:text-blue-500"
                        >
                            <span class="sr-only">Share on Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
                        </a>
                        <a 
                            href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" 
                            target="_blank"
                            class="text-gray-400 hover:text-blue-500"
                        >
                            <span class="sr-only">Share on LinkedIn</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                    <a 
                        href="{{ route('posts.index') }}" 
                        class="inline-flex items-center text-blue-600 hover:text-blue-800"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Posts
                    </a>
                </div>
            </div>

            {{-- Author Bio --}}
            @if($post->author->bio)
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <div class="flex items-start space-x-4">
                        <img 
                            src="{{ $post->author->profile_image_url }}" 
                            alt="{{ $post->author->name }}" 
                            class="w-16 h-16 rounded-full"
                        >
                        <div>
                            <h3 class="font-medium text-gray-900">
                                About {{ $post->author->name }}
                            </h3>
                            <p class="mt-1 text-gray-600">
                                {{ $post->author->bio }}
                            </p>
                            <div class="mt-4">
                                <a 
                                    href="{{ $post->author->author_url }}" 
                                    class="text-blue-600 hover:text-blue-800"
                                >
                                    View Profile and Posts →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </article>

    {{-- Related Posts --}}
    @if($relatedPosts->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Posts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($relatedPosts as $relatedPost)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        @if($relatedPost->featured_image)
                            <img 
                                src="{{ $relatedPost->featured_image_url }}" 
                                alt="{{ $relatedPost->title }}" 
                                class="w-full h-48 object-cover"
                            >
                        @endif
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ $relatedPost->url }}" class="hover:text-blue-600">
                                    {{ $relatedPost->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-4">{{ $relatedPost->excerpt }}</p>
                            <a 
                                href="{{ $relatedPost->url }}" 
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Read More →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Add copy link button functionality
    function copyToClipboard() {
        const el = document.createElement('textarea');
        el.value = window.location.href;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        
        // Show success message
        alert('Link copied to clipboard!');
    }
</script>
@endpush

@push('styles')
<style>
    .prose-sm {
        @apply prose-blue;
    }
    
    .prose-sm ul {
        @apply list-disc pl-4;
    }
    
    .prose-sm ol {
        @apply list-decimal pl-4;
    }
    
    .prose-sm a {
        @apply text-blue-600 hover:text-blue-800;
    }
    
    .prose-sm p:last-child {
        @apply mb-0;
    }
</style>
@endpush