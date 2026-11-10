<?php

namespace Database\Seeders;

use App\Models\OurTeam;
use Illuminate\Database\Seeder;

class OurTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'name' => 'Olivia Smith',
                'position' => 'Founder',
                'image' => 'mantu/assets/img/user/1.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'William Dalin',
                'position' => 'Co-Founder',
                'image' => 'mantu/assets/img/user/2.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Emma Welson',
                'position' => 'Manager',
                'image' => 'mantu/assets/img/user/3.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Benjamin Martin',
                'position' => 'Leader',
                'image' => 'mantu/assets/img/user/4.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Amelia Martin',
                'position' => 'Leader',
                'image' => 'mantu/assets/img/user/5.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Margret Smith',
                'position' => 'Marketing',
                'image' => 'mantu/assets/img/user/7.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Julee Beans',
                'position' => 'Manager',
                'image' => 'mantu/assets/img/user/8.jpg',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($members as $member) {
            OurTeam::query()->firstOrCreate(
                ['name' => $member['name'], 'position' => $member['position']],
                $member
            );
        }
    }
}
