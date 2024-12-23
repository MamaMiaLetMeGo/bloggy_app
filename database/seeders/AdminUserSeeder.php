<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsers = [
            [
                'name' => 'Charles Gendron',
                'email' => 'gitcommitcg@gmail.com',
                'password' => 'password123',
                'is_admin' => true,
            ],
        ];

        foreach ($adminUsers as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'email_verified_at' => now(),
                    'is_admin' => true,
                ]
            );
        }

        // Delete the example admin users
        User::whereIn('email', ['admin@example.com', 'admin2@example.com'])->delete();

        $this->command->info('Admin users created or updated successfully.');
    }
}