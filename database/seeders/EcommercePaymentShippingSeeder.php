<?php

namespace Database\Seeders;

use App\Models\PaymentBank;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class EcommercePaymentShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Shipping Methods
        $shippingMethods = [
            [
                'code' => 'reguler',
                'name' => 'Reguler / Standard Shipping',
                'cost' => 0,
                'estimated_delivery' => '3 - 5 hari kerja',
                'description' => 'Pengiriman standar ke seluruh wilayah dengan promo gratis ongkir.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'jne-reg',
                'name' => 'JNE Express (Reguler)',
                'cost' => 12000,
                'estimated_delivery' => '2 - 3 hari kerja',
                'description' => 'Layanan pengiriman cepat dan terpercaya dengan jangkauan luas.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'sicepat-reg',
                'name' => 'SiCepat Ekspres (REG)',
                'cost' => 11000,
                'estimated_delivery' => '1 - 2 hari kerja',
                'description' => 'Pengiriman kilat SiCepat langsung ke alamat tujuan.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'instant',
                'name' => 'Instant Courier (GoSend / Grab)',
                'cost' => 25000,
                'estimated_delivery' => '2 - 4 jam tiba',
                'description' => 'Pengiriman instan pada hari yang sama khusus area dalam kota.',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($shippingMethods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }

        // 2. Payment Methods
        $paymentMethods = [
            [
                'code' => 'cod',
                'name' => 'Cash On Delivery (COD)',
                'type' => 'cod',
                'fee' => 0,
                'description' => 'Bayar langsung secara tunai saat kurir tiba di alamat Anda.',
                'instructions' => 'Pastikan Anda menyiapkan uang pas sesuai total tagihan saat kurir mengantarkan pesanan.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'bank_transfer',
                'name' => 'Transfer Bank Manual',
                'type' => 'bank_transfer',
                'fee' => 0,
                'description' => 'Transfer manual ke rekening resmi bank kami (BCA, Mandiri, BRI).',
                'instructions' => 'Silakan lakukan transfer ke salah satu nomor rekening resmi yang tertera. Cantumkan nomor pesanan pada berita transfer.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'qris_ewallet',
                'name' => 'QRIS & E-Wallet (GoPay, OVO, Dana)',
                'type' => 'e_wallet',
                'fee' => 1000,
                'description' => 'Bayar instan dengan memindai kode QRIS menggunakan e-wallet atau mobile banking favorit.',
                'instructions' => 'Pindai kode QRIS yang tampil setelah melakukan pemesanan dan selesaikan pembayaran sebelum batas waktu habis.',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }

        // 3. Payment Banks
        $banks = [
            [
                'bank_code' => 'BCA',
                'bank_name' => 'Bank Central Asia (BCA)',
                'account_name' => 'PT LARAVEL STISLA STORE',
                'account_number' => '8801234567',
                'branch' => 'KCU Thamrin Jakarta',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'bank_code' => 'MANDIRI',
                'bank_name' => 'Bank Mandiri',
                'account_name' => 'PT LARAVEL STISLA STORE',
                'account_number' => '1370019876543',
                'branch' => 'KCP Sudirman Jakarta',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'bank_code' => 'BRI',
                'bank_name' => 'Bank Rakyat Indonesia (BRI)',
                'account_name' => 'PT LARAVEL STISLA STORE',
                'account_number' => '034101000998877',
                'branch' => 'KC Gatot Subroto',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banks as $bank) {
            PaymentBank::updateOrCreate(
                ['bank_code' => $bank['bank_code']],
                $bank
            );
        }
    }
}

