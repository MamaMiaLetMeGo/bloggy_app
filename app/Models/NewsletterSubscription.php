<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'verified_at',
        'travel_updates',
        'sailing_updates',
    ];

    protected $casts = [
        'travel_updates' => 'boolean',
        'sailing_updates' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
