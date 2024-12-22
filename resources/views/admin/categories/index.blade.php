@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Categories</h1>
            <p class="mt-2 text-sm text-gray-700">Create and manage blog categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Category
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @foreach($categories as $category)
                <li>
                    <div class="px-4 py-4 flex items-center sm:px-6">
                        <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <div class="flex text-sm">
                                    <a href="{{ route('categories.slug.show', $category->slug) }}" 
                                       class="font-medium text-blue-600 truncate hover:text-blue-800 hover:underline">
                                        {{ $category->name }}
                                    </a>
                                    <p class="ml-1 flex-shrink-0 font-normal text-gray-500">in {{ $category->posts_count }} posts</p>
                                </div>
                                <div class="mt-2 flex">
                                    @if($category->description)
                                        <div class="flex items-center text-sm text-gray-500">
                                            <p>{{ Str::limit($category->description, 100) }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 flex-shrink-0 sm:mt-0">
                                <div class="flex space-x-4">
                                    <form action="{{ route('admin.categories.toggleFeatured', $category) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md {{ $category->is_featured ? 'text-yellow-700 bg-yellow-100 hover:bg-yellow-50' : 'text-gray-700 bg-gray-100 hover:bg-gray-50' }}">
                                            <svg class="h-4 w-4 {{ $category->is_featured ? 'text-yellow-400' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Edit
                                    </a>
                                    @if($category->posts_count === 0)
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this category?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    @if($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection