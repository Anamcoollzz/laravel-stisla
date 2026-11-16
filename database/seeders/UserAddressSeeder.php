<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->take(5)->get();

        if ($users->isEmpty()) {
            return;
        }

        $templates = [
            [
                'label' => 'Rumah',
                'address_line' => 'Jl PB Sudirman Dusun Krajan Tanggul',
                'city' => 'Jember',
                'state' => 'Jawa Timur',
                'postal_code' => '68155',
                'notes' => 'Rumah pagar hitam di depan masjid',
                'is_default' => true,
            ],
            [
                'label' => 'Kantor',
                'address_line' => 'Jl. Jend. Sudirman Kav. 45',
                'city' => 'Jakarta Selatan',
                'state' => 'DKI Jakarta',
                'postal_code' => '12930',
                'notes' => 'Titip ke resepsionis lantai 3',
                'is_default' => false,
            ],
        ];

        foreach ($users as $user) {
            foreach ($templates as $index => $template) {
                UserAddress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'label' => $template['label'],
                    ],
                    [
                        'recipient_name' => $user->name,
                        'phone_number' => $user->phone ?: '6285322778935',
                        'address_line' => $index === 0 && $user->address ? $user->address : $template['address_line'],
                        'city' => $index === 0 && $user->city ? $user->city : $template['city'],
                        'state' => $index === 0 && $user->state ? $user->state : $template['state'],
                        'postal_code' => $index === 0 && $user->postal_code ? $user->postal_code : $template['postal_code'],
                        'notes' => $template['notes'],
                        'is_default' => $template['is_default'],
                    ]
                );
            }
        }
    }
}
