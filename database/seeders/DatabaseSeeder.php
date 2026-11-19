<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UserAddressSeeder::class,
            SchoolClassSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            SubjectSeeder::class,
            SppPaymentSeeder::class,
            TicketSeeder::class,
            AnnouncementSeeder::class,
            AppNotificationSeeder::class,
            ChatSeeder::class,
            ProductCategorySeeder::class,
            ProductMasterDataSeeder::class,
            ProductSeeder::class,
            ProductBannerSeeder::class,
            OurTeamSeeder::class,
            BlogSeeder::class,
            MenuSeeder::class,
            EcommercePaymentShippingSeeder::class,
            NewsSeeder::class,
            EcommerceFaqSeeder::class,
            StoreServiceSeeder::class,
            IndonesiaRegionSeeder::class,
        ]);
    }
}
