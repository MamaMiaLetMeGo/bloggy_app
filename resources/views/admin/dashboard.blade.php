@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Total Posts</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $postStats['total'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Published Posts</h3>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $postStats['published'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Draft Posts</h3>
            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $postStats['drafts'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500">Posts This Month</h3>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $postStats['thisMonth'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Recent Posts --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Recent Posts</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentPosts as $post)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">
                                    <a href="{{ $post->url }}" class="hover:text-blue-600">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <div class="mt-1 text-sm text-gray-500">
                                    By {{ $post->author->name }} • {{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $post->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $post->status === 'archived' ? 'bg-gray-100 text-gray-800' : '' }}
                                ">
                                    {{ ucfirst($post->status) }}
                                </span>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No posts yet
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Monthly Posts Chart --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-6">Posts by Month</h2>
            <canvas id="monthlyPostsChart" class="w-full"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyPostsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyPosts->pluck('month')->reverse()) !!},
            datasets: [{
                label: 'Posts',
                data: {!! json_encode($monthlyPosts->pluck('total')->reverse()) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush