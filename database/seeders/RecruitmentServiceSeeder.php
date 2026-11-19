<?php

namespace Database\Seeders;

use App\Models\LP\RecruitmentService;
use Illuminate\Database\Seeder;

class RecruitmentServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'slug' => 'domestic-helper',
                'category_key' => 'ART',
                'title' => 'Domestic Helper (ART)',
                'subtitle' => 'Household Upkeep & Culinary Support',
                'icon' => 'home',
                'image' => 'lp-assets/images/services/art.jpg',
                'short_description' => 'Dependable domestic helpers trained in daily housekeeping, laundry care, and healthy family meal preparation.',
                'description' => 'Our Domestic Helpers (Asisten Rumah Tangga) are trained in structured residential cleaning, appliance operation, fabric care, and hygienic daily cooking to keep busy households running smoothly.',
                'responsibilities' => [
                    'Daily sweeping, mopping, dusting, and room sanitization',
                    'Laundry sorting, washing, steam ironing, and wardrobe care',
                    'Preparing hygienic everyday Indonesian home meals',
                    'Managing kitchen cleanliness and household pantry routines',
                ],
                'skills' => [
                    'Residential Housekeeping',
                    'Home Cooking',
                    'Modern Appliances',
                    'Laundry & Ironing',
                ],
                'benefits' => [
                    'Verified identity and family reference checks',
                    'Structured pre-placement housekeeping orientation',
                    'Dedicated consultant follow-up after placement',
                ],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'slug' => 'babysitter',
                'category_key' => 'Babysitter',
                'title' => 'Babysitter',
                'subtitle' => 'Newborn & Infant Care Specialists',
                'icon' => 'baby',
                'image' => 'lp-assets/images/services/babysitter.jpg',
                'short_description' => 'Attentive babysitters skilled in newborn hygiene, bottle sterilization, sleep schedules, and infant feeding routines.',
                'description' => 'Prepared specifically for newborns and infants (0–24 months), our babysitters are carefully selected for warmth, patience, nursery hygiene, and calm attentiveness.',
                'responsibilities' => [
                    'Safe infant bathing, dressing, and daily hygiene care',
                    'Sterilizing feeding bottles and preparing infant formula or MPASI',
                    'Tracking feeding, nap, and diaper routines accurately',
                    'Maintaining clean nursery spaces and baby laundry',
                ],
                'skills' => [
                    'Infant Bathing & Hygiene',
                    'Bottle Sterilization',
                    'MPASI Prep',
                    'Sleep Routine Support',
                ],
                'benefits' => [
                    'Health readiness and medical clearance on file',
                    'Trained in infant safety and soothing methods',
                    'Live video or office interview before placement',
                ],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'slug' => 'nanny',
                'category_key' => 'Nanny',
                'title' => 'Nanny',
                'subtitle' => 'Toddler & Childhood Companion',
                'icon' => 'book-open',
                'image' => 'lp-assets/images/services/nanny.jpg',
                'short_description' => 'Caring nannies focused on active toddlers, school routines, creative play, and healthy daily habits.',
                'description' => 'Tailored for toddlers and school-aged children, our nannies balance safe supervision with structured routines, age-appropriate play, nutritious snacks, and supportive communication.',
                'responsibilities' => [
                    'Preparing nutritious child meals, snacks, and school lunchboxes',
                    'Guiding positive hygiene, tidy-up, and bedtime habits',
                    'Supervising creative play, reading, and outdoor walks',
                    'Coordinating daily schedules smoothly with parents',
                ],
                'skills' => [
                    'Early Childhood Routines',
                    'Creative Play',
                    'Child Nutrition',
                    'Patient Communication',
                ],
                'benefits' => [
                    'Candidates screened for patience and child-friendly demeanor',
                    'Optional bilingual or PAUD-educated profiles available',
                    'Ongoing consultant assistance during adaptation',
                ],
                'order' => 3,
                'is_active' => true,
            ],
            [
                'slug' => 'caregiver',
                'category_key' => 'Caregiver',
                'title' => 'Caregiver',
                'subtitle' => 'Home Health & Recovery Assistance',
                'icon' => 'heart-pulse',
                'image' => 'lp-assets/images/services/caregiver.jpg',
                'short_description' => 'Trained caregivers supporting post-hospital recovery, vital sign observation, and daily mobility assistance.',
                'description' => 'Our Caregivers assist clients needing attentive day-to-day home support following hospital discharge or ongoing physical recovery, working respectfully alongside family members.',
                'responsibilities' => [
                    'Monitoring blood pressure, temperature, and daily wellness logs',
                    'Assisting with safe wheelchair transfer and gentle movement',
                    'Reminding prescribed medication schedules accurately',
                    'Supporting personal grooming, bathing, and hydration routines',
                ],
                'skills' => [
                    'Vital Monitoring',
                    'Mobility Transfer',
                    'Medication Reminders',
                    'Recovery Support',
                ],
                'benefits' => [
                    'Many candidates hold SMK Kesehatan or D3 Nursing backgrounds',
                    'Screened for composure, empathy, and physical readiness',
                    'Clear placement terms and responsive support desk',
                ],
                'order' => 4,
                'is_active' => true,
            ],
            [
                'slug' => 'elderly-care',
                'category_key' => 'Elderly Care',
                'title' => 'Elderly Care',
                'subtitle' => 'Compassionate Senior Companionship',
                'icon' => 'heart-handshake',
                'image' => 'lp-assets/images/services/elderly-care.jpg',
                'short_description' => 'Respectful senior companions dedicated to comfort, nutrition support, fall prevention, and warm daily engagement.',
                'description' => 'Designed specifically for aging parents and grandparents, our Elderly Care specialists provide respectful companionship, balanced senior meal prep, and gentle daily living assistance.',
                'responsibilities' => [
                    'Providing warm conversation and attentive daily companionship',
                    'Preparing low-sodium, diabetic-friendly, or soft senior meals',
                    'Assisting with safe walking, seating, and fall prevention',
                    'Keeping bedrooms fresh, comfortable, and clutter-free',
                ],
                'skills' => [
                    'Senior Companionship',
                    'Special Diet Prep',
                    'Fall Prevention',
                    'Dementia Patience',
                ],
                'benefits' => [
                    'Calm, mature candidates matched for senior comfort',
                    'Verified reference checks and medical screening',
                    'Regular follow-up check-ins from our care team',
                ],
                'order' => 5,
                'is_active' => true,
            ],
            [
                'slug' => 'other-manpower',
                'category_key' => 'Other',
                'title' => 'Other Manpower Services',
                'subtitle' => 'Private Drivers & Family Cooks',
                'icon' => 'briefcase',
                'image' => 'lp-assets/images/services/other-manpower.jpg',
                'short_description' => 'Vetted private household chauffeurs, family cooks, and specialized residential support staff.',
                'description' => 'For households requiring broader domestic support, we recruit dependable private drivers, culinary household staff, and estate caretakers selected for discretion and reliability.',
                'responsibilities' => [
                    'Safe, punctual family commutes, school runs, and errands',
                    'Maintaining clean, serviced family vehicles and route planning',
                    'Planning weekly family menus and maintaining food hygiene',
                    'Upholding strict household privacy and professional etiquette',
                ],
                'skills' => [
                    'Defensive Driving (SIM A)',
                    'Culinary Planning',
                    'Route Familiarity',
                    'Discreet Etiquette',
                ],
                'benefits' => [
                    'Verified license and employment history checks',
                    'Clear job scope matching before deployment',
                    'Flexible live-in or daily commute arrangements',
                ],
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            RecruitmentService::query()->firstOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
