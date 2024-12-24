@extends('layouts.app')

@section('title', 'CharlesGendron.com - ' . $post->title)

@section('meta')
<meta property="og:title" content="{{ $post->title }}" />
<meta property="og:description" content="{{ $post->excerpt }}" />
@if($post->featured_image)
    <meta property="og:image" content="{{ $post->featured_image_url }}" />
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    {{-- Main content and sidebar --}}
    <div class="flex flex-col lg:flex-row lg:space-x-8">
        <!-- Main Content -->
        <article class="lg:w-3/4 bg-white rounded-lg shadow-lg overflow-hidden tinymce-content">
            <div class="pt-4 px-6 pb-6">
                {{-- Breadcrumb and Category Info --}}
                @if($post->breadcrumb || $post->categories->isNotEmpty())
                    <div class="-ml-8 mb-6">
                        {{-- Breadcrumb --}}
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol style="list-style: none;" class="flex items-center space-x-1 md:space-x-3 m-0 p-0">
                                <li class="inline-flex items-center">
                                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                        </svg>
                                        Home
                                    </a>
                                </li>
                                @if($post->categories->isNotEmpty())
                                    <li aria-hidden="true">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <a href="{{ $post->categories->first()->url }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">
                                                {{ $post->categories->first()->name }}
                                            </a>
                                        </div>
                                    </li>
                                @endif
                                <li aria-hidden="true">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $post->title }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                @endif

                {{-- Title --}}
                <h1 class="text-4xl font-bold text-gray-900 mb-3">{{ $post->title }}</h1>

                {{-- Author and Meta --}}
                <div class="flex items-center space-x-4 mb-8">
                    <div class="flex items-center text-gray-700 !no-underline group">
                        <a href="{{ $post->author->author_url }}" class="flex items-center hover:text-blue-600">
                            <img src="{{ $post->author->profile_image_url }}" alt="{{ $post->author->name }}" class="h-10 w-10 rounded-full mr-3">
                            <div>
                                <div class="font-medium text-gray-800">{{ $post->author->name }}</div>
                                @if($post->published_date)
                                    <div class="text-sm text-gray-500">
                                        Published {{ $post->published_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="text-gray-500 text-sm flex items-center space-x-4">
                        <span>{{ $post->reading_time }} min read</span>
                        <a href="#comments" class="text-gray-500 hover:text-blue-600">
                            {{ $post->comments()->count() }} {{ Str::plural('Comment', $post->comments()->count()) }}
                        </a>
                        <button 
                            type="button"
                            class="like-button inline-flex items-center space-x-1"
                            data-post-id="{{ $post->id }}"
                            data-liked="{{ $post->isLikedByIp(request()->ip()) ? 'true' : 'false' }}"
                        >
                            <svg class="w-5 h-5 transition-colors duration-200 {{ $post->isLikedByIp(request()->ip()) ? 'text-red-600' : 'text-gray-400' }}"
                                 xmlns="http://www.w3.org/2000/svg" 
                                 fill="currentColor" 
                                 viewBox="0 0 24 24"
                                 stroke-width="2"
                            >
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="likes-count">{{ $post->likes_count }}</span>
                        </button>
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
                        @if($post->categories->isNotEmpty())
                            <a 
                                href="{{ $post->categories->first()->url }}" 
                                class="inline-flex items-center text-blue-600 hover:text-blue-800"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to {{ $post->categories->first()->name }}
                            </a>
                        @else
                            <a 
                                href="{{ route('home') }}" 
                                class="inline-flex items-center text-blue-600 hover:text-blue-800"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Home
                            </a>
                        @endif
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

        <!-- Sidebar -->
        <div class="lg:w-1/4 mt-8 lg:mt-0">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <a href="#" 
                       class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors duration-200"
                       onclick="event.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' });"
                    >
                        <svg class="w-4 h-4 mr-2 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        Top
                    </a>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contents</h3>
                <nav id="table-of-contents" class="space-y-2">
                    <!-- Table of contents will be populated by JavaScript -->
                </nav>
            </div>
        </div>
    </div>

    {{-- Comments Section - Same width as main content --}}
    <div class="lg:w-3/4">
        <div class="mt-12" id="comments">
            <x-comments 
                :postId="$post->id"
                :commentsCount="$post->comments()->count()"
            />
        </div>
    </div>

    {{-- Related Posts Section - Same width as main content --}}
    @if($relatedPosts->isNotEmpty())
        <div class="lg:w-3/4 mt-12">
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
    document.addEventListener('DOMContentLoaded', function() {
        const likeButtons = document.querySelectorAll('.like-button');
        
        likeButtons.forEach(button => {
            button.addEventListener('click', async function() {
                console.log('Like button clicked'); // Debug log
                const postId = this.dataset.postId;
                const isCurrentlyLiked = this.dataset.liked === 'true';
                const likesCount = parseInt(this.querySelector('.likes-count').textContent) || 0;
                
                // Debug logs
                console.log('Current state:', {
                    postId,
                    isCurrentlyLiked,
                    likesCount
                });
                
                // Optimistically update UI
                const heartIcon = this.querySelector('svg');
                if (isCurrentlyLiked) {
                    heartIcon.classList.remove('text-red-600');
                    heartIcon.classList.add('text-gray-400');
                    this.querySelector('.likes-count').textContent = likesCount - 1;
                } else {
                    heartIcon.classList.remove('text-gray-400');
                    heartIcon.classList.add('text-red-600');
                    this.querySelector('.likes-count').textContent = likesCount + 1;
                }
                this.dataset.liked = (!isCurrentlyLiked).toString();
                
                try {
                    const response = await fetch(`/posts/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    
                    const data = await response.json();
                    console.log('Server response:', data); // Debug log
                    
                    // Update with server response
                    if (data.isLiked) {
                        heartIcon.classList.remove('text-gray-400');
                        heartIcon.classList.add('text-red-600');
                    } else {
                        heartIcon.classList.remove('text-red-600');
                        heartIcon.classList.add('text-gray-400');
                    }
                    this.querySelector('.likes-count').textContent = data.likesCount;
                    this.dataset.liked = data.isLiked.toString();
                    
                } catch (error) {
                    console.error('Error:', error);
                    // Revert optimistic update on error
                    if (isCurrentlyLiked) {
                        heartIcon.classList.add('text-red-600');
                        heartIcon.classList.remove('text-gray-400');
                    } else {
                        heartIcon.classList.add('text-gray-400');
                        heartIcon.classList.remove('text-red-600');
                    }
                    this.querySelector('.likes-count').textContent = likesCount;
                    this.dataset.liked = isCurrentlyLiked.toString();
                }
            });
        });
    });
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

    @media (min-width: 1024px) {
        .sticky {
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }
    }

    #table-of-contents {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }

    #table-of-contents::-webkit-scrollbar {
        width: 4px;
    }

    #table-of-contents::-webkit-scrollbar-track {
        background: transparent;
    }

    #table-of-contents::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 2px;
    }
</style>
@endpush