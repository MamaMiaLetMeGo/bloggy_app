<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
        ]);

        // Only create test users if we're in development
        if (app()->environment('local')) {
            \App\Models\User::factory(5)->create();
        }
    }
}
