<?php

namespace Database\Seeders;

use App\Models\SppPayment;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SppPaymentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::query()->get();

        if ($students->isEmpty()) {
            $students = Student::factory()->count(10)->create();
        }

        SppPayment::factory()
            ->count(20)
            ->recycle($students)
            ->create();
    }
}
