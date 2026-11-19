<?php

namespace Database\Seeders;

use App\Models\LP\CompanyIntroduction;
use Illuminate\Database\Seeder;

class CompanyIntroductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (CompanyIntroduction::query()->exists()) {
            return;
        }

        CompanyIntroduction::query()->create([
            'image' => 'lp-assets/images/general/about-training.jpg',
            'image_alt' => 'Candidates attending practical orientation at PT Permata Kasih Anugerah',
            'mission_label' => 'Our Mission',
            'mission_text' => 'Delivering dependable, carefully screened household and care professionals with transparent communication.',
            'eyebrow' => 'Company Introduction',
            'title' => 'Your Trusted Partner in Manpower Solutions',
            'paragraph_1' => 'Welcoming someone into your home or workplace requires confidence and clarity. At PT Permata Kasih Anugerah, we manage structured candidate screening, practical readiness orientation, and responsive placement coordination so you can hire with peace of mind.',
            'paragraph_2' => 'Our mission is to bridge responsible workers with households and businesses through honest candidate profiles, attentive consultation, and follow-up support after placement.',
            'feature_1_icon' => 'shield-check',
            'feature_1_title' => 'Verified Profiles',
            'feature_1_description' => 'Identity, reference, and readiness screening prior to listing.',
            'feature_2_icon' => 'handshake',
            'feature_2_title' => 'Guided Placement',
            'feature_2_description' => 'Personal consultant support from interview to onboarding.',
            'primary_button_label' => 'Learn More',
            'primary_button_url' => null,
            'whatsapp_button_label' => 'Consult Our Team',
            'whatsapp_button_message' => null,
        ]);
    }
}
