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
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
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
        'provider',
        'provider_id',
        'two_factor_secret',
        'two_factor_enabled',
        'failed_login_attempts',
        'login_code',
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'social_links' => 'array',    // For JSON storage of social media links
        'is_admin' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
        'login_code_expires_at' => 'datetime',
    ];

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

    public function getProfilePhotoUrlAttribute()
    {
        // If you're using Laravel's profile photos feature
        return $this->profile_photo_path
            ? Storage::url($this->profile_photo_path)
            : null;
    }

    public function enableTwoFactor(): void
    {
        $this->two_factor_enabled = true;
        $this->two_factor_confirmed_at = now();
        $this->save();
    }

    public function disableTwoFactor(): void
    {
        $this->two_factor_enabled = false;
        $this->two_factor_confirmed_at = null;
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->save();
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }
}