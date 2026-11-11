<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Marketing Guide: 5 Steps to Success to way.',
                'category' => 'Fashion',
                'author_name' => 'John Deo',
                'image' => 'mantu/assets/img/blog/1.jpg',
                'published_at' => '2025-06-30',
                'order' => 1,
                'short_description' => 'Discover the essential five steps to build a modern fashion brand and market your products effectively.',
                'content' => '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p><p class="mn-text-highlight">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested.</p><p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Best way to solve business issue in market.',
                'category' => 'Shoes',
                'author_name' => 'Sarah Connor',
                'image' => 'mantu/assets/img/blog/2.jpg',
                'published_at' => '2025-04-02',
                'order' => 2,
                'short_description' => 'Overcoming market challenges requires data-driven strategies and agile decision making for footwear retailers.',
                'content' => '<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.</p><p>Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.</p>',
                'is_active' => true,
            ],
            [
                'title' => '31 grocery customer service stats know in 2025.',
                'category' => 'Grocery',
                'author_name' => 'David Miller',
                'image' => 'mantu/assets/img/blog/3.jpg',
                'published_at' => '2025-03-09',
                'order' => 3,
                'short_description' => 'Key insights and benchmarks that grocery e-commerce store owners must understand to maximize repeat customer rate.',
                'content' => '<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters.</p><p>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Business ideas to grow your business traffic.',
                'category' => 'Bags',
                'author_name' => 'Emily Watson',
                'image' => 'mantu/assets/img/blog/4.jpg',
                'published_at' => '2025-01-25',
                'order' => 4,
                'short_description' => 'Actionable tips and organic marketing channels to bring high-intent shoppers to your bag and accessories store.',
                'content' => '<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable.</p><p>If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Marketing Guide: 5 Steps way to Success.',
                'category' => 'Cosmetics',
                'author_name' => 'Olivia Smith',
                'image' => 'mantu/assets/img/blog/5.jpg',
                'published_at' => '2026-12-10',
                'order' => 5,
                'short_description' => 'How to leverage influencer partnerships and authentic social reviews to scale cosmetic e-commerce sales.',
                'content' => '<p>All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p><p>It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>',
                'is_active' => true,
            ],
            [
                'title' => '15 customer service stats idea know in 2026.',
                'category' => 'Bags',
                'author_name' => 'Michael Brown',
                'image' => 'mantu/assets/img/blog/6.jpg',
                'published_at' => '2027-08-08',
                'order' => 6,
                'short_description' => 'Crucial customer experience metrics to track and enhance for high customer lifetime value.',
                'content' => '<p>The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p><p>Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($blogs as $blogData) {
            $slug = Str::slug($blogData['title']);
            Blog::updateOrCreate(
                ['slug' => $slug],
                $blogData
            );
        }
    }
}

