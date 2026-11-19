<?php

namespace Database\Seeders;

use App\Models\LP\VisionMission;
use Illuminate\Database\Seeder;

class VisionMissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (VisionMission::query()->exists()) {
            return;
        }

        VisionMission::query()->create([
            'vision_eyebrow' => 'Our Vision',
            'vision_title' => 'Setting a Dependable Standard in Manpower Placement',
            'vision_description' => 'To be recognized as a trusted, people-centered manpower recruitment partner—delivering qualified workers and fostering respectful, harmonious working relationships in every home.',
            'mission_eyebrow' => 'Our Mission',
            'mission_title' => 'How We Serve Families & Candidates',
            'mission_points' => [
                'Apply disciplined multi-step candidate screening, reference verification, and readiness checks before placement.',
                'Provide practical orientation in residential hygiene, infant care, senior assistance, and communication etiquette.',
                'Communicate candidate qualifications transparently while protecting sensitive personal privacy.',
                'Maintain responsive consultation and structured follow-up assistance after placement day.',
            ],
        ]);
    }
}
