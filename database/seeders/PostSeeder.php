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
        $author = User::where('email', 'admin@example.com')->first();
        if (!$author) {
            $author = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]);
        }

        // Get all categories
        $categories = Category::all();
        
        // Create 10 posts
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
            while (str_word_count(strip_tags($bodyContent)) < 1000) {
                $bodyContent .= "\n\n" . $faker->paragraph(rand(4, 8));
            }

            // Create the post
            $title = $faker->unique()->sentence();
            $post = Post::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'author_id' => $author->id,
                'breadcrumb' => $faker->sentence(3),
                'body_content' => $bodyContent,
                'status' => 'published',
                'published_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            // Attach 1-2 random categories to the post
            $post->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')->toArray()
            );
        }
    }
}
