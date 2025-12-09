<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;

class NewsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::create([
            'title' => 'Carol Evening with Advent Candlelights',
            'image' => 'images/news/advent-candlelight.jpg',
            'content' => "Join us for a warm evening of carols, scripture readings, and a candlelight service as we begin the Advent season together. Families and visitors are welcome — we'll have hot chocolate for the children and a short message from Pastor Daniel about hope and renewal. The service will be followed by light refreshments in the fellowship hall.",
            'published_at' => now()->subDays(8),
            'author' => 'Pastor Daniel Smith',
        ]);

        News::create([
            'title' => 'Volunteer Drive: Winter Outreach 2025',
            'image' => 'images/news/winter-outreach.jpg',
            'content' => "Our annual Winter Outreach begins next week. We are collecting warm coats, blankets, and non-perishable food for local families in need. Volunteers are needed for sorting, delivery, and the distribution event on Saturday morning. Please sign up at the welcome desk or email outreach@ourchurch.org to get involved.",
            'published_at' => now()->subDays(15),
            'author' => 'Community Outreach Team',
        ]);

        News::create([
            'title' => 'Remembering Billy Graham: A Special Message',
            'image' => 'images/news/billy-graham.jpg',
            'content' => "This week we pause to remember the life and legacy of Reverend Billy Graham, a pastor whose faithful ministry reached millions with a clear proclamation of the Gospel. As we reflect on his witness, we're reminded of the importance of humility, prayer, and living out the hope of Christ in word and deed. Join us after the Sunday service for a short special message where our pastors will reflect on his life, celebrate his commitment to sharing the Good News, and encourage us to follow his example of compassion and faithfulness.",
            'published_at' => now()->subDays(22),
            'author' => 'Church Office',
        ]);

        News::create([
            'title' => 'Christmas Worship Schedule & Live Stream Info',
            'image' => 'images/news/christmas-schedule.jpg',
            'content' => "Plan your holiday worship with us — Christmas Eve family service at 5pm and Christmas Day morning worship at 10am. All services will be live-streamed on our website and YouTube channel for those who cannot attend in person.",
            'published_at' => now()->subDays(2),
            'author' => 'Church Office',
        ]);
    }
}
