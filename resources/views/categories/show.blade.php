@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Category Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('categories.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Categories
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            @if($category->image)
                <div class="mb-6">
                    <img src="{{ Storage::disk('public')->url($category->image) }}" 
                         alt="{{ $category->name }}" 
                         class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif

            <h1 class="text-3xl font-bold mb-4">{{ $category->name }}</h1>
            
            @if($category->description)
                <div class="tinymce-content text-gray-600 mb-4">
                    {!! $category->description !!}
                </div>
            @endif

            <div class="text-sm text-gray-500">
                {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }} in this category
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
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

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection