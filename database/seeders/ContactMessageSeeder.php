<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactMessage;

class ContactMessageSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactMessage::firstOrCreate(
            ['email' => 'alice@example.com', 'message' => 'I would like to know more about your programs.'],
            ['name' => 'Alice']
        );

        ContactMessage::firstOrCreate(
            ['email' => 'bob@example.com', 'message' => 'Can you provide the schedule for next Sunday?'],
            ['name' => 'Bob']
        );

        ContactMessage::firstOrCreate(
            ['email' => 'carol@example.com', 'message' => 'I am interested in volunteering.'],
            ['name' => 'Carol']
        );
    }
}

