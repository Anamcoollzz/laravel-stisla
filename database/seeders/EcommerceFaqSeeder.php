<?php

namespace Database\Seeders;

use App\Models\EcommerceFaq;
use Illuminate\Database\Seeder;

class EcommerceFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Left column
            [
                'question' => 'Bagaimana cara melakukan pemesanan produk?',
                'answer' => 'Pilih produk yang Anda inginkan, klik tombol Add To Cart, lalu buka halaman Cart atau Checkout. Pastikan Anda sudah login dan memilih alamat pengiriman, ekspedisi, serta metode pembayaran sebelum menyelesaikan pesanan.',
                'column' => 'left',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Metode pembayaran apa saja yang tersedia?',
                'answer' => 'Kami menyediakan pembayaran melalui Transfer Bank Manual (BCA, Mandiri, BRI), QRIS & E-Wallet (GoPay, OVO, Dana), serta Cash On Delivery (COD) untuk area tertentu.',
                'column' => 'left',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara mengunggah bukti pembayaran?',
                'answer' => 'Setelah checkout berhasil, buka halaman Detail Pesanan atau masuk ke menu Daftar Pesanan. Pada bagian Data Pembayaran, unggah foto/PDF bukti transfer Anda agar admin dapat segera memverifikasi pembayaran.',
                'column' => 'left',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa lama proses verifikasi pembayaran dan pengiriman?',
                'answer' => 'Verifikasi pembayaran diproses maksimal 1x24 jam kerja setelah bukti pembayaran diunggah. Pesanan yang telah terverifikasi akan segera dikemas dan dikirim sesuai estimasi kurir yang dipilih.',
                'column' => 'left',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah saya bisa membatalkan atau mengubah pesanan?',
                'answer' => 'Perubahan atau pembatalan pesanan dapat dilakukan selama status pesanan masih Menunggu Pembayaran (Pending). Silakan hubungi tim kami melalui halaman Contact Us atau Ticket List dengan menyertakan nomor pesanan.',
                'column' => 'left',
                'order' => 5,
                'is_active' => true,
            ],

            // Right column
            [
                'question' => 'Bagaimana cara melacak nomor resi pengiriman?',
                'answer' => 'Setelah pesanan dikirim oleh admin, nomor resi dan bukti resi pengiriman akan tampil otomatis pada halaman Detail Pesanan Anda dan dikirimkan melalui notifikasi email.',
                'column' => 'right',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana kebijakan pengembalian dana (Refund)?',
                'answer' => 'Pengajuan refund berlaku maksimal 7 hari setelah barang diterima apabila produk cacat produksi, rusak saat pengiriman, atau tidak sesuai pesanan, disertai video unboxing yang jelas.',
                'column' => 'right',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah produk bisa ditukar jika ukuran tidak sesuai?',
                'answer' => 'Ya, penukaran ukuran dapat diajukan dalam waktu maksimal 7 hari sejak barang diterima selama tag produk masih terpasang, belum dicuci, dan stok ukuran pengganti tersedia.',
                'column' => 'right',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah tersedia promo gratis ongkos kirim?',
                'answer' => 'Kami menyediakan opsi pengiriman Reguler / Standard Shipping dengan promo gratis ongkir sesuai ketentuan yang berlaku pada halaman Checkout.',
                'column' => 'right',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara menghubungi layanan pelanggan (Customer Support)?',
                'answer' => 'Anda dapat mengirim pesan langsung melalui halaman Contact Us (otomatis tercatat sebagai tiket bantuan) dan memantau balasan tim support kami di halaman Ticket List.',
                'column' => 'right',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        EcommerceFaq::query()->delete();

        foreach ($faqs as $faq) {
            EcommerceFaq::create($faq);
        }
    }
}
