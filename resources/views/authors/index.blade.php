@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Featured Authors Section -->
        @if($featuredAuthors->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Featured Authors</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredAuthors as $author)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                @if($author->profile_image)
                                    <img src="{{ Storage::url($author->profile_image) }}" alt="{{ $author->name }}" class="h-16 w-16 object-cover">
                                @else
                                    <span class="text-2xl text-gray-600">{{ substr($author->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-semibold">{{ $author->name }}</h3>
                                <p class="text-gray-600">{{ $author->published_posts_count }} {{ Str::plural('post', $author->published_posts_count) }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="bg-gray-50 rounded p-2">
                                <span class="block text-lg font-semibold">{{ $author->published_posts_count }}</span>
                                <span class="text-sm text-gray-600">Posts</span>
                            </div>
                            <div class="bg-gray-50 rounded p-2">
                                <span class="block text-lg font-semibold">{{ $author->comments_count ?? 0 }}</span>
                                <span class="text-sm text-gray-600">Comments</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('authors.show', $author) }}" 
                               class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- All Authors Section -->
        <div>
            <h2 class="text-2xl font-bold mb-6">All Authors</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($authors as $author)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                @if($author->profile_image)
                                    <img src="{{ Storage::url($author->profile_image) }}" alt="{{ $author->name }}" class="h-12 w-12 object-cover">
                                @else
                                    <span class="text-xl text-gray-600">{{ substr($author->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold">{{ $author->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $author->published_posts_count }} {{ Str::plural('post', $author->published_posts_count) }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Published Posts:</span>
                                <span class="font-medium">{{ $author->published_posts_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Comments:</span>
                                <span class="font-medium">{{ number_format($author->comments_count ?? 0) }}</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('authors.show', $author) }}" 
                               class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $authors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 