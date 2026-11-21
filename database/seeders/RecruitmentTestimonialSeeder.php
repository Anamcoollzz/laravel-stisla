<?php

namespace Database\Seeders;

use App\Models\LP\RecruitmentTestimonial;
use Illuminate\Database\Seeder;

class RecruitmentTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Mrs. Ratna Wijaya',
                'location' => 'South Jakarta',
                'service' => 'Babysitter Placement',
                'quote' => 'The recruitment team listened carefully to our newborn routine needs. Our babysitter Nadia is attentive, hygienic, and communicates daily schedules clearly.',
                'avatar' => 'lp-assets/images/workers/worker-2.jpg',
                'rating' => 5,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Mr. Hendra Kusuma',
                'location' => 'Bekasi, West Java',
                'service' => 'Elderly Care Placement',
                'quote' => 'Finding a respectful caregiver for my father after surgery felt daunting until we consulted PT Permata Kasih Anugerah. Their candidate screening made us confident.',
                'avatar' => 'lp-assets/images/workers/worker-3.jpg',
                'rating' => 5,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Mrs. Clarissa Tan',
                'location' => 'BSD City, Tangerang',
                'service' => 'Domestic Helper (ART)',
                'quote' => 'Clear communication from initial WhatsApp inquiry through onboarding. Our helper Siti maintains the home thoughtfully and adapts smoothly to our family.',
                'avatar' => 'lp-assets/images/workers/worker-1.jpg',
                'rating' => 5,
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            RecruitmentTestimonial::query()->firstOrCreate(
                ['name' => $item['name'], 'service' => $item['service']],
                $item
            );
        }
    }
}
