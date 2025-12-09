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

        $resourceCategoryIds = ResourceCategory::pluck('id')->toArray();
        if (!empty($resourceCategoryIds)) {
            Resource::factory()->count(4)->make()->each(function ($resource) use ($resourceCategoryIds) {
                $resource->resource_category_id = $resourceCategoryIds[array_rand($resourceCategoryIds)];
                $resource->save();
            });
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
