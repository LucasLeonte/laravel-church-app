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
        // Default user -> delete
        User::firstOrCreate(
            ['email' => 'lucas@ehb.be'],
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
                'name' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('Password!321'),
            ]
        );
    }
}
