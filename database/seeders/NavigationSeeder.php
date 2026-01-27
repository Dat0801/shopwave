<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NavigationItem;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Header Navigation
        $headerItems = [
            ['name' => 'Shop', 'type' => 'route', 'url' => 'shop.index', 'order' => 0],
            ['name' => 'Blog', 'type' => 'route', 'url' => 'blog.index', 'order' => 1],
            ['name' => 'About Us', 'type' => 'route', 'url' => 'about', 'order' => 2],
            ['name' => 'Contact Us', 'type' => 'route', 'url' => 'contact.index', 'order' => 3],
        ];

        foreach ($headerItems as $item) {
            NavigationItem::create(array_merge($item, ['location' => 'header', 'is_active' => true]));
        }

        // Footer Navigation
        // Column 1: Shop
        $shopCol = NavigationItem::create([
            'name' => 'Shop',
            'type' => 'custom',
            'url' => '#',
            'location' => 'footer',
            'is_active' => true,
            'order' => 0
        ]);

        $shopLinks = [
            ['name' => 'All Products', 'type' => 'route', 'url' => 'shop.index', 'order' => 0],
            ['name' => 'Featured Drops', 'type' => 'custom', 'url' => '#', 'order' => 1],
            ['name' => "Men's Apparel", 'type' => 'custom', 'url' => '#', 'order' => 2],
            ['name' => "Women's Apparel", 'type' => 'custom', 'url' => '#', 'order' => 3],
        ];

        foreach ($shopLinks as $link) {
            NavigationItem::create(array_merge($link, ['location' => 'footer', 'is_active' => true, 'parent_id' => $shopCol->id]));
        }

        // Column 2: Support
        $supportCol = NavigationItem::create([
            'name' => 'Support',
            'type' => 'custom',
            'url' => '#',
            'location' => 'footer',
            'is_active' => true,
            'order' => 1
        ]);

        $supportLinks = [
            ['name' => 'Help Center', 'type' => 'custom', 'url' => '#', 'order' => 0],
            ['name' => 'Shipping & Returns', 'type' => 'custom', 'url' => '#', 'order' => 1],
            ['name' => 'Size Guide', 'type' => 'custom', 'url' => '#', 'order' => 2],
            ['name' => 'Track Order', 'type' => 'custom', 'url' => '#', 'order' => 3],
        ];

        foreach ($supportLinks as $link) {
            NavigationItem::create(array_merge($link, ['location' => 'footer', 'is_active' => true, 'parent_id' => $supportCol->id]));
        }

        // Column 3: Company
        $companyCol = NavigationItem::create([
            'name' => 'Company',
            'type' => 'custom',
            'url' => '#',
            'location' => 'footer',
            'is_active' => true,
            'order' => 2
        ]);

        $companyLinks = [
            ['name' => 'Our Story', 'type' => 'custom', 'url' => '#', 'order' => 0],
            ['name' => 'Sustainability', 'type' => 'custom', 'url' => '#', 'order' => 1],
            ['name' => 'Careers', 'type' => 'custom', 'url' => '#', 'order' => 2],
            ['name' => 'Contact', 'type' => 'route', 'url' => 'contact.index', 'order' => 3],
        ];

        foreach ($companyLinks as $link) {
            NavigationItem::create(array_merge($link, ['location' => 'footer', 'is_active' => true, 'parent_id' => $companyCol->id]));
        }

        // Mobile Navigation (Mirror Header + Account)
        foreach ($headerItems as $item) {
            NavigationItem::create(array_merge($item, ['location' => 'mobile', 'is_active' => true]));
        }
    }
}
