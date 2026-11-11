<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogComment;
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
                'content' => '<p class="mn-text">Building a modern fashion label requires balancing creative artistry with rigorous unit economics. In today\'s crowded digital marketplace, brand identity is your ultimate competitive moat.</p><p class="mn-text-highlight">Focus on delivering an exceptional unboxing experience and fostering genuine community dialogue through organic social storytelling.</p><p>By understanding customer acquisition cost (CAC) versus lifetime value (LTV), you can responsibly scale marketing budgets across omnichannel touchpoints.</p>',
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
            [
                'title' => 'The Ultimate Guide to Sustainable Fashion 2025.',
                'category' => 'Fashion',
                'author_name' => 'Jessica Taylor',
                'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-07-15',
                'order' => 7,
                'short_description' => 'How conscious consumerism and circular fabrics are reshaping the global apparel supply chain.',
                'content' => '<p class="mn-text">Consumers increasingly vote with their wallets, prioritizing garments made with certified organic cotton, recycled fibers, and zero toxic water dyes.</p><p class="mn-text-highlight">Circularity initiatives such as trade-in programs and repair guarantees create long-term affinity and repeat purchases.</p><p>Brands embracing transparent supply chains build unshakeable credibility against greenwashing scrutiny.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Top 10 Running Shoes for Performance & Style.',
                'category' => 'Shoes',
                'author_name' => 'Alex Rivera',
                'image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-08-01',
                'order' => 8,
                'short_description' => 'A comprehensive review of the latest nitrogen-infused midsole runners perfect for both marathon training and street fashion.',
                'content' => '<p class="mn-text">Modern running footwear blends carbon plate bounce technology with sleek minimalist aesthetics that easily transition from track to café.</p><p class="mn-text-highlight">Cushioning longevity and breathable knit uppers make these top picks the most versatile daily trainers this season.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'How Organic Farming is Transforming Supermarkets.',
                'category' => 'Grocery',
                'author_name' => 'Hannah Scott',
                'image' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-08-10',
                'order' => 9,
                'short_description' => 'Farm-to-shelf traceability enables shoppers to verify harvest locations with QR code scans.',
                'content' => '<p class="mn-text">Regenerative agriculture promotes rich soil biodiversity while eliminating pesticide runoff into local water tables.</p><p class="mn-text-highlight">Retailers partnering directly with local growers bypass expensive distributor markups and guarantee 24-hour freshness.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Minimalist Everyday Carry: The Best Backpacks.',
                'category' => 'Bags',
                'author_name' => 'Daniel Kim',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-08-20',
                'order' => 10,
                'short_description' => 'Weatherproof materials and smart laptop protection compartments designed for digital nomads.',
                'content' => '<p class="mn-text">Cordura ballistic nylon, YKK AquaGuard zippers, and ergonomic magnetic clasps define this generation of high-utility commuter backpacks.</p><p class="mn-text-highlight">Dedicated tech organization sleeves safeguard chargers, SSD drives, and laptops from sudden impacts.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Clean Beauty: Essential Skincare Routine Steps.',
                'category' => 'Cosmetics',
                'author_name' => 'Emma Roberts',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-09-02',
                'order' => 11,
                'short_description' => 'Streamline your morning routine with gentle double cleansers, hyaluronic acid, and non-nano zinc sunscreen.',
                'content' => '<p class="mn-text">Skin barrier health is the foundational pillar of lasting natural radiance, far more effective than aggressive chemical peels.</p><p class="mn-text-highlight">Layering lightweight humectants before sealing with ceramides ensures all-day hydration without pore congestion.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Modern Smart Home Gadgets You Actually Need.',
                'category' => 'Electronics',
                'author_name' => 'Lucas Vance',
                'image' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-09-15',
                'order' => 12,
                'short_description' => 'Automate your daily ambient lighting, climate control, and home security with seamless Matter protocol devices.',
                'content' => '<p class="mn-text">Unified smart home protocols have finally eliminated fragmented proprietary hubs and bridge hardware.</p><p class="mn-text-highlight">Intelligent scheduling reduces unnecessary electric consumption by up to 22% during peak utility tariff hours.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Smart Wearables & Smartwatches for Fitness.',
                'category' => 'Electronics',
                'author_name' => 'Marcus Chen',
                'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-09-25',
                'order' => 13,
                'short_description' => 'Biometric sensors track heart rate variability, sleep stages, and recovery readiness with clinical precision.',
                'content' => '<p class="mn-text">Continuous wrist-based PPG tracking empowers athletes to prevent overtraining injuries through actionable strain scores.</p><p class="mn-text-highlight">Always-on AMOLED displays and multi-day battery endurance make these wearables indispensable daily companions.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Eco-Friendly Packaging: Winning Shopper Trust.',
                'category' => 'Lifestyle',
                'author_name' => 'Chloe Bennett',
                'image' => 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-10-05',
                'order' => 14,
                'short_description' => 'Replace single-use bubble wraps with biodegradable cornstarch mailers and water-activated kraft paper tape.',
                'content' => '<p class="mn-text">Modern customers notice packaging waste immediately upon parcel delivery, influencing brand sentiment and review scores.</p><p class="mn-text-highlight">Custom FSC-certified corrugated boxes project premium quality while maintaining 100% home compostability.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Urban Commuting Essentials for Busy Professionals.',
                'category' => 'Lifestyle',
                'author_name' => 'Ryan Cooper',
                'image' => 'https://images.unsplash.com/photo-1517404215738-15263e9f9178?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-10-18',
                'order' => 15,
                'short_description' => 'Compact umbrellas, noise-canceling earbuds, and insulated travel mugs designed for morning transit.',
                'content' => '<p class="mn-text">Navigating busy metropolitan transit networks requires streamlined everyday gear that combines durability with effortless mobility.</p><p class="mn-text-highlight">Active noise cancellation creates a calm mobile office bubble even on crowded subway platforms.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Luxury Watch Collection: A Beginner Investment Guide.',
                'category' => 'Fashion',
                'author_name' => 'Arthur Pendelton',
                'image' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-10-28',
                'order' => 16,
                'short_description' => 'Mechanical horology, heritage movements, and historical price appreciation across iconic steel sports watches.',
                'content' => '<p class="mn-text">Collecting mechanical timepieces combines a passion for micro-engineering with tangible store-of-value assets.</p><p class="mn-text-highlight">Focus on pristine dial condition, original box papers, and unpolished case bevels when acquiring pre-owned pieces.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'The Rise of Artisan Coffee and Specialty Beans.',
                'category' => 'Grocery',
                'author_name' => 'Oliver Wood',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-11-04',
                'order' => 17,
                'short_description' => 'Single-origin Ethiopian Gesha and anaerobic fermentation methods unlocking bright floral and fruit tasting notes.',
                'content' => '<p class="mn-text">Third-wave coffee connoisseurs celebrate unique terroir and micro-lot farm processing that reveal complex jasmine and citrus profiles.</p><p class="mn-text-highlight">Investing in a high-precision conical burr grinder is the single greatest upgrade for elevating morning pour-over brews.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Leather Care 101: How to Maintain Leather Boots.',
                'category' => 'Shoes',
                'author_name' => 'Gabriel Thorne',
                'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-11-12',
                'order' => 18,
                'short_description' => 'Horsehair brush buffing, conditioning with natural beeswax, and cedar shoe trees to extend boot life for decades.',
                'content' => '<p class="mn-text">Goodyear welted boots develop a rich patina that tells the story of every mile walked when properly cared for.</p><p class="mn-text-highlight">Conditioning full-grain leather every three months prevents drying and premature cracking in wet climates.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Travel Essentials: Packing Light with Style.',
                'category' => 'Bags',
                'author_name' => 'Sophie Laurent',
                'image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-11-20',
                'order' => 19,
                'short_description' => 'Compression packing cubes and wrinkle-resistant merino wool capsules for carry-on only international travels.',
                'content' => '<p class="mn-text">Skipping checked luggage lines eliminates airport stress and gives travelers ultimate freedom upon landing.</p><p class="mn-text-highlight">Versatile neutral palettes and modular packing organizers keep garments neat and accessible throughout multi-city trips.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Hydration & Sunscreen: Daily Dermatology Tips.',
                'category' => 'Cosmetics',
                'author_name' => 'Dr. Elena Rostova',
                'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=1000&q=80',
                'published_at' => '2025-12-01',
                'order' => 20,
                'short_description' => 'Broad-spectrum SPF 50 shields against photoaging and hyperpigmentation through rain, shine, and blue light glare.',
                'content' => '<p class="mn-text">Dermatologists consistently rank daily broad-spectrum sunscreen as the number one anti-aging regimen step.</p><p class="mn-text-highlight">Reapplying every two hours during outdoor exposure prevents silent UV cellular damage.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($blogs as $blogData) {
            $slug = Str::slug($blogData['title']);
            $blog = Blog::updateOrCreate(
                ['slug' => $slug],
                $blogData
            );

            // Add sample comments to first few blogs if empty
            if ($blog->comments()->count() === 0 && in_array($blog->order, [1, 2, 3])) {
                $c1 = BlogComment::create([
                    'blog_id' => $blog->id,
                    'name' => 'John Deo',
                    'email' => 'john.deo@example.com',
                    'comment' => 'Artikel yang sangat informatif dan bermanfaat! Tips strategi pemasarannya sangat relevan dan mudah diaplikasikan.',
                    'is_approved' => true,
                    'created_at' => now()->subDays(2),
                ]);

                BlogComment::create([
                    'blog_id' => $blog->id,
                    'parent_id' => $c1->id,
                    'name' => 'Jenifer Lowes',
                    'email' => 'jenifer@example.com',
                    'comment' => 'Setuju sekali dengan poin di atas, terutama mengenai pentingnya data analytics untuk loyalitas pelanggan.',
                    'is_approved' => true,
                    'created_at' => now()->subDay(),
                ]);

                BlogComment::create([
                    'blog_id' => $blog->id,
                    'name' => 'Rian Hidayat',
                    'email' => 'rian@example.com',
                    'comment' => 'Kualitas ulasannya mendalam dan langsung to the point. Ditunggu kelanjutan artikel berikutnya!',
                    'is_approved' => true,
                    'created_at' => now()->subHours(6),
                ]);
            }
        }
    }
}
