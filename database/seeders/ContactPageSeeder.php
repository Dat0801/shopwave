<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'content' => 'We are here to help you with any questions or concerns.',
                'is_active' => true,
                'meta' => [
                    'header_title' => "Let's connect.",
                    'header_description' => "We're here to help you ride the wave of the latest fashion trends. Reach out to us for any queries about orders, styling, or collaborations.",
                    'subjects' => [
                        'Order Support',
                        'Returns & Exchanges',
                        'Product Inquiry',
                        'Collaboration',
                        'Other'
                    ],
                    'seo_title' => 'Contact ShopWave | Customer Support & Inquiries',
                    'seo_description' => 'Get in touch with the ShopWave team. We are here to assist with your orders, returns, and fashion questions.',
                ]
            ]
        );
    }
}
