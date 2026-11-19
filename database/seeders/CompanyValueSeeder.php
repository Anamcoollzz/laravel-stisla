<?php

namespace Database\Seeders;

use App\Models\LP\CompanyValue;
use Illuminate\Database\Seeder;

class CompanyValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Integrity & Honesty',
                'description' => 'Transparent screening and accurate candidate representation build lasting trust with every household.',
                'icon' => 'shield-check',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Compassionate Care',
                'description' => 'We place helpers and caregivers who genuinely respect children, seniors, and family well-being.',
                'icon' => 'heart-handshake',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Professional Readiness',
                'description' => 'Practical hospitality, hygiene, and safety orientation prepares candidates before deployment.',
                'icon' => 'award',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Accountable Support',
                'description' => 'Our consultants remain reachable after placement day to support smooth long-term adaptation.',
                'icon' => 'headphones',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            CompanyValue::query()->firstOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
