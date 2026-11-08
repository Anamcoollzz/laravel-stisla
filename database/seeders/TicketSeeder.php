<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first()
            ?? User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ]);

        $user1 = User::firstOrCreate(
            ['email' => 'farhan@example.com'],
            ['name' => 'Farhan A. Mujib', 'role' => 'user', 'password' => bcrypt('password')]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'amanda@example.com'],
            ['name' => 'Amanda Aprilia Azmi', 'role' => 'user', 'password' => bcrypt('password')]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'irwansyah@example.com'],
            ['name' => 'Irwansyah Saputra', 'role' => 'user', 'password' => bcrypt('password')]
        );

        $ticket1 = Ticket::firstOrCreate(
            ['title' => 'Technical problem'],
            [
                'user_id' => $user1->id,
                'description' => "Halo tim support, saya mengalami kendala teknis saat mencoba mengakses modul pembayaran SPP. Halaman menampilkan error dan tidak dapat memuat data siswa secara normal.\n\nMohon bantuannya untuk memeriksa konfigurasi akun atau koneksi database sistem kami. Terima kasih!",
                'status' => 'open',
                'priority' => 'high',
                'created_at' => now()->subMinutes(15),
            ]
        );

        TicketReply::firstOrCreate(
            [
                'ticket_id' => $ticket1->id,
                'user_id' => $admin->id,
            ],
            [
                'message' => 'Halo Farhan, terima kasih atas laporannya. Tim teknis kami sedang memeriksa log server terkait modul SPP. Kami akan segera memperbarui tiket ini.',
                'created_at' => now()->subMinutes(5),
            ]
        );

        Ticket::firstOrCreate(
            ['title' => 'Cancel my order please!'],
            [
                'user_id' => $user2->id,
                'description' => "Saya tidak sengaja melakukan submit transaksi ganda pada formulir pendaftaran. Apakah bisa dibantu untuk membatalkan tagihan nomor #SPP-2026-0012?\n\nTerima kasih banyak atas bantuannya.",
                'status' => 'in_progress',
                'priority' => 'medium',
                'created_at' => now()->subDay(),
            ]
        );

        Ticket::firstOrCreate(
            ['title' => 'Where is my mother?'],
            [
                'user_id' => $user3->id,
                'description' => 'Permisi admin, saya ingin menanyakan perihal data wali murid di profil siswa yang belum terisi lengkap. Bagaimana cara memperbarui data orang tua/wali murid di sistem ini?',
                'status' => 'closed',
                'priority' => 'low',
                'created_at' => now()->subDays(3),
            ]
        );
    }
}
