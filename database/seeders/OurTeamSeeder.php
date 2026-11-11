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
                'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'William Dalin',
                'position' => 'Co-Founder',
                'image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Emma Welson',
                'position' => 'Manager',
                'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Benjamin Martin',
                'position' => 'Leader',
                'image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Amelia Martin',
                'position' => 'Leader',
                'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Margret Smith',
                'position' => 'Marketing',
                'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Julee Beans',
                'position' => 'Manager',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=600&auto=format&fit=crop&q=80',
                'twitter_url' => 'https://twitter.com',
                'facebook_url' => 'https://facebook.com',
                'linkedin_url' => 'https://linkedin.com',
                'order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($members as $member) {
            OurTeam::query()->updateOrCreate(
                ['name' => $member['name'], 'position' => $member['position']],
                $member
            );
        }
    }
}
