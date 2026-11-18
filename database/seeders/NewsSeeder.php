<?php

namespace Database\Seeders;

use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => 'Teknologi',
                'description' => 'Berita terkini seputar perkembangan teknologi, AI, software, dan gadget.',
            ],
            [
                'name' => 'Bisnis & Ekonomi',
                'description' => 'Informasi pasar modal, startup, UMKM, dan tren ekonomi global.',
            ],
            [
                'name' => 'Gaya Hidup',
                'description' => 'Artikel seputar kesehatan, kuliner, traveling, dan gaya hidup modern.',
            ],
            [
                'name' => 'Olahraga',
                'description' => 'Update skor, turnamen, dan berita dunia olahraga nasional maupun internasional.',
            ],
            [
                'name' => 'Hiburan',
                'description' => 'Berita film, musik, seni, dan selebritas terkini.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = NewsCategory::query()->firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description']]
            );
        }

        $publisherId = User::query()->first()?->id;

        $articles = [
            [
                'title' => 'Transformasi Digital UMKM Indonesia Makin Cepat di Tahun 2026',
                'category' => 'Bisnis & Ekonomi',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'content' => "Perkembangan teknologi pembayaran digital dan platform e-commerce mendorong jutaan pelaku UMKM di Indonesia untuk beralih ke ekosistem digital.\n\nDengan dukungan infrastruktur internet yang semakin merata, pelaku usaha kini dapat menjangkau pasar nasional maupun ekspor secara lebih efisien.",
            ],
            [
                'title' => 'Kecerdasan Buatan Mulai Banyak Diterapkan di Sektor Pendidikan',
                'category' => 'Teknologi',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'content' => "Berbagai institusi pendidikan mulai mengadopsi asisten belajar berbasis AI untuk membantu personalisasi materi bagi siswa.\n\nPara pengajar menekankan bahwa teknologi tetap berfungsi sebagai alat bantu untuk meningkatkan kualitas interaksi belajar-mengajar.",
            ],
            [
                'title' => 'Tips Menjaga Produktivitas Kerja di Era Hybrid Working',
                'category' => 'Gaya Hidup',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'content' => "Bekerja secara hybrid membutuhkan manajemen waktu yang baik serta batasan yang jelas antara waktu kerja dan istirahat.\n\nMenentukan ruang kerja khusus di rumah dan rutin melakukan peregangan dapat membantu menjaga fokus sepanjang hari.",
            ],
            [
                'title' => 'Timnas Bulutangkis Indonesia Siap Tampil di Turnamen Tur Eropa',
                'category' => 'Olahraga',
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'content' => "Persiapan intensif terus dilakukan oleh para atlet pelatnas menjelang rangkaian turnamen tur Eropa bulan ini.\n\nPelatih memastikan kondisi fisik seluruh pemain berada dalam performa puncak.",
            ],
            [
                'title' => 'Festival Film Independen Nusantara Hadirkan Puluhan Karya Baru',
                'category' => 'Hiburan',
                'status' => 'draft',
                'published_at' => null,
                'content' => 'Festival tahunan ini menampilkan karya-karya sineas muda dari berbagai daerah di Indonesia dengan beragam genre yang segar dan inspiratif.',
            ],
        ];

        foreach ($articles as $article) {
            $slug = Str::slug($article['title']);
            News::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'news_category_id' => $categories[$article['category']]->id ?? null,
                    'publisher_user_id' => $publisherId,
                    'status' => $article['status'],
                    'published_at' => $article['published_at'],
                ]
            );
        }
    }
}
