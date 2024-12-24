<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Get the admin user (assuming it exists from AdminUserSeeder)
        $author = User::where('email', 'gitcommitcg@gmail.com')->first();
        if (!$author) {
            throw new \Exception('Admin user not found. Please run AdminUserSeeder first.');
        }

        // Get all categories
        $categories = Category::all();
        
        // Create 10 posts per category
        foreach ($categories as $category) {
            for ($i = 0; $i < 10; $i++) {
                // Generate 4 sections with headers and content
                $sections = [];
                for ($j = 0; $j < 4; $j++) {
                    $sections[] = [
                        'header' => $faker->unique()->sentence(),
                        'content' => $faker->paragraphs(5, true),
                    ];
                }

                // Combine sections into body content with h1 tags
                $bodyContent = '';
                foreach ($sections as $section) {
                    $bodyContent .= "<h1>{$section['header']}</h1>\n\n";
                    $bodyContent .= $section['content'] . "\n\n";
                }

                // Add extra paragraphs to reach approximately 1000 words
                $extraParagraphs = $faker->paragraphs(3, true);
                $bodyContent .= $extraParagraphs;

                // Create post
                $title = $faker->unique()->sentence();
                $post = Post::create([
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'body_content' => $bodyContent,
                    'featured_image' => null, // You might want to add real images later
                    'status' => 'published',
                    'published_date' => Carbon::now()->subDays(rand(1, 30)),
                    'author_id' => $author->id,
                    'breadcrumb' => $category->name,
                ]);

                // Attach the current category
                $post->categories()->attach($category->id);

                // Randomly attach 0-2 additional categories
                $otherCategories = $categories->where('id', '!=', $category->id)->random(rand(0, 2));
                foreach ($otherCategories as $otherCategory) {
                    $post->categories()->attach($otherCategory->id);
                }
            }
        }
    }
}
