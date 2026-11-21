<?php

namespace Database\Seeders;

use App\Models\LP\CompanyStat;
use Illuminate\Database\Seeder;

class CompanyStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'value' => '10+',
                'label' => 'Years of Experience',
                'description' => 'Sample benchmark representing dedicated domestic & care recruitment experience.',
                'icon' => 'award',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'value' => '850+',
                'label' => 'Workers Available',
                'description' => 'Pre-screened candidates across childcare, eldercare, and household support.',
                'icon' => 'users',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'value' => '2,100+',
                'label' => 'Successful Placements',
                'description' => 'Families and households matched through structured screening processes.',
                'icon' => 'check-circle-2',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'value' => '1,450+',
                'label' => 'Satisfied Clients',
                'description' => 'Returning households served across Jabodetabek and major Indonesian cities.',
                'icon' => 'heart-handshake',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            CompanyStat::query()->firstOrCreate(
                ['label' => $item['label']],
                $item
            );
        }
    }
}
