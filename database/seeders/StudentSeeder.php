<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = SchoolClass::query()->get();

        if ($classes->isEmpty()) {
            $classes = SchoolClass::factory()->count(5)->create();
        }

        Student::factory()
            ->count(20)
            ->recycle($classes)
            ->create();
    }
}
