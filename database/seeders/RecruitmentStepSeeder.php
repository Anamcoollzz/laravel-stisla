<?php

namespace Database\Seeders;

use App\Models\LP\RecruitmentStep;
use Illuminate\Database\Seeder;

class RecruitmentStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $steps = [
            [
                'step' => '01',
                'title' => 'Consultation',
                'description' => "Understand the client's needs, household dynamics, preferred skills, and placement schedule.",
                'icon' => 'message-square',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'step' => '02',
                'title' => 'Candidate Selection',
                'description' => 'Present suitable worker candidates and coordinate video or face-to-face interviews.',
                'icon' => 'user-check',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'step' => '03',
                'title' => 'Placement',
                'description' => 'Coordinate transparent placement documentation and structured candidate onboarding.',
                'icon' => 'briefcase',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'step' => '04',
                'title' => 'Assistance',
                'description' => 'Provide follow-up support after placement to foster long-term household harmony.',
                'icon' => 'life-buoy',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($steps as $item) {
            RecruitmentStep::query()->firstOrCreate(
                ['step' => $item['step'], 'title' => $item['title']],
                $item
            );
        }
    }
}
