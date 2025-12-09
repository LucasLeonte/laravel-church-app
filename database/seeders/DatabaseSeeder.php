<?php

namespace Database\Seeders;

use App\Models\User;
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

        // Default users (2)
        User::firstOrCreate(
            ['email' => 'alice.johnson@example.com'],
            [
                'name' => 'Alice Johnson',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'bob.smith@example.com'],
            [
                'name' => 'Bob Smith',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Call seeders
        $this->call([
            ProgramSeeder::class,
            NewsSeeder::class,
            ResourceSeeder::class,
            FaqSeeder::class,
            ContactMessageSeeder::class,
        ]);
    }
}
