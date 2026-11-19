<?php

namespace Database\Seeders;

use App\Models\LP\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultFooterDescription = 'A professional manpower recruitment and placement company connecting households and businesses with carefully selected Domestic Helpers (ART), Babysitters, Nannies, Caregivers, and Elderly Care specialists.';

        $existing = CompanyProfile::query()->first();
        if ($existing) {
            if (blank($existing->footer_description)) {
                $existing->update(['footer_description' => $defaultFooterDescription]);
            }

            return;
        }

        CompanyProfile::query()->create([
            'name' => config('company.name', 'Permata Kasih Anugerah'),
            'legal_name' => config('company.legal_name', 'PT Permata Kasih Anugerah'),
            'tagline' => config('company.tagline', 'Trusted Manpower Recruitment & Family Care Placement'),
            'footer_description' => $defaultFooterDescription,
            'license_number' => config('company.license_number', 'Official LPK License No. 563/LPK-BKS/2018'),
            'whatsapp_number' => config('company.whatsapp_number', '6282299109766'),
            'phone_display' => config('company.phone_display', '+62 822-9910-9766'),
            'secondary_phone' => config('company.secondary_phone', '+62 21-8243-5910'),
            'email' => config('company.email', 'consultation@permatakasihanugerah.com'),
            'address' => config('company.address', 'Perumahan Kemang IFI Graha, Jl. Seruling V No. 1, Jatirasa, Kec. Jatiasih, Kota Bekasi, Jawa Barat 17424'),
            'short_address' => config('company.short_address', 'Jatiasih, Bekasi, West Java'),
            'operating_hours_weekday' => config('company.operating_hours.weekday', 'Monday – Friday: 08:00 – 17:00 WIB'),
            'operating_hours_saturday' => config('company.operating_hours.saturday', 'Saturday: 09:00 – 15:00 WIB'),
            'operating_hours_sunday' => config('company.operating_hours.sunday', 'Sunday & Public Holidays: By Appointment (WhatsApp Active)'),
            'map_embed_url' => config('company.map_embed_url'),
            'instagram_url' => config('company.social_links.instagram'),
            'facebook_url' => config('company.social_links.facebook'),
            'youtube_url' => config('company.social_links.youtube'),
            'default_whatsapp_message' => config('company.default_whatsapp_message'),
        ]);
    }
}
