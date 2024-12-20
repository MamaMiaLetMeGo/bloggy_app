<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Travel adventures and experiences',
                'is_featured' => true,
            ],
            [
                'name' => 'Sailing',
                'slug' => 'sailing',
                'description' => 'Sailing journeys and maritime adventures',
                'is_featured' => true,
            ],
            [
                'name' => 'Photography',
                'slug' => 'photography',
                'description' => 'Photography tips and amazing shots',
                'is_featured' => true,
            ],
            [
                'name' => 'Adventure',
                'slug' => 'adventure',
                'description' => 'Exciting adventures and outdoor activities',
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}