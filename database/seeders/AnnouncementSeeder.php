<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorId = User::query()->value('id');

        $items = [
            [
                'title' => 'Pemeliharaan Sistem Terjadwal Akhir Pekan',
                'slug' => 'pemeliharaan-sistem-terjadwal-akhir-pekan',
                'content' => 'Kami akan melakukan pemeliharaan server terjadwal pada hari Minggu pukul 00:00 - 03:00 WIB untuk peningkatan performa dan keamanan.',
                'type' => 'warning',
                'published_at' => now()->subDays(2),
                'expires_at' => now()->addDays(14),
                'is_active' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'Selamat Datang di Portal Admin Terbaru',
                'slug' => 'selamat-datang-di-portal-admin-terbaru',
                'content' => 'Dashboard admin kini dilengkapi fitur manajemen menu dinamis, ekspor data, dan pembaruan keamanan terbaru.',
                'type' => 'info',
                'published_at' => now()->subDays(5),
                'expires_at' => null,
                'is_active' => true,
                'is_pinned' => false,
            ],
            [
                'title' => 'Batas Akhir Pengumpulan Laporan Bulanan',
                'slug' => 'batas-akhir-pengumpulan-laporan-bulanan',
                'content' => 'Seluruh tim dimohon menyelesaikan rekapitulasi dan verifikasi data sebelum tanggal 25 bulan berjalan.',
                'type' => 'urgent',
                'published_at' => now()->subDay(),
                'expires_at' => now()->addDays(10),
                'is_active' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'Rapat Koordinasi & Evaluasi Kuartal',
                'slug' => 'rapat-koordinasi-evaluasi-kuartal',
                'content' => 'Rapat evaluasi kuartal akan dilaksanakan secara hybrid melalui ruang rapat utama dan tautan video konferensi.',
                'type' => 'event',
                'published_at' => now(),
                'expires_at' => now()->addDays(20),
                'is_active' => true,
                'is_pinned' => false,
            ],
        ];

        foreach ($items as $item) {
            Announcement::query()->updateOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, ['created_by' => $authorId])
            );
        }
    }
}
