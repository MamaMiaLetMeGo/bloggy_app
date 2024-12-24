@extends('layouts.app')

@section('title', 'CharlesGendron.com - ' . $category->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Admin Actions -->
    @auth
        @if(auth()->user()->is_admin)
            <div class="mb-6 flex space-x-4">
                <a href="{{ route('admin.categories.edit', $category) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition">
                    Edit Category
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition"
                            onclick="return confirm('Are you sure you want to delete this category?')">
                        Delete Category
                    </button>
                </form>
            </div>
        @endif
    @endauth

    <!-- Category Header with Latest Post Overlay -->
    <div class="relative h-[500px] mb-8 rounded-2xl overflow-hidden">
        @if($category->image)
            <img src="{{ Storage::disk('public')->url($category->image) }}" 
                 alt="{{ $category->name }}" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
        @endif

        <!-- Latest Post Overlay -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="max-w-2xl mx-auto px-4">
                @if($posts->isNotEmpty())
                    <div class="bg-white/3 backdrop-blur-md rounded-2xl p-1 shadow-2xl">
                        <div class="bg-white/40 rounded-xl pt-12 px-6 sm:px-8 pb-8 sm:pb-10">
                            @if($posts->first()->featured_image)
                                <div class="relative h-48 mb-6 rounded-xl overflow-hidden shadow-lg">
                                    <a href="{{ route('posts.category.show', ['category' => $category->slug, 'post' => $posts->first()->slug]) }}" class="block h-full">
                                        <img src="{{ $posts->first()->featured_image_url }}" 
                                             alt="{{ $posts->first()->title }}"
                                             class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                    </a>
                                </div>
                            @endif
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                                <a href="{{ route('posts.category.show', ['category' => $category->slug, 'post' => $posts->first()->slug]) }}" 
                                   class="hover:text-blue-600 transition-colors duration-200">
                                    {{ $posts->first()->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600 mb-6 line-clamp-2">{{ Str::limit(strip_tags($posts->first()->body_content), 150) }}</p>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700 gap-4">
                                <div class="flex flex-wrap items-center gap-4">
                                    @if($posts->first()->author->profile_photo_url)
                                        <img src="{{ $posts->first()->author->profile_photo_url }}" 
                                             alt="{{ $posts->first()->author->name }}" 
                                             class="w-8 h-8 rounded-full">
                                    @endif
                                    <span>By {{ $posts->first()->author->name }}</span>
                                    <span class="mx-3">{{ $posts->first()->published_date->format('M j, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>{{ $posts->first()->likes_count }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <span>{{ $posts->first()->comments_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white/3 backdrop-blur-md rounded-2xl p-1 shadow-2xl">
                        <div class="bg-white/40 rounded-xl pt-12 px-6 sm:px-8 pb-8 sm:pb-10">
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                                No posts yet in {{ $category->name }}
                            </h2>
                            <p class="text-gray-600">Check back soon for new content!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content and Sidebar -->
    <div class="flex flex-col lg:flex-row lg:space-x-8">
        <!-- Main Content -->
        <div class="lg:w-3/4">
            <!-- Posts Section Header with Filter -->
            <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                <h2 class="text-xl font-semibold text-gray-900">Latest Posts</h2>
                <div class="relative inline-block">
                    <select 
                        id="sort-filter"
                        class="bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-8 text-sm text-gray-700 cursor-pointer hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        onchange="window.location.href = this.value"
                    >
                        <option value="{{ route('categories.slug.show', ['category' => $category->slug, 'sort' => 'likes']) }}" {{ $currentSort === 'likes' ? 'selected' : '' }}>Most Liked</option>
                        <option value="{{ route('categories.slug.show', ['category' => $category->slug, 'sort' => 'comments']) }}" {{ $currentSort === 'comments' ? 'selected' : '' }}>Most Commented</option>
                        <option value="{{ route('categories.slug.show', ['category' => $category->slug, 'sort' => 'newest']) }}" {{ $currentSort === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="{{ route('categories.slug.show', ['category' => $category->slug, 'sort' => 'oldest']) }}" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($posts->skip(1) as $post)
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
                                        <div class="font-medium text-gray-900">By {{ $post->author->name }} <span class="mx-3">{{ $post->published_date->format('M j, Y') }}</span></div>
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
                                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                        <span>{{ $post->likes_count }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
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
                                            {{ $post->published_date->format('M d, Y') }}
                                            <span class="inline-flex items-center space-x-3 mt-1">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $post->reading_time }} min
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                    </svg>
                                                    {{ $post->likes_count }}
                                                </span>
                                            </span>
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
@endsection