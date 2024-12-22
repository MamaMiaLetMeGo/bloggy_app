@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6">Welcome Back, {{ auth()->user()->name }}!</h1>

                <!-- Quick Links Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Quick Links</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('profile.show') }}" class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="font-medium">Your Profile</div>
                            <div class="text-sm text-gray-600">View and edit your profile settings</div>
                        </a>
                        
                        <a href="{{ route('location.show') }}" class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="font-medium">Movement Notifications</div>
                            <div class="text-sm text-gray-600">
                                @if($hasLocationNotifications)
                                    Manage your notification settings
                                @else
                                    Set up movement notifications
                                @endif
                            </div>
                        </a>

                        <a href="{{ route('authors.index') }}" class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="font-medium">Browse Authors</div>
                            <div class="text-sm text-gray-600">Discover new content creators</div>
                        </a>
                    </div>
                </div>

                <!-- New Posts Section -->
                @if($newPosts->isNotEmpty())
                    <div>
                        <h2 class="text-xl font-semibold mb-4">New Posts Since Your Last Visit</h2>
                        <div class="space-y-4">
                            @foreach($newPosts as $post)
                                <div class="p-4 border rounded-lg hover:bg-gray-50">
                                    <a href="{{ route('posts.category.show', ['category' => $post->categories->first()->slug, 'post' => $post->slug]) }}" 
                                       class="block">
                                        <h3 class="font-medium">{{ $post->title }}</h3>
                                        <div class="text-sm text-gray-600">
                                            By {{ $post->author->name }} in {{ $post->categories->first()->name }}
                                            • {{ $post->published_date->diffForHumans() }}
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-gray-600">
                        No new posts since your last visit.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection