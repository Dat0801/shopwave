<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'ShopWave', 'group' => 'general', 'type' => 'text'],
            ['key' => 'site_description', 'value' => 'Your one-stop shop for everything.', 'group' => 'general', 'type' => 'textarea'],
            ['key' => 'contact_email', 'value' => 'support@shopwave.com', 'group' => 'general', 'type' => 'email'],
            ['key' => 'contact_phone', 'value' => '+1 (555) 123-4567', 'group' => 'general', 'type' => 'text'],
            ['key' => 'address', 'value' => '123 Shop Street, Commerce City, CA 90210', 'group' => 'general', 'type' => 'text'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/shopwave', 'group' => 'social', 'type' => 'url'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/shopwave', 'group' => 'social', 'type' => 'url'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/shopwave', 'group' => 'social', 'type' => 'url'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        // 1. Create Users
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@shopwave.test'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $customer = User::query()->updateOrCreate(
            ['email' => 'user@shopwave.test'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]
        );

        // 2. Create Categories
        $categories = [
            [
                'name' => 'Electronics',
                'image_path' => 'https://placehold.co/200x200?text=Electronics',
                'description' => 'Latest gadgets and electronic devices for your daily needs.',
                'children' => [
                    ['name' => 'Smartphones', 'image_path' => 'https://placehold.co/200x200?text=Smartphones'],
                    ['name' => 'Laptops', 'image_path' => 'https://placehold.co/200x200?text=Laptops'],
                    ['name' => 'Headphones', 'image_path' => 'https://placehold.co/200x200?text=Headphones'],
                    ['name' => 'Cameras', 'image_path' => 'https://placehold.co/200x200?text=Cameras'],
                    ['name' => 'Wearables', 'image_path' => 'https://placehold.co/200x200?text=Wearables'],
                ]
            ],
            [
                'name' => 'Fashion',
                'image_path' => 'https://placehold.co/200x200?text=Fashion',
                'description' => 'Trendy clothing and accessories for men and women.',
                'children' => [
                    ['name' => 'Men', 'image_path' => 'https://placehold.co/200x200?text=Men'],
                    ['name' => 'Women', 'image_path' => 'https://placehold.co/200x200?text=Women'],
                    ['name' => 'Accessories', 'image_path' => 'https://placehold.co/200x200?text=Accessories'],
                    ['name' => 'Shoes', 'image_path' => 'https://placehold.co/200x200?text=Shoes'],
                ]
            ],
            [
                'name' => 'Home & Living',
                'image_path' => 'https://placehold.co/200x200?text=Home',
                'description' => 'Furniture, decor, and essentials for a beautiful home.',
                'children' => [
                    ['name' => 'Furniture', 'image_path' => 'https://placehold.co/200x200?text=Furniture'],
                    ['name' => 'Decor', 'image_path' => 'https://placehold.co/200x200?text=Decor'],
                    ['name' => 'Kitchen', 'image_path' => 'https://placehold.co/200x200?text=Kitchen'],
                ]
            ],
            [
                'name' => 'Sports & Outdoors',
                'image_path' => 'https://placehold.co/200x200?text=Sports',
                'description' => 'Gear and equipment for your active lifestyle.',
                'children' => [
                    ['name' => 'Gym Equipment', 'image_path' => 'https://placehold.co/200x200?text=Gym'],
                    ['name' => 'Running', 'image_path' => 'https://placehold.co/200x200?text=Running'],
                    ['name' => 'Cycling', 'image_path' => 'https://placehold.co/200x200?text=Cycling'],
                ]
            ],
            [
                'name' => 'Beauty & Health',
                'image_path' => 'https://placehold.co/200x200?text=Beauty',
                'description' => 'Skincare, makeup, and health supplements.',
                'children' => [
                    ['name' => 'Skincare', 'image_path' => 'https://placehold.co/200x200?text=Skincare'],
                    ['name' => 'Makeup', 'image_path' => 'https://placehold.co/200x200?text=Makeup'],
                    ['name' => 'Supplements', 'image_path' => 'https://placehold.co/200x200?text=Supplements'],
                ]
            ],
            [
                'name' => 'Books',
                'image_path' => 'https://placehold.co/200x200?text=Books',
                'description' => 'Explore worlds of fiction, non-fiction, and knowledge.',
                'children' => [
                    ['name' => 'Fiction', 'image_path' => 'https://placehold.co/200x200?text=Fiction'],
                    ['name' => 'Non-fiction', 'image_path' => 'https://placehold.co/200x200?text=Non-fiction'],
                    ['name' => 'Self-help', 'image_path' => 'https://placehold.co/200x200?text=Self-help'],
                ]
            ]
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);
            
            $catData['slug'] = Str::slug($catData['name']);
            $catData['status'] = true;
            $catData['order'] = 0;
            
            $parent = Category::firstOrCreate(['name' => $catData['name']], $catData);
            
            foreach ($children as $childData) {
                $childData['slug'] = Str::slug($childData['name']);
                $childData['status'] = true;
                $childData['parent_id'] = $parent->id;
                $childData['order'] = 0;
                $childData['description'] = $childData['name'] . ' category';
                Category::firstOrCreate(['name' => $childData['name']], $childData);
            }
        }

        // 3. Create Products
        $products = [
            // Smartphones
            ['name' => 'iPhone 15 Pro', 'price' => 999, 'category' => 'Smartphones'],
            ['name' => 'Samsung Galaxy S24', 'price' => 899, 'category' => 'Smartphones'],
            ['name' => 'Google Pixel 8', 'price' => 699, 'category' => 'Smartphones'],
            ['name' => 'OnePlus 12', 'price' => 799, 'category' => 'Smartphones'],
            ['name' => 'Xiaomi 14 Ultra', 'price' => 850, 'category' => 'Smartphones'],
            
            // Laptops
            ['name' => 'MacBook Air M2', 'price' => 1199, 'category' => 'Laptops'],
            ['name' => 'Dell XPS 13', 'price' => 1099, 'category' => 'Laptops'],
            ['name' => 'HP Spectre x360', 'price' => 1299, 'category' => 'Laptops'],
            ['name' => 'Lenovo ThinkPad X1', 'price' => 1499, 'category' => 'Laptops'],
            
            // Headphones
            ['name' => 'Sony WH-1000XM5', 'price' => 349, 'category' => 'Headphones'],
            ['name' => 'AirPods Pro 2', 'price' => 249, 'category' => 'Headphones'],
            ['name' => 'Bose QuietComfort Ultra', 'price' => 429, 'category' => 'Headphones'],
            ['name' => 'Sennheiser Momentum 4', 'price' => 379, 'category' => 'Headphones'],

            // Cameras
            ['name' => 'Sony Alpha a7 IV', 'price' => 2499, 'category' => 'Cameras'],
            ['name' => 'Canon EOS R6', 'price' => 2299, 'category' => 'Cameras'],
            ['name' => 'Fujifilm X-T5', 'price' => 1699, 'category' => 'Cameras'],

            // Wearables
            ['name' => 'Apple Watch Series 9', 'price' => 399, 'category' => 'Wearables'],
            ['name' => 'Samsung Galaxy Watch 6', 'price' => 299, 'category' => 'Wearables'],
            ['name' => 'Garmin Fenix 7', 'price' => 799, 'category' => 'Wearables'],
            
            // Men
            ['name' => 'Classic T-Shirt', 'price' => 29, 'category' => 'Men'],
            ['name' => 'Denim Jeans', 'price' => 59, 'category' => 'Men'],
            ['name' => 'Leather Jacket', 'price' => 199, 'category' => 'Men'],
            ['name' => 'Formal Shirt', 'price' => 45, 'category' => 'Men'],
            ['name' => 'Chino Pants', 'price' => 49, 'category' => 'Men'],
            
            // Women
            ['name' => 'Summer Dress', 'price' => 49, 'category' => 'Women'],
            ['name' => 'Leather Handbag', 'price' => 129, 'category' => 'Women'],
            ['name' => 'High Heels', 'price' => 89, 'category' => 'Women'],
            ['name' => 'Floral Blouse', 'price' => 39, 'category' => 'Women'],
            ['name' => 'Maxi Skirt', 'price' => 55, 'category' => 'Women'],

            // Shoes
            ['name' => 'Nike Air Force 1', 'price' => 110, 'category' => 'Shoes'],
            ['name' => 'Adidas Ultraboost', 'price' => 180, 'category' => 'Shoes'],
            ['name' => 'Converse Chuck Taylor', 'price' => 60, 'category' => 'Shoes'],
            
            // Furniture
            ['name' => 'Modern Sofa', 'price' => 599, 'category' => 'Furniture'],
            ['name' => 'Coffee Table', 'price' => 149, 'category' => 'Furniture'],
            ['name' => 'Ergonomic Chair', 'price' => 299, 'category' => 'Furniture'],
            ['name' => 'Bookshelf', 'price' => 199, 'category' => 'Furniture'],

            // Decor
            ['name' => 'Wall Art Abstract', 'price' => 89, 'category' => 'Decor'],
            ['name' => 'Table Lamp', 'price' => 49, 'category' => 'Decor'],
            ['name' => 'Floor Rug', 'price' => 129, 'category' => 'Decor'],

            // Kitchen
            ['name' => 'Non-stick Frying Pan', 'price' => 39, 'category' => 'Kitchen'],
            ['name' => 'Chef Knife Set', 'price' => 149, 'category' => 'Kitchen'],
            ['name' => 'Blender', 'price' => 79, 'category' => 'Kitchen'],

            // Gym Equipment
            ['name' => 'Dumbbell Set', 'price' => 99, 'category' => 'Gym Equipment'],
            ['name' => 'Yoga Mat', 'price' => 29, 'category' => 'Gym Equipment'],
            ['name' => 'Resistance Bands', 'price' => 19, 'category' => 'Gym Equipment'],

            // Running
            ['name' => 'Running Shoes', 'price' => 129, 'category' => 'Running'],
            ['name' => 'Hydration Pack', 'price' => 49, 'category' => 'Running'],
            
            // Skincare
            ['name' => 'Hydrating Moisturizer', 'price' => 25, 'category' => 'Skincare'],
            ['name' => 'Vitamin C Serum', 'price' => 35, 'category' => 'Skincare'],
            ['name' => 'Sunscreen SPF 50', 'price' => 20, 'category' => 'Skincare'],

            // Makeup
            ['name' => 'Matte Lipstick', 'price' => 18, 'category' => 'Makeup'],
            ['name' => 'Foundation', 'price' => 32, 'category' => 'Makeup'],
            ['name' => 'Mascara', 'price' => 15, 'category' => 'Makeup'],

            // Fiction
            ['name' => 'The Great Gatsby', 'price' => 12, 'category' => 'Fiction'],
            ['name' => '1984', 'price' => 10, 'category' => 'Fiction'],
            ['name' => 'To Kill a Mockingbird', 'price' => 14, 'category' => 'Fiction'],

            // Non-fiction
            ['name' => 'Sapiens', 'price' => 18, 'category' => 'Non-fiction'],
            ['name' => 'Atomic Habits', 'price' => 16, 'category' => 'Non-fiction'],
            ['name' => 'Thinking, Fast and Slow', 'price' => 20, 'category' => 'Non-fiction'],
        ];

        foreach ($products as $prodData) {
            $categoryName = $prodData['category'];
            unset($prodData['category']);
            
            $category = Category::where('name', $categoryName)->first();
            
            if ($category) {
                $prodData['slug'] = Str::slug($prodData['name']);
                $prodData['category_id'] = $category->id;
                $prodData['description'] = 'Experience the quality of ' . $prodData['name'] . '. This is a premium product designed to meet your needs.';
                $prodData['stock'] = rand(20, 100);
                $prodData['status'] = true;
                $prodData['sale_price'] = rand(0, 1) ? $prodData['price'] * 0.9 : null;
                $prodData['image_path'] = 'https://placehold.co/400x400?text=' . urlencode($prodData['name']);
                
                $product = Product::firstOrCreate(['name' => $prodData['name']], $prodData);

                // Add variants for some
                if (in_array($categoryName, ['Men', 'Women', 'Shoes', 'Running']) && $product->variants()->count() == 0) {
                     $sizes = ['S', 'M', 'L', 'XL'];
                     if ($categoryName == 'Shoes' || $categoryName == 'Running') {
                         $sizes = ['40', '41', '42', '43', '44'];
                     }
                     
                     foreach ($sizes as $size) {
                         ProductVariant::create([
                             'product_id' => $product->id,
                             'name' => $size,
                             'sku' => 'SKU-' . Str::upper(Str::random(8)),
                             'price' => $product->price,
                             'stock' => 50,
                             'options' => ['Size' => $size],
                             'active' => true
                         ]);
                     }
                }
            }
        }

        // 4. Create Banners
        $banners = [
            [
                'title' => 'Summer Sale',
                'description' => 'Up to 50% off on selected items. Don\'t miss out!',
                'image_path' => 'https://placehold.co/1200x400?text=Summer+Sale',
                'link' => '/shop',
                'duration' => 5000,
                'is_active' => true,
                'order' => 1
            ],
            [
                'title' => 'New Tech Arrivals',
                'description' => 'Check out the latest gadgets and upgrade your tech game.',
                'image_path' => 'https://placehold.co/1200x400?text=New+Arrivals',
                'link' => '/shop?category=electronics',
                'duration' => 5000,
                'is_active' => true,
                'order' => 2
            ],
            [
                'title' => 'Fashion Trends',
                'description' => 'Stay stylish with our new collection.',
                'image_path' => 'https://placehold.co/1200x400?text=Fashion+Trends',
                'link' => '/shop?category=fashion',
                'duration' => 5000,
                'is_active' => true,
                'order' => 3
            ],
            [
                'title' => 'Book Worms',
                'description' => 'Dive into your next adventure with our bestseller collection.',
                'image_path' => 'https://placehold.co/1200x400?text=Books+Collection',
                'link' => '/shop?category=books',
                'duration' => 5000,
                'is_active' => true,
                'order' => 4
            ]
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(['title' => $banner['title']], $banner);
        }
        
        // 5. Create Orders for the customer
        $orders = [
             [
                 'status' => 'completed',
                 'items' => [
                     ['product_name' => 'Classic T-Shirt', 'quantity' => 2],
                     ['product_name' => 'Denim Jeans', 'quantity' => 1],
                 ]
             ],
             [
                 'status' => 'pending',
                 'items' => [
                     ['product_name' => 'Sony WH-1000XM5', 'quantity' => 1],
                 ]
             ],
             [
                 'status' => 'processing',
                 'items' => [
                     ['product_name' => 'Atomic Habits', 'quantity' => 1],
                     ['product_name' => 'Sapiens', 'quantity' => 1],
                 ]
             ],
             [
                 'status' => 'cancelled',
                 'items' => [
                     ['product_name' => 'MacBook Air M2', 'quantity' => 1],
                 ]
             ]
        ];

        foreach ($orders as $orderData) {
            // Only create if user has no orders, to avoid duplicates on re-seed
            if ($customer->orders()->count() >= count($orders)) {
                break;
            }

            $itemsData = $orderData['items'];
            unset($orderData['items']);
            
            $order = Order::create([
                'user_id' => $customer->id,
                'status' => $orderData['status'],
                'total_price' => 0,
            ]);

            $total = 0;
            foreach ($itemsData as $item) {
                $product = Product::where('name', $item['product_name'])->first();
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                    ]);
                    $total += $product->price * $item['quantity'];
                }
            }
            $order->update(['total_price' => $total]);
        }
    }
}
