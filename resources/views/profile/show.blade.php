@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $user->name }}'s Profile
                    </h2>
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Edit Profile') }}
                        </a>
                    @endif
                </div>

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Basic Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-start space-x-6">
                                <div class="flex-shrink-0">
                                    @if($user->profile_image)
                                        <img src="{{ Storage::url($user->profile_image) }}" 
                                             alt="{{ $user->name }}" 
                                             class="h-24 w-24 object-cover rounded-full">
                                    @else
                                        <div class="h-24 w-24 rounded-full bg-blue-600 flex items-center justify-center">
                                            <span class="text-2xl font-medium text-white">
                                                {{ substr($user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-grow">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Name</p>
                                        <p class="mt-1">{{ $user->name }}</p>
                                    </div>
                                    @if(auth()->id() === $user->id)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Email</p>
                                            <p class="mt-1">{{ $user->email }}</p>
                                        </div>
                                    @endif
                                    @if($user->bio)
                                        <div class="col-span-2">
                                            <p class="text-sm font-medium text-gray-500">Bio</p>
                                            <p class="mt-1">{{ $user->bio }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    @if($user->posts->count() > 0)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Recent Posts</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="space-y-4">
                                    @foreach($user->posts as $post)
                                        <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                                            <h4 class="text-lg font-medium">
                                                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $post->title }}
                                                </a>
                                            </h4>
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ $post->published_date->format('F j, Y') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
