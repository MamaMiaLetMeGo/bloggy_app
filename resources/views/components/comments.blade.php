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
    
    {{-- Main Comment Form --}}
    <form @submit.prevent="submitComment" class="mb-8">
        <textarea
            x-model="formData.content"
            class="w-full rounded-lg border-gray-300 shadow-sm"
            rows="3"
            placeholder="Leave a comment..."
            :disabled="isSubmitting"
            @mention="handleMention"
        ></textarea>

        @guest
            <div class="grid grid-cols-2 gap-4 mt-4">
                <input
                    type="text"
                    x-model="formData.author_name"
                    class="rounded-lg border-gray-300"
                    placeholder="Your name"
                    :disabled="isSubmitting"
                >
                <input
                    type="email"
                    x-model="formData.author_email"
                    class="rounded-lg border-gray-300"
                    placeholder="Your email"
                    :disabled="isSubmitting"
                >
            </div>
        @endguest

        <div x-show="error" x-text="error" class="mt-2 text-red-600 text-sm"></div>

        <button 
            type="submit" 
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg disabled:opacity-50"
            :disabled="isSubmitting"
        >
            <span x-show="!isSubmitting">Submit Comment</span>
            <span x-show="isSubmitting">Submitting...</span>
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
                    <div class="flex items-center space-x-4">
                        <button 
                            @click="likeComment(comment)"
                            class="flex items-center space-x-1 text-gray-500 hover:text-blue-600"
                            :class="{ 'text-blue-600': comment.liked }"
                        >
                            <span x-text="comment.likes_count || 0"></span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </button>
                        <button 
                            @click="startReply(comment)"
                            class="text-gray-500 hover:text-blue-600 text-sm"
                        >
                            Reply
                        </button>
                    </div>
                </div>
                <p x-text="comment.content" class="mt-2"></p>

                {{-- Reply Form --}}
                <div x-show="replyingTo === comment.id" class="mt-4">
                    <form @submit.prevent="submitReply(comment)">
                        <textarea
                            x-model="formData.content"
                            class="w-full rounded-lg border-gray-300 shadow-sm"
                            rows="3"
                            placeholder="Write your reply..."
                            :disabled="isSubmitting"
                            @mention="handleMention"
                        ></textarea>

                        @guest
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <input
                                    type="text"
                                    x-model="formData.author_name"
                                    class="rounded-lg border-gray-300"
                                    placeholder="Your name"
                                    :disabled="isSubmitting"
                                >
                                <input
                                    type="email"
                                    x-model="formData.author_email"
                                    class="rounded-lg border-gray-300"
                                    placeholder="Your email"
                                    :disabled="isSubmitting"
                                >
                            </div>
                        @endguest

                        <div class="mt-2 space-x-2">
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg disabled:opacity-50"
                                :disabled="isSubmitting"
                            >
                                <span x-show="!isSubmitting">Submit Reply</span>
                                <span x-show="isSubmitting">Submitting...</span>
                            </button>
                            <button 
                                @click="cancelReply" 
                                type="button"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Nested Replies --}}
                <template x-if="comment.replies && comment.replies.length > 0">
                    <div class="mt-4 ml-6 space-y-4">
                        <template x-for="reply in comment.replies" :key="reply.id">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h6 x-text="reply.author_name" class="font-medium"></h6>
                                        <p class="text-sm text-gray-500" x-text="formatDate(reply.created_at)"></p>
                                    </div>
                                    <button 
                                        @click="likeComment(reply)"
                                        class="flex items-center space-x-1 text-gray-500 hover:text-blue-600"
                                        :class="{ 'text-blue-600': reply.liked }"
                                    >
                                        <span x-text="reply.likes_count || 0"></span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                        </svg>
                                    </button>
                                </div>
                                <p x-text="reply.content" class="mt-2"></p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>
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
        error: null,
        isSubmitting: false,
        sort: 'newest',
        page: 1,
        hasMorePages: false,
        postId: {{ $postId }},
        replyingTo: null,
        mentions: [],

        async init() {
            await this.loadComments();
            this.setupMentions();
        },

        setupMentions() {
            const textarea = this.$el.querySelector('textarea');
            let mentionSearch = '';
            
            textarea.addEventListener('input', (e) => {
                const pos = e.target.selectionStart;
                const content = e.target.value;
                
                if (content[pos - 1] === '@') {
                    this.showMentionSuggestions();
                }
            });
        },

        async showMentionSuggestions() {
            // Fetch users who have commented on this post
            const response = await fetch(`/posts/${this.postId}/commenters`);
            const users = await response.json();
            this.mentions = users;
        },

        handleMention(user) {
            this.formData.content += `@${user.name} `;
            this.mentions = [];
        },

        startReply(comment) {
            this.replyingTo = comment.id;
            this.formData.parent_id = comment.id;
            this.formData.content = `@${comment.author_name} `;
        },

        cancelReply() {
            this.replyingTo = null;
            this.formData.parent_id = null;
            this.formData.content = '';
        },

        async loadComments() {
            try {
                const response = await fetch(`/posts/${this.postId}/comments?page=${this.page}&sort=${this.sort}`);
                const data = await response.json();
                
                if (this.page === 1) {
                    this.comments = data.data;
                } else {
                    this.comments = [...this.comments, ...data.data];
                }
                
                this.hasMorePages = data.current_page < data.last_page;
            } catch (error) {
                console.error('Error loading comments:', error);
            }
        },

        async loadMore() {
            this.page++;
            await this.loadComments();
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        async submitComment() {
            if (this.isSubmitting) return;
            this.error = null;
            this.isSubmitting = true;

            try {
                const response = await fetch(`/posts/${this.postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to submit comment');
                }

                // Reset form
                this.formData = {
                    content: '',
                    author_name: '',
                    author_email: '',
                };

                // Reset to first page and reload comments
                this.page = 1;
                await this.loadComments();

            } catch (error) {
                this.error = error.message;
                console.error('Error submitting comment:', error);
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitReply(parentComment) {
            if (this.isSubmitting) return;
            this.error = null;
            this.isSubmitting = true;

            try {
                const response = await fetch(`/posts/${this.postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to submit reply');
                }

                // Add the new reply to the parent comment's replies
                if (!parentComment.replies) {
                    parentComment.replies = [];
                }
                parentComment.replies.push(data.comment);

                // Reset form
                this.formData = {
                    content: '',
                    author_name: '',
                    author_email: '',
                    parent_id: null
                };

                this.replyingTo = null;

            } catch (error) {
                this.error = error.message;
                console.error('Error submitting reply:', error);
            } finally {
                this.isSubmitting = false;
            }
        },

        async likeComment(comment) {
            if (comment.isLiking) return;
            
            try {
                comment.isLiking = true;
                console.log('Liking comment:', comment.id); // Debug log
                
                const response = await fetch(`/comments/${comment.id}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status); // Debug log

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to like comment');
                }

                const data = await response.json();
                console.log('Like response:', data); // Debug log
                
                // Update the comment's likes count
                comment.likes_count = data.likes_count;
                comment.liked = true;

            } catch (error) {
                console.error('Error liking comment:', error);
                alert('Failed to like comment. Please try again.');
            } finally {
                comment.isLiking = false;
            }
        }
    }));
});
</script>
@endpush