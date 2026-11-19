<?php

namespace Database\Seeders;

use App\Models\LP\LpFaq;
use Illuminate\Database\Seeder;

class LpFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'question' => 'How can I find a suitable worker?',
                'answer' => 'You can browse our Worker Catalog using filters for category, age range, experience, origin, and availability—or message our recruitment consultants via WhatsApp for curated candidate recommendations.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'What worker categories are available?',
                'answer' => 'We provide Domestic Helpers (ART), Infant Babysitters, Toddler Nannies, Caregivers, Elderly Care specialists, as well as Other Manpower Services including Private Drivers and Family Cooks.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'How does the recruitment process work?',
                'answer' => 'Our process follows four clear stages: initial Consultation to understand your needs, Candidate Selection with video or office interviews, Placement coordination, and follow-up Assistance after arrival.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Can I choose a specific candidate?',
                'answer' => 'Yes. Each worker profile displays a unique candidate code. Click "View Details" to review their background and skills, then use the WhatsApp button to inquire specifically about that candidate.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'How can I contact the recruitment team?',
                'answer' => 'You can reach our consultants immediately via the WhatsApp buttons across the site, call our office telephone line, email us, or visit our Jatiasih, Bekasi office during business hours.',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'What information is available in worker profiles?',
                'answer' => 'Public worker profiles present recruitment-relevant details such as candidate code, age, regional origin, education, work experience, skills, languages, and placement preferences while keeping sensitive personal documents strictly private.',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            LpFaq::query()->firstOrCreate(
                ['question' => $item['question']],
                $item
            );
        }
    }
}
