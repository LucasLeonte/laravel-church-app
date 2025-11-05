<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Database\Factories\PostFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default user -> delete
        User::firstOrCreate(
            ['email' => 'lucas@ehb.be'], //search criteria -> check if user exists
            [
                'name' => 'Lucas',
                'email_verified_at' => now(),
                'password' => Hash::make('Lucas!321'),
            ]
        );

        // Default admin
        User::firstOrCreate(
            ['email' => 'admin@ehb.be'],
            [
                'is_admin' => true,
                'name' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('Password!321'),
            ]
        );

        // Posts (news and articles)
        Post::factory()->count(10)->create();
    }
}
