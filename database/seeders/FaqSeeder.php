<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqCategory;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqCategories = [
            'Systematic Theology' => 'Questions about core doctrines and theological systems.',
            'Biblical Theology' => 'Questions about biblical themes, interpretation, and context.',
            'Apologetics' => 'Common objections and concise answers defending the faith.',
            'Church History' => 'Questions regarding events, movements, and figures in church history.',
            'Denominations' => 'Questions about practices and beliefs of different Christian traditions.',
            'Worship' => 'Questions about worship services, liturgy, music, and corporate practice.',
        ];

        foreach ($faqCategories as $name => $description) {
            FaqCategory::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        $faqCategoryIds = FaqCategory::pluck('id')->toArray();
        if (!empty($faqCategoryIds)) {
            Faq::factory()->count(4)->make()->each(function ($faq) use ($faqCategoryIds) {
                $faq->faq_category_id = $faqCategoryIds[array_rand($faqCategoryIds)];
                $faq->save();
            });
        }
    }
}

