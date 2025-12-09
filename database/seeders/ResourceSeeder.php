<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResourceCategory;
use App\Models\Resource;
use App\Models\Comment;
use App\Models\User;

class ResourceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resourceCategories = [
            'Systematic Theology' => 'Resources exploring doctrine and the organized study of Christian beliefs.',
            'Biblical Theology' => 'Resources focusing on theological themes and narratives across the Bible.',
            'Apologetics' => 'Defenses of the Christian faith and answers to common objections.',
            'Church History' => 'Materials covering the historical development of the church and its movements.',
            'Denominations' => 'Information about different Christian denominations and their distinctions.',
            'Pastoral' => 'Pastoral care, counseling, and ministry resources for church leaders.',
            'Sermons' => 'Collections of sermon transcripts, outlines, and recordings for preaching and study.',
            'Science' => 'Discussions and resources at the intersection of science and faith.',
            'Philosophy' => 'Philosophical works and reflections relevant to theology and apologetics.',
            'Devotional' => 'Daily devotionals and spiritual formation materials for personal growth.',
            'Revival' => 'Resources related to revival movements, spiritual renewal, and awakening.',
        ];

        foreach ($resourceCategories as $name => $description) {
            ResourceCategory::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Create 5 articles
        $articles = [
            'Sermons' => [
                'title' => 'The High Cost of Following Christ',
                'content' => "In this classic sermon-style reflection, Billy Graham calls listeners back to the centrality of the cross — its meaning for sin, forgiveness, and the call to follow Jesus. The piece combines clear gospel proclamation with pastoral urgency, inviting a personal response and practical steps for new believers.",
                'author' => 'Billy Graham',
                'image' => 'images/resources/billy-graham.jpg',
                'link' => 'https://www.youtube.com/watch?v=-vOGxGce3OM',
                'published_at' => now()->subDays(5),
            ],
            'Apologetics' => [
                'title' => 'Does God Exist?',
                'content' => "Philosopher and theologian William Lane Craig presents a compelling case for the existence of God, drawing on cosmological, teleological, and moral arguments. This article breaks down complex philosophical concepts into accessible language, making it suitable for both skeptics and believers seeking a deeper understanding of faith.",
                'author' => 'William Lane Craig',
                'image' => 'images/resources/william-lane-craig.jpg',
                'link' => 'https://www.reasonablefaith.org/writings/popular-writings/existence-nature-of-god/does-god-exist1',
                'published_at' => now()->subDays(10),
            ],
            'Revival' => [
                'title' => 'Pentecost At Any Cost',
                'content' => "Leonard Ravenhill challenges believers to seek genuine revival with unwavering commitment. In this stirring article, he emphasizes that true revival demands total surrender, fervent prayer, and a willingness to pay any price for spiritual renewal. Ravenhill's passionate call to action inspires readers to pursue a deeper relationship with God and to ignite revival in their own lives and communities.",
                'author' => 'Leonard Ravenhill',
                'image' => 'images/resources/leonard-ravenhill.jpg',
                'link' => 'http://www.ravenhill.org/prayer.htm',
                'published_at' => now()->subDays(20),
            ],
            'Devotional' => [
                'title' => 'Morning Breath: A Short Devotional for Busy Days',
                'content' => "A five-minute devotional designed to center your day. Includes a short Scripture reading, a brief reflection to apply it practically, and a one-line prayer for everyday struggles — work, relationships, and rest. Perfect for those who want spiritual rhythm without long study time.",
                'author' => 'Alice Johnson',
                'image' => 'default-resources-image.jpg',
                'link' => null,
                'published_at' => now()->subDays(2),
            ],
            'Systematic Theology' => [
                'title' => 'Doctrine of The Trinity',
                'content' => "An overview of the Christian doctrine of the Trinity, exploring the biblical foundations, historical development, and theological significance of understanding God as Father, Son, and Holy Spirit. This article delves into key scriptural passages and addresses common misconceptions about this central tenet of Christian faith.",
                'author' => 'Wayne Grudem',
                'image' => 'images/resources/trinity.jpg',
                'link' => 'https://www.biblicaltraining.org/library/trinity-by-wayne-grudem',
                'published_at' => now()->subDays(30),
            ],
        ];

        foreach ($articles as $categoryName => $data) {
            $category = ResourceCategory::firstWhere('name', $categoryName);
            if (! $category) {
                // skip if category was not created for some reason
                continue;
            }

            Resource::firstOrCreate(
                ['title' => $data['title']],
                [
                    'content' => $data['content'],
                    'author' => $data['author'],
                    'image' => $data['image'],
                    'link' => $data['link'],
                    'published_at' => $data['published_at'],
                    'resource_category_id' => $category->id,
                ]
            );
        }

        // Attach comments to 3 recent resources (if users exist)
        $sampleResources = Resource::orderBy('created_at', 'desc')->take(3)->get();
        if ($sampleResources->isNotEmpty()) {
            $admin = User::firstWhere('email', 'admin@ehb.be');
            $alice = User::firstWhere('email', 'alice.johnson@example.com');
            $bob = User::firstWhere('email', 'bob.smith@example.com');

            if (isset($sampleResources[0]) && $alice) {
                $r0 = $sampleResources[0];
                Comment::firstOrCreate([
                    'user_id' => $alice->id,
                    'resource_id' => $r0->id,
                    'body' => 'This was a really helpful overview — clarified several questions I had about the doctrine. Thanks for sharing!'
                ]);
            }

            if (isset($sampleResources[1]) && $bob) {
                $r1 = $sampleResources[1];
                Comment::firstOrCreate([
                    'user_id' => $bob->id,
                    'resource_id' => $r1->id,
                    'body' => 'I appreciated the historical context here. Would love references to primary sources if anyone has recommendations.'
                ]);
            }

            if (isset($sampleResources[2]) && $admin) {
                $r2 = $sampleResources[2];
                Comment::firstOrCreate([
                    'user_id' => $admin->id,
                    'resource_id' => $r2->id,
                    'body' => 'We plan to add a follow-up guide to this resource next month — stay tuned.'
                ]);
            }
        }
    }
}
