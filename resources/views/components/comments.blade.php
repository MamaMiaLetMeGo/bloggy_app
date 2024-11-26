@props(['postId', 'commentsCount'])

<div x-data="comments" class="mt-8">
    {{-- Comments Header with Sort Control --}}
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold">Comments ({{ $commentsCount }})</h3>
        <select x-model="sort" @change="loadComments" class="rounded-lg border-gray-300 text-sm">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="popular">Most Popular</option>
        </select>
    </div>
    
    {{-- Comment Form --}}
    <form @submit.prevent="submitComment" class="mb-8">
        <textarea
            x-model="formData.content"
            class="w-full rounded-lg border-gray-300 shadow-sm"
            rows="3"
            placeholder="Leave a comment..."
        ></textarea>

        @guest
            <div class="grid grid-cols-2 gap-4 mt-4">
                <input
                    type="text"
                    x-model="formData.author_name"
                    class="rounded-lg"
                    placeholder="Your name"
                >
                <input
                    type="email"
                    x-model="formData.author_email"
                    class="rounded-lg"
                    placeholder="Your email"
                >
            </div>
        @endguest

        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">
            Submit Comment
        </button>
    </form>

    {{-- Comments List --}}
    <div class="space-y-6">
        <template x-for="comment in comments" :key="comment.id">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between">
                    <div>
                        <h5 x-text="comment.author_name" class="font-medium"></h5>
                        <p class="text-sm text-gray-500" x-text="formatDate(comment.created_at)"></p>
                    </div>
                    <button 
                        @click="likeComment(comment)"
                        class="flex items-center space-x-1 text-gray-500 hover:text-blue-600"
                    >
                        <span x-text="comment.likes_count"></span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                    </button>
                </div>
                <p x-text="comment.content" class="mt-2"></p>
                <button 
                    @click="showReplyForm(comment.id)"
                    class="mt-2 text-sm text-blue-600 hover:text-blue-800"
                >
                    Reply
                </button>
            </div>
        </template>
    </div>

    {{-- Load More --}}
    <button 
        x-show="hasMorePages"
        @click="loadMore"
        class="mt-4 w-full py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
    >
        Load More Comments
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('comments', () => ({
        comments: [],
        formData: {
            content: '',
            author_name: '',
            author_email: '',
            parent_id: null
        },
        sort: 'newest',
        page: 1,
        hasMorePages: false,
        postId: {{ $postId }},

        async init() {
            await this.loadComments();
        },

        async loadComments() {
            const response = await fetch(`/posts/${this.postId}/comments?sort=${this.sort}&page=${this.page}`);
            const data = await response.json();
            
            this.comments = this.page === 1 ? data.data : [...this.comments, ...data.data];
            this.hasMorePages = data.next_page_url !== null;
        },

        async submitComment() {
            try {
                const response = await fetch(`/posts/${this.postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.ok) {
                    this.formData = {
                        content: '',
                        author_name: '',
                        author_email: '',
                        parent_id: null
                    };
                    await this.loadComments();
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
            }
        },

        async likeComment(comment) {
            try {
                const response = await fetch(`/comments/${comment.id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    comment.likes_count = data.likes_count;
                }
            } catch (error) {
                console.error('Error liking comment:', error);
            }
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString();
        }
    }));
});
</script>
@endpush