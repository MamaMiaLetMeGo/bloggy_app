@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">All Posts</h1>
        @auth
            <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Post
            </a>
        @endauth
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse($posts as $post)
            <article class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                @if($post->featured_image)
                    <img 
                        src="{{ $post->featured_image_url }}" 
                        alt="{{ $post->title }}" 
                        class="w-full h-48 object-cover"
                    >
                @endif
                <div class="p-6 flex-1 flex flex-col">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">
                        <a href="{{ $post->url }}" class="hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <div class="text-sm text-gray-500 mb-4 flex items-center">
                        <span class="hover:text-blue-600 flex items-center">
                            <a href="{{ $post->author->author_url }}" class="flex items-center hover:text-blue-600">
                                <img src="{{ $post->author->profile_image_url }}" alt="{{ $post->author->name }}" class="h-6 w-6 rounded-full mr-2">
                                {{ $post->author->name }}
                            </a>
                        </span>
                        @if($post->published_date)
                            <span class="mx-2">•</span>
                            <span>{{ $post->published_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                    <p class="text-gray-600 mb-4">
                        {{ $post->excerpt }}
                    </p>
                    @if($post->categories->count() > 0)
                        <div class="mb-4">
                            @foreach($post->categories as $category)
                                <a 
                                    href="{{ $category->url }}" 
                                    class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-600 mr-2 mb-2 hover:bg-gray-200"
                                >
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex justify-between items-center mt-auto pt-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="mr-4">{{ $post->reading_time }} min read</span>
                        </div>
                        <a href="{{ $post->url }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Read More →
                        </a>
                    </div>
                    @auth
                        @can('update', $post)
                            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-4">
                                <a 
                                    href="{{ route('admin.posts.edit', $post) }}" 
                                    class="text-gray-600 hover:text-gray-900 text-sm"
                                >
                                    Edit
                                </a>
                                <form 
                                    action="{{ route('admin.posts.destroy', $post) }}" 
                                    method="POST" 
                                    class="inline"
                                    x-data
                                    @submit.prevent="if (confirm('Are you sure you want to delete this post?')) $el.submit()"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endcan
                    @endauth
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow-lg p-12 text-center">
                <p class="text-gray-500 text-lg">No posts available yet.</p>
                @auth
                    <a 
                        href="{{ route('admin.posts.create') }}" 
                        class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Create your first post →
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection