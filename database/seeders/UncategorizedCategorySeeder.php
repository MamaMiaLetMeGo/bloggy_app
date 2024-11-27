<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;

class UncategorizedCategorySeeder extends Seeder
{
    public function run()
    {
        if (!Category::where('slug', 'uncategorized')->exists()) {
            Category::create([
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'description' => 'Posts without a specific category'
            ]);
        }
    }
}
