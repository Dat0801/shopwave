<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@shopwave.test')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@shopwave.test',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        $posts = [
            [
                'title' => 'The Future of Sustainable Fashion',
                'category' => 'Sustainable Fashion',
                'status' => 'published',
                'excerpt' => 'Discover how eco-friendly materials and ethical production are reshaping the industry.',
                'content' => '<p>The fashion industry is undergoing a significant transformation. As consumers become more environmentally conscious, brands are shifting towards sustainable practices.</p><h2>Why Sustainability Matters</h2><p>Fast fashion has taken a toll on our planet. From water pollution to textile waste, the impact is undeniable. However, a new wave of designers is prioritizing the planet.</p><h2>Eco-Friendly Materials</h2><p>Materials like organic cotton, recycled polyester, and Tencel are gaining popularity. These fabrics not only reduce environmental footprint but also offer superior comfort.</p>',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&q=80&w=800',
                'meta_title' => 'Sustainable Fashion Trends 2026',
                'meta_description' => 'Explore the latest trends in sustainable fashion and how they impact the environment.',
                'tags' => ['sustainability', 'fashion', 'eco-friendly'],
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Top 10 Style Trends for Summer 2026',
                'category' => 'Trends',
                'status' => 'published',
                'excerpt' => 'Get ready for the sunny season with our curated list of must-have summer essentials.',
                'content' => '<p>Summer is around the corner, and it\'s time to update your wardrobe. Here are the top trends you need to know about.</p><h2>1. Bold Colors</h2><p>This summer is all about making a statement. Think neon greens, electric blues, and hot pinks.</p><h2>2. Retro Vibes</h2><p>The 70s are back! Flared pants, crochet tops, and oversized sunglasses are everywhere.</p>',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&q=80&w=800',
                'meta_title' => 'Summer Style Trends 2026',
                'meta_description' => 'Your guide to the hottest fashion trends for Summer 2026.',
                'tags' => ['summer', 'trends', 'style'],
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Mastering the Art of Minimalist Living',
                'category' => 'Lifestyle',
                'status' => 'published',
                'excerpt' => 'Simplify your life and find joy in less. A practical guide to minimalism.',
                'content' => '<p>Minimalism isn\'t just about having fewer things; it\'s about making room for what truly matters.</p><h2>Decluttering Your Space</h2><p>Start small. Tackle one room at a time. Ask yourself: Does this item bring me joy?</p><h2>Mindful Consumption</h2><p>Before buying something new, consider if you really need it. Quality over quantity is the key.</p>',
                'image' => 'https://images.unsplash.com/photo-1449247709967-d4461a6a6103?auto=format&fit=crop&q=80&w=800',
                'meta_title' => 'Minimalist Living Guide',
                'meta_description' => 'Learn how to embrace minimalism and improve your quality of life.',
                'tags' => ['minimalism', 'lifestyle', 'wellness'],
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Understanding UX Design Principles',
                'category' => 'UX Design',
                'status' => 'published',
                'excerpt' => 'A deep dive into the core principles that create exceptional user experiences.',
                'content' => '<p>User Experience (UX) design is crucial for the success of any digital product.</p><h2>Empathy is Key</h2><p>Understanding your users\' needs and pain points is the first step in creating a great product.</p><h2>Consistency</h2><p>Consistent design elements help users navigate your interface intuitively.</p>',
                'image' => 'https://images.unsplash.com/photo-1586717791821-3f44a5638d48?auto=format&fit=crop&q=80&w=800',
                'meta_title' => 'UX Design Basics',
                'meta_description' => 'A beginner\'s guide to User Experience Design principles.',
                'tags' => ['ux', 'design', 'web'],
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'New Product Launch: The Eco-Tote',
                'category' => 'Product News',
                'status' => 'scheduled',
                'excerpt' => 'Introducing our latest addition to the collection, made from 100% recycled materials.',
                'content' => '<p>We are excited to announce the launch of our new Eco-Tote.</p><p>It is durable, stylish, and most importantly, good for the planet.</p>',
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&q=80&w=800',
                'meta_title' => 'Eco-Tote Launch',
                'meta_description' => 'Announcing the new Eco-Tote from ShopWave.',
                'tags' => ['product', 'news', 'launch'],
                'published_at' => now()->addDays(5),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                array_merge($post, [
                    'slug' => Str::slug($post['title']),
                    'author_id' => $admin->id,
                ])
            );
        }
    }
}
