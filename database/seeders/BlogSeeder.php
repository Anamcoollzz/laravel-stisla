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
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-06-30',
                'order' => 1,
                'short_description' => 'Discover the essential five steps to build a modern fashion brand and market your products effectively.',
                'content' => '<p class="mn-text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p><p class="mn-text-highlight">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero.</p><p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Best way to solve business issue in market.',
                'category' => 'Shoes',
                'author_name' => 'Sarah Connor',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-04-02',
                'order' => 2,
                'short_description' => 'Overcoming market challenges requires data-driven strategies and agile decision making for footwear retailers.',
                'content' => '<p class="mn-text">Footwear and retail markets are undergoing rapid shifts with customer preference moving towards sustainable manufacturing and direct-to-consumer delivery models.</p><p class="mn-text-highlight">Understanding unit economics and supply chain visibility is critical for surviving modern supply shocks.</p><p>By prioritizing authentic brand messaging, clear return policies, and high-quality sizing guides, shoe brands can drastically reduce return rates while boosting loyalty.</p>',
                'is_active' => true,
            ],
            [
                'title' => '31 grocery customer service stats know in 2025.',
                'category' => 'Grocery',
                'author_name' => 'David Miller',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-03-09',
                'order' => 3,
                'short_description' => 'Key insights and benchmarks that grocery e-commerce store owners must understand to maximize repeat customer rate.',
                'content' => '<p class="mn-text">Grocery delivery customer loyalty hinges almost entirely on fulfillment accuracy and freshness guarantees. Shoppers who receive an incorrect item on their first order are 60% less likely to re-order within 30 days.</p><p class="mn-text-highlight">Investing in real-time inventory synchronization between store shelves and online storefronts prevents painful out-of-stock substitutions.</p><p>Explore key automation tools that warehouse managers use to track temperature-sensitive perishables from pick to doorstep.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Business ideas to grow your business traffic.',
                'category' => 'Bags',
                'author_name' => 'Emily Watson',
                'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-01-25',
                'order' => 4,
                'short_description' => 'Actionable tips and organic marketing channels to bring high-intent shoppers to your bag and accessories store.',
                'content' => '<p class="mn-text">Building organic brand momentum in the competitive leather goods and everyday carry accessories space requires distinct design storytelling and community-driven content.</p><p class="mn-text-highlight">Micro-influencer product seeding and long-form durability reviews on YouTube generate high-converting referral traffic.</p><p>Learn how to optimize product pages with video reviews, size comparisons against common laptops, and detailed leather care instructions.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Marketing Guide: 5 Steps way to Success.',
                'category' => 'Cosmetics',
                'author_name' => 'Olivia Smith',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2026-12-10',
                'order' => 5,
                'short_description' => 'How to leverage influencer partnerships and authentic social reviews to scale cosmetic e-commerce sales.',
                'content' => '<p class="mn-text">Skincare and beauty consumers demand complete ingredient transparency, dermatologist backing, and cruelty-free certifications.</p><p class="mn-text-highlight">User-generated before-and-after video content outperforms studio product photography across all paid social ad channels.</p><p>Implement automated replenishment subscription options to transform one-off serum and moisturizer purchases into recurring predictable monthly revenue.</p>',
                'is_active' => true,
            ],
            [
                'title' => '15 customer service stats idea know in 2026.',
                'category' => 'Bags',
                'author_name' => 'Michael Brown',
                'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2027-08-08',
                'order' => 6,
                'short_description' => 'Crucial customer experience metrics to track and enhance for high customer lifetime value.',
                'content' => '<p class="mn-text">Rapid response times on live chat during checkout reduce shopping cart abandonment by over 28% for luxury accessory purchases.</p><p class="mn-text-highlight">Empowering customer support agents to issue immediate replacements or store credits eliminates negative social reviews.</p><p>Integrate omnichannel support so customer conversations across Instagram DM, email, and live chat share the same customer order context.</p>',
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
