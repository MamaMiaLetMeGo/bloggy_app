<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LocationSubscriber extends Model
{
    use Notifiable;

    protected $fillable = ['email', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
