@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Admin Actions -->
    @auth
        @if(auth()->user()->is_admin)
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                </div>
                <a href="{{ route('admin.categories.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Category
                </a>
            </div>
        @endif
    @endauth

    <!-- Category Header -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('categories.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Categories</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row lg:space-x-8">
            <!-- Main Content -->
            <div class="lg:w-3/4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
                        <div class="text-sm text-gray-500">
                            {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} in this category
                        </div>
                    </div>

                    @if($category->image)
                        <div class="mb-6">
                            <img src="{{ Storage::disk('public')->url($category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endif

                    @if($category->description)
                        <div class="prose max-w-none mb-6">
                            {!! $category->description !!}
                        </div>
                    @endif

                    <!-- Posts Section Header with Filter -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Latest Posts</h2>
                        <div class="relative inline-block">
                            <select 
                                id="sort-filter"
                                class="bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-8 text-sm text-gray-700 cursor-pointer hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="window.location.href = '{{ route('categories.show', $category) }}?sort=' + this.value"
                            >
                                <option value="most-liked" {{ $currentSort === 'most-liked' ? 'selected' : '' }}>Most Liked</option>
                                <option value="most-commented" {{ $currentSort === 'most-commented' ? 'selected' : '' }}>Most Commented</option>
                                <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($posts as $post)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                                <a href="{{ $post->url }}" class="block">
                                    @if($post->featured_image)
                                        <img src="{{ $post->featured_image_url }}" 
                                             alt="{{ $post->title }}" 
                                             class="w-full h-48 object-cover">
                                    @endif
                                    
                                    <div class="p-6">
                                        <h2 class="text-xl font-semibold mb-4 hover:text-blue-600">{{ $post->title }}</h2>

                                        <div class="flex items-center mb-4">
                                            <img src="{{ $post->author->profile_image_url ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name) }}" 
                                                 alt="{{ $post->author->name }}"
                                                 class="w-8 h-8 rounded-full mr-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $post->author->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $post->published_date->format('M d, Y') }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ $post->reading_time }} min read</span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                </svg>
                                                <span>{{ $post->likes_count }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                                <span>{{ $post->comments()->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-8">
                                <p class="text-gray-500">No posts found in this category.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/4 mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Recent Posts</h2>
                    <div class="space-y-4">
                        @foreach($recentPosts as $index => $post)
                            @if($index < 10)
                                <a href="{{ $post->url }}" class="block group">
                                    <div class="flex items-start space-x-3">
                                        @if($post->featured_image)
                                            <img src="{{ Storage::disk('public')->url($post->featured_image) }}" 
                                                alt="{{ $post->title }}" 
                                                class="w-16 h-16 object-cover rounded">
                                        @endif
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 line-clamp-2">
                                                {{ $post->title }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $post->published_date->format('M j, Y') }} • {{ $post->reading_time }} min read
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach

                        @if($recentPosts->count() > 10)
                            <a href="{{ route('categories.posts.index', $category) }}" 
                               class="block text-sm text-blue-600 hover:text-blue-800 font-medium mt-4">
                                View all posts →
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection