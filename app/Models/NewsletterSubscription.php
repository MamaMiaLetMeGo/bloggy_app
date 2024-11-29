<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'travel_updates',
        'sailing_updates',
    ];

    protected $casts = [
        'travel_updates' => 'boolean',
        'sailing_updates' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
