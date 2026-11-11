<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Mariya Klinton',
                'designation' => 'CEO',
                'content' => "Lorem Ipsum is simply dummy text of the printing and industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
                'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80',
                'rating' => 5,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'John Doe',
                'designation' => 'CFO',
                'content' => 'Standard dummy text ever since the 1500s, when an unknown printer took a galley of type and this is the lorem and scrambled it to make a type specimen.',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
                'rating' => 5,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Nency Lykra',
                'designation' => 'Manager',
                'content' => "When an unknown printer took a galley of type and scrambled it to make a type specimen Lorem Ipsum has been the industry's and ever since to the 1500s.",
                'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&auto=format&fit=crop&q=80',
                'rating' => 5,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Alexander Wright',
                'designation' => 'Product Designer',
                'content' => 'Exceptional quality and fast shipping! The customer support team was incredibly helpful when I had questions about sizing. Highly recommended.',
                'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop&q=80',
                'rating' => 5,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($items as $data) {
            Testimonial::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}

