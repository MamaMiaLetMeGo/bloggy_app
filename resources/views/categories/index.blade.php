@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">Categories</h1>
        </div>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.categories.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Category
                </a>
            @endif
        @endauth
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <form action="{{ route('categories.index') }}" method="GET" class="flex gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search categories..."
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Search
                </button>
            </form>
        </div>
        <div>
            <select 
                onchange="window.location.href='{{ route('categories.index') }}?sort=' + this.value + '{{ request('search') ? '&search=' . request('search') : '' }}'"
                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="" {{ !request('sort') ? 'selected' : '' }}>Sort by name</option>
                <option value="posts" {{ request('sort') === 'posts' ? 'selected' : '' }}>Sort by post count</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($categories as $category)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                @if($category->image)
                    <a href="{{ route('categories.slug.show', $category->slug) }}" class="block mb-4">
                        <img src="{{ Storage::disk('public')->url($category->image) }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-48 object-cover rounded-md hover:opacity-90 transition duration-300">
                    </a>
                @endif
                
                <div class="flex justify-between items-start mb-4">
                    <a href="{{ route('categories.slug.show', $category->slug) }}" 
                       class="text-xl font-semibold hover:text-blue-600 transition duration-300">
                        {{ $category->name }}
                    </a>
                    @auth
                        @if(auth()->user()->is_admin)
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                                @if($category->posts_count === 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                                onclick="return confirm('Are you sure you want to delete this category?')">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>
                
                @if($category->description)
                    <div class="text-gray-600 mb-4 tinymce-content">
                        {!! Str::limit(strip_tags($category->description), 100) !!}
                    </div>
                @endif

                <div class="text-sm text-gray-500">
                    {{ $category->posts_count ?? 0 }} posts
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No categories found.</p>
            </div>
        @endforelse
    </div>

    @if($categories->hasPages())
        <div class="mt-8">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
