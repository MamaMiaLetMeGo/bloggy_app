<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationUpdate extends Model
{
    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'timestamp',
        'raw_data'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getMapUrlAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('timestamp', 'desc');
    }

    public function scopeLastDay($query)
    {
        return $query->where('timestamp', '>=', now()->subDay());
    }

    public function getFormattedLocationAttribute(): string
    {
        return "({$this->latitude}, {$this->longitude})";
    }
}