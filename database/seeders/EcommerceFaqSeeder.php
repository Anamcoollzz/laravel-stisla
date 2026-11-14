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
                'question' => 'What is the multi vendor services?',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'left',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'How to buy many products at a time?',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever. unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'left',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Refund policy for customer.',
                'answer' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem unknown printer took a galley of type and scrambled it to make a type specimen book.',
                'column' => 'left',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Exchange policy for customer.',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an Ipsum has been the industry's unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'left',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Give a way products available.',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an Ipsum has been the industry's standard dummy text ever since the 1500s. unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'left',
                'order' => 5,
                'is_active' => true,
            ],

            // Right column
            [
                'question' => 'Refund policy for customer.',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'right',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Exchange policy for customer.',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an Ipsum has been the industry's unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'right',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'How to buy many products at a time?',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever. unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'right',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Give a way products available.',
                'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an Ipsum has been the industry's standard dummy text ever since the 1500s. unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'column' => 'right',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'What is the multi vendor services?',
                'answer' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem unknown printer took a galley of type and scrambled it to make a type specimen book.',
                'column' => 'right',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            EcommerceFaq::updateOrCreate(
                ['question' => $faq['question'], 'column' => $faq['column']],
                $faq
            );
        }
    }
}
