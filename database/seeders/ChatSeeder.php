<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first()
            ?? User::query()->first();

        if (! $admin) {
            return;
        }

        $others = User::query()
            ->where('id', '!=', $admin->id)
            ->orderBy('id')
            ->take(3)
            ->get();

        if ($others->isEmpty()) {
            return;
        }

        if (ChatMessage::query()->exists()) {
            return;
        }

        foreach ($others as $index => $user) {
            $baseTime = now()->subHours(($index + 1) * 4);

            ChatMessage::query()->create([
                'sender_id' => $user->id,
                'receiver_id' => $admin->id,
                'message' => 'Halo Admin, mohon bantuannya untuk cek status akun dan data terbaru saya ya.',
                'is_read' => true,
                'read_at' => $baseTime->copy()->addMinutes(5),
                'created_at' => $baseTime,
                'updated_at' => $baseTime,
            ]);

            ChatMessage::query()->create([
                'sender_id' => $admin->id,
                'receiver_id' => $user->id,
                'message' => 'Halo '.$user->name.', baik sedang kami cek. Ada detail tambahan yang ingin disampaikan?',
                'is_read' => true,
                'read_at' => $baseTime->copy()->addMinutes(12),
                'created_at' => $baseTime->copy()->addMinutes(10),
                'updated_at' => $baseTime->copy()->addMinutes(10),
            ]);

            ChatMessage::query()->create([
                'sender_id' => $user->id,
                'receiver_id' => $admin->id,
                'message' => 'Sudah lengkap semua, terima kasih respon cepatnya Admin!',
                'is_read' => $index > 1,
                'read_at' => $index > 1 ? $baseTime->copy()->addMinutes(25) : null,
                'created_at' => $baseTime->copy()->addMinutes(20),
                'updated_at' => $baseTime->copy()->addMinutes(20),
            ]);
        }
    }
}
