<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('blog_category_id')->nullable()->after('category')->constrained('blog_categories')->nullOnDelete();
        });

        // Seed default categories
        $categories = ['Style Guide', 'Product News', 'UX Design', 'Sustainable Fashion', 'Trends', 'Lifestyle'];
        foreach ($categories as $name) {
            $slug = Str::slug($name);
            if (!DB::table('blog_categories')->where('slug', $slug)->exists()) {
                DB::table('blog_categories')->insert([
                    'name' => $name,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Migrate existing posts
        $posts = DB::table('blog_posts')->whereNotNull('category')->get();
        foreach ($posts as $post) {
            $categoryName = $post->category;
            $slug = Str::slug($categoryName);
            
            $category = DB::table('blog_categories')->where('slug', $slug)->first();
            
            if (!$category) {
                // Create if not exists (for existing posts with other categories)
                $id = DB::table('blog_categories')->insertGetId([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('blog_posts')->where('id', $post->id)->update(['blog_category_id' => $id]);
            } else {
                DB::table('blog_posts')->where('id', $post->id)->update(['blog_category_id' => $category->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign(['blog_category_id']);
            $table->dropColumn('blog_category_id');
        });
    }
};
