<?php

namespace Database\Seeders;

use App\Models\LP\LpGallery;
use Illuminate\Database\Seeder;

class LpGallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Structured Childcare & Hygiene Training',
                'category' => 'Training Activities',
                'date' => 'October 2026',
                'description' => 'Hands-on classroom module covering infant safety, clean nursery practices, and residential care standards.',
                'image' => 'lp-assets/images/gallery/gallery-1.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Client Interview & Candidate Matching',
                'category' => 'Recruitment Activities',
                'date' => 'October 2026',
                'description' => 'Consultants facilitating pre-placement interviews between hiring families and screened candidates.',
                'image' => 'lp-assets/images/gallery/gallery-2.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Pre-Placement Household Orientation',
                'category' => 'Worker Preparation',
                'date' => 'September 2026',
                'description' => 'Final readiness briefing aligning communication habits, work schedules, and family etiquette prior to deployment.',
                'image' => 'lp-assets/images/gallery/gallery-3.jpg',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Elderly Care & Vital Monitoring Readiness',
                'category' => 'Training Activities',
                'date' => 'September 2026',
                'description' => 'Caregiver and senior companion candidates completing blood pressure observation and mobility assistance training.',
                'image' => 'lp-assets/images/gallery/gallery-4.jpg',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Bekasi Placement Desk Operations',
                'category' => 'Company Activities',
                'date' => 'August 2026',
                'description' => 'Daily candidate coordination and client consultation activities at our Jatiasih headquarters.',
                'image' => 'lp-assets/images/gallery/gallery-5.jpg',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Early Childhood Engagement Workshop',
                'category' => 'Worker Preparation',
                'date' => 'August 2026',
                'description' => 'Nanny candidates practicing toddler communication, nutritious snack preparation, and structured routines.',
                'image' => 'lp-assets/images/gallery/gallery-6.jpg',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Annual Hospitality & Ethics Refresher',
                'category' => 'Events',
                'date' => 'July 2026',
                'description' => 'Gathering focused on professional ethics, culinary hygiene, and service pride among active personnel.',
                'image' => 'lp-assets/images/gallery/gallery-7.jpg',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Candidate Verification & Clearance Desk',
                'category' => 'Documentation',
                'date' => 'July 2026',
                'description' => 'Administrative screening, family reference validation, and placement contract documentation.',
                'image' => 'lp-assets/images/gallery/gallery-8.jpg',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            LpGallery::query()->firstOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
