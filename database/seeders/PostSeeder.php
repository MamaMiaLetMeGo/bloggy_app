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
        $author = User::where('email', 'gitcommitcg@gmail.coom')->first();
        if (!$author) {
            $author = User::factory()->create([
                'name' => 'Charles Gendron',
                'email' => 'gitcommitcg@gmail.coom',
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
            $post = Post::create([
                'title' => $faker->unique()->sentence(),
                'slug' => Str::slug($faker->unique()->sentence()),
                'body_content' => $bodyContent,
                'reading_time' => rand(3, 15),
                'status' => 'published',
                'published_date' => Carbon::now()->subDays(rand(1, 30)),
                'author_id' => $author->id,
            ]);

            // Attach random categories (1-3)
            $post->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Add random number of likes (0-50)
            $numLikes = rand(0, 50);
            for ($k = 0; $k < $numLikes; $k++) {
                $post->likes()->create([
                    'ip_address' => $faker->ipv4
                ]);
            }

            // Add random number of comments (0-20)
            $numComments = rand(0, 20);
            for ($k = 0; $k < $numComments; $k++) {
                $post->comments()->create([
                    'author_name' => $faker->name,
                    'author_email' => $faker->email,
                    'content' => $faker->paragraph(rand(1, 3)),
                    'is_approved' => true,
                    'created_at' => Carbon::now()->subDays(rand(0, 30))->addHours(rand(1, 24))
                ]);
            }
        }
    }
}
