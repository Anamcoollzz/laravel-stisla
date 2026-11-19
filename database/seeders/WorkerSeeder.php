<?php

namespace Database\Seeders;

use App\Models\LP\Worker;
use App\Services\RecruitmentDataService;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workers = app(RecruitmentDataService::class)->defaultWorkersData();

        foreach ($workers as $worker) {
            Worker::query()->firstOrCreate(
                ['code' => $worker['code']],
                [
                    'slug' => $worker['slug'],
                    'name' => $worker['name'],
                    'category' => $worker['category'],
                    'category_label' => $worker['category_label'],
                    'age' => $worker['age'],
                    'age_bracket' => $worker['age_bracket'],
                    'origin' => $worker['origin'],
                    'origin_region' => $worker['origin_region'],
                    'education' => $worker['education'],
                    'experience_years' => $worker['experience_years'],
                    'experience_label' => $worker['experience_label'],
                    'experience_bracket' => $worker['experience_bracket'],
                    'status' => $worker['status'],
                    'is_featured' => $worker['is_featured'],
                    'published_date' => $worker['created_at'] ?? now()->toDateString(),
                    'photo' => $worker['photo'] ?? null,
                    'gallery' => $worker['gallery'] ?? [],
                    'languages' => $worker['languages'] ?? [],
                    'bio' => $worker['bio'] ?? null,
                    'skills' => $worker['skills'] ?? [],
                    'work_history' => $worker['work_history'] ?? [],
                    'placement_preferences' => $worker['placement_preferences'] ?? [],
                    'training_certifications' => $worker['training_certifications'] ?? [],
                ]
            );
        }
    }
}
