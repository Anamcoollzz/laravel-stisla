<?php

namespace Database\Seeders;

use App\Models\LP\CompanyMilestone;
use Illuminate\Database\Seeder;

class CompanyMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'year' => '2016',
                'title' => 'Agency Foundation in Bekasi',
                'description' => 'Established to connect households in Greater Jakarta with screened, dependable domestic staff.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'year' => '2019',
                'title' => 'Practical Training Program',
                'description' => 'Launched structured training covering infant hygiene, eldercare, and modern household standards.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'year' => '2022',
                'title' => 'Expanded Placement Coverage',
                'description' => 'Extended coordinated placement services across Jabodetabek, Bandung, and major Indonesian cities.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'year' => '2026',
                'title' => 'Digital Candidate Directory',
                'description' => 'Introduced transparent online candidate profiles and streamlined WhatsApp video interview scheduling.',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            CompanyMilestone::query()->firstOrCreate(
                ['year' => $item['year'], 'title' => $item['title']],
                $item
            );
        }
    }
}
