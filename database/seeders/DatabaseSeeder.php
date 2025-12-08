<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use \App\Models\FaqCategory;
use \App\Models\Faq;
use Database\Factories\ResourceFactory;
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

        // News
        News::factory()->count(4)->create();

        // Resources
        ResourceCategory::factory()->count(4)->create()->each(function ($category) {
            Resource::factory()->create([
                'resource_category_id' => $category->id,
            ]);
        });

        // FAQ
        FaqCategory::factory()->count(4)->create()->each(function ($category) {
            Faq::factory()->create([
                'faq_category_id' => $category->id,
            ]);
        });

        // Programs
        $this->call(ProgramSeeder::class);
    }
}
