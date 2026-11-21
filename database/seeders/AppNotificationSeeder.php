<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->first();

        $items = [
            [
                'title' => 'Selamat Datang di Panel Admin',
                'message' => 'Gunakan menu di samping untuk mengelola data aplikasi dan memantau aktivitas terbaru.',
                'type' => 'info',
                'icon' => 'fas fa-bell',
                'action_url' => null,
                'user_id' => null,
                'is_broadcast' => true,
            ],
            [
                'title' => 'Pembaruan Fitur Keamanan',
                'message' => 'Sistem logging dan mode demo telah aktif untuk menjaga keamanan data.',
                'type' => 'primary',
                'icon' => 'fas fa-shield-alt',
                'action_url' => null,
                'user_id' => null,
                'is_broadcast' => true,
            ],
        ];

        if ($admin) {
            $items[] = [
                'title' => 'Akun Admin Siap Digunakan',
                'message' => 'Halo '.$admin->name.', seluruh hak akses administrator sudah aktif.',
                'type' => 'success',
                'icon' => 'fas fa-check',
                'action_url' => null,
                'user_id' => $admin->id,
                'is_broadcast' => false,
            ];
        }

        foreach ($items as $item) {
            AppNotification::query()->firstOrCreate(
                ['title' => $item['title'], 'user_id' => $item['user_id']],
                array_merge($item, ['created_by' => $admin?->id])
            );
        }
    }
}
