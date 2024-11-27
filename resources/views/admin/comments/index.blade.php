@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Comments Management</h1>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500">Total Comments</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            <div class="text-sm text-gray-500">Pending Approval</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
            <div class="text-sm text-gray-500">Approved</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6">
            <form action="{{ route('admin.comments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Comments List --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('admin.comments.bulk') }}" method="POST">
            @csrf
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" onclick="toggleAll(this)">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Author
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Comment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Post
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($comments as $comment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="comments[]" value="{{ $comment->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($comment->user_id)
                                        <img src="{{ $comment->user->profile_image_url }}" alt="" class="h-8 w-8 rounded-full">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</div>
                                            <div class="text-sm text-gray-500">Registered User</div>
                                        </div>
                                    @else
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $comment->author_name }}</div>
                                            <div class="text-sm text-gray-500">Guest User</div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($comment->content, 100) }}</div>
                                <div class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('posts.show', $comment->post) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ Str::limit($comment->post->title, 30) }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                @if(!$comment->is_approved)
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this comment?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Bulk Actions --}}
            <div class="px-6 py-4 bg-gray-50">
                <div class="flex items-center">
                    <select name="action" class="rounded-md border-gray-300 mr-4">
                        <option value="approve">Approve Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">
                        Apply to Selected
                    </button>
                </div>
            </div>
        </form>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50">
            {{ $comments->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleAll(source) {
    const checkboxes = document.getElementsByName('comments[]');
    for(let checkbox of checkboxes) {
        checkbox.checked = source.checked;
    }
}
</script>
@endpush
@endsection