<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = ['email', 'name', 'message'];

    public static function hasRecentSubmission($email, $hours = 24)
    {
        return static::where('email', $email)
            ->where('created_at', '>=', now()->subHours($hours))
            ->exists();
    }
}
