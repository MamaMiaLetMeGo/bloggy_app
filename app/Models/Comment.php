<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content',
        'author_name',
        'author_email',
        'post_id',
        'user_id',
        'parent_id',
        'is_approved'
    ];

    protected $with = ['user'];

    protected $appends = ['liked'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function getLikedAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        return DB::table('comment_likes')
            ->where('comment_id', $this->id)
            ->where('user_id', auth()->id())
            ->exists();
    }
}
