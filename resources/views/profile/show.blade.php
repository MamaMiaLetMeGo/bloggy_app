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
                    <a href="{{ route('profile.edit') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Edit Profile') }}
                    </a>
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
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Email</p>
                                        <p class="mt-1">{{ $user->email }}</p>
                                        @if ($user->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                Not Verified
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Account Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Member Since</p>
                                    <p class="mt-1">{{ $user->created_at->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Last Updated</p>
                                    <p class="mt-1">{{ $user->updated_at->format('F j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    @if($user->posts->count() > 0)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Recent Posts</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="space-y-4">
                                    @foreach($user->posts->take(5) as $post)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <a href="{{ $post->url }}" class="text-gray-900 hover:text-blue-600">
                                                    {{ $post->title }}
                                                </a>
                                                <p class="text-sm text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                                            </div>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
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
