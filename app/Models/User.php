<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\HasBlogPosts;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasBlogPosts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',                 // Add if you want author bios
        'profile_image',       // Add if you want author images
        'social_links',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',    // For JSON storage of social media links
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get all posts authored by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Get only published posts authored by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()
            ->where('status', 'published')
            ->where('published_date', '<=', Carbon::now());
    }

    /**
     * Get only draft posts authored by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function draftPosts(): HasMany
    {
        return $this->posts()->where('status', 'draft');
    }

    /**
     * Get the count of all posts by the user.
     *
     * @return int
     */
    public function getPostCountAttribute(): int
    {
        return $this->posts()->count();
    }

    /**
     * Get the count of published posts by the user.
     *
     * @return int
     */
    public function getPublishedPostCountAttribute(): int
    {
        return $this->publishedPosts()->count();
    }

    /**
     * Get the author's full name or username for display.
     *
     * @return string
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the URL for the author's profile page.
     *
     * @return string
     */
    public function getAuthorUrlAttribute(): string
    {
        return route('authors.show', $this);
    }

    /**
     * Get the author's profile image URL.
     *
     * @return string
     */
    public function getProfileImageUrlAttribute(): string
    {
        return $this->profile_image
            ? Storage::url($this->profile_image)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    /**
     * Get recent posts by the author.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentPosts(int $limit = 5): Collection
    {
        return $this->publishedPosts()
            ->orderBy('published_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get posts statistics for the author.
     *
     * @return array<string, mixed>
     */
    public function getPostsStatistics(): array
    {
        return [
            'total_posts' => $this->post_count,
            'published_posts' => $this->published_post_count,
            'draft_posts' => $this->draftPosts()->count(),
            'total_views' => $this->posts()->sum('views'),
            'avg_reading_time' => $this->publishedPosts()->avg('reading_time'),
            'most_viewed_post' => $this->publishedPosts()
                ->orderBy('views', 'desc')
                ->first(),
            'latest_post' => $this->publishedPosts()
                ->latest('published_date')
                ->first(),
        ];
    }

    /**
     * Get categories the author has written in.
     *
     * @return Collection
     */
    public function getAuthorCategories(): Collection
    {
        return Category::whereHas('posts', function ($query) {
            $query->where('author_id', $this->id);
        })->get();
    }

    /**
     * Check if user is an active author (has published posts).
     *
     * @return bool
     */
    public function isActiveAuthor(): bool
    {
        return $this->publishedPosts()->exists();
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function newsletterSubscription()
    {
        return $this->hasOne(NewsletterSubscription::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}