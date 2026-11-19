<?php

namespace Database\Seeders;

use App\Models\LP\WhyChooseUs;
use Illuminate\Database\Seeder;

class WhyChooseUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Carefully Selected Candidates',
                'description' => 'Applicants undergo identity validation, family reference confirmation, and behavioral interviews before profile listing.',
                'icon' => 'shield-check',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Professional Recruitment Process',
                'description' => 'Structured matching ensures families interview candidates aligned with their exact household needs and schedule.',
                'icon' => 'clipboard-check',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Qualified and Trained Workers',
                'description' => 'Candidates receive practical orientation in residential hygiene, childcare safety, eldercare etiquette, and home appliances.',
                'icon' => 'graduation-cap',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Transparent Communication',
                'description' => 'Clear candidate backgrounds, explicit preference summaries, and straightforward service terms from day one.',
                'icon' => 'file-check',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Responsive Customer Support',
                'description' => 'Dedicated placement consultants ready via WhatsApp and office appointments to guide selection and onboarding.',
                'icon' => 'headphones',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Placement Assistance',
                'description' => 'Post-placement check-ins and practical mediation help both families and workers adapt smoothly together.',
                'icon' => 'handshake',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            WhyChooseUs::query()->firstOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
