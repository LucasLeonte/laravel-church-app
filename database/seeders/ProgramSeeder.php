<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Sunday Service',
                'description' => 'Worship, sermon and fellowship.',
                'day_of_week' => 'Sunday',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'published' => true,
            ],
            [
                'title' => 'Youth Service',
                'description' => 'Weekly meeting for young people.',
                'day_of_week' => 'Monday',
                'start_time' => '20:00',
                'end_time' => '22:00',
                'published' => true,
            ],
            [
                'title' => 'Prayer Meeting',
                'description' => 'Midweek prayer and motivations.',
                'day_of_week' => 'Tuesday',
                'start_time' => '19:30',
                'end_time' => '21:00',
                'published' => true,
            ],
            [
                'title' => 'Thursday Service',
                'description' => 'Midweek prayer, worship and sermon.',
                'day_of_week' => 'Thursday',
                'start_time' => '19:30',
                'end_time' => '21:30',
                'published' => true,
            ],
            [
                'title' => 'Choir Practice',
                'description' => 'Rehearsal for the choir.',
                'day_of_week' => 'Saturday',
                'start_time' => '19:00',
                'end_time' => '21:00',
                'published' => true,
            ],
        ];

        foreach ($items as $item) {
            Program::create($item);
        }
    }
}

