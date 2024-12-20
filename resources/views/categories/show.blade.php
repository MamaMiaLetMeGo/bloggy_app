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
                        
                        <!-- Admin Actions -->
                        @auth
                            @if(auth()->user()->is_admin)
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    @if($category->posts_count === 0)
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                                    onclick="return confirm('Are you sure you want to delete this category?')">
                                                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if($category->image)
                        <div class="mb-6">
                            <img src="{{ Storage::disk('public')->url($category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="w-full h-64 object-cover rounded-lg">
                        </div>
                    @endif

                    @if($category->description)
                        <div class="tinymce-content text-gray-600 mb-4">
                            {!! $category->description !!}
                        </div>
                    @endif

                    <div class="text-sm text-gray-500">
                        {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} in this category
                    </div>
                </div>

                <!-- Posts Grid -->
                <div class="mt-8">
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
                                        <h2 class="text-xl font-semibold mb-2 hover:text-blue-600">{{ $post->title }}</h2>
                                        
                                        <p class="text-gray-600 mb-4">
                                            {{ $post->excerpt ?? Str::limit(strip_tags($post->body_content), 100) }}
                                        </p>

                                        <div class="flex items-center text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <img src="{{ $post->author->profile_image_url ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name) }}" 
                                                     alt="{{ $post->author->name }}"
                                                     class="w-6 h-6 rounded-full mr-2">
                                                <span>{{ $post->author->name }}</span>
                                            </div>
                                            <span class="mx-2">•</span>
                                            <span>{{ $post->published_date->format('M d, Y') }}</span>
                                            @if($post->reading_time)
                                                <span class="mx-2">•</span>
                                                <span>{{ $post->reading_time }} min read</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <h3 class="text-lg font-medium text-gray-900">No posts yet</h3>
                                <p class="mt-2 text-sm text-gray-500">No posts have been published in this category.</p>
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
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection