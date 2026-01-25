<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_categories_list()
    {
        Category::factory()->count(15)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Categories/Index')
                ->has('categories.data', 10) // Pagination limit
                ->has('categories.links')
            );
    }

    public function test_admin_can_view_create_category_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Categories/Create')
                ->has('allCategories')
            );
    }

    public function test_admin_can_create_category()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'New Category',
                'slug' => 'new-category',
                'description' => 'Description',
                'status' => true,
                'order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index')); // Or back, depending on implementation
        
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    public function test_admin_can_view_edit_category_page()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $category));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Categories/Edit')
                ->has('category')
                ->where('category.id', $category->id)
            );
    }

    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Updated Category',
                'slug' => 'updated-category',
                'description' => 'Updated Description',
                'status' => false,
                'order' => 2,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'status' => 0,
        ]);
    }

    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect();
        
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_admin_can_bulk_update_status()
    {
        $categories = Category::factory()->count(3)->create(['status' => false]);
        $ids = $categories->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.bulk-update-status'), [
                'ids' => $ids,
                'status' => true,
            ]);

        $response->assertRedirect();

        foreach ($ids as $id) {
            $this->assertDatabaseHas('categories', [
                'id' => $id,
                'status' => 1,
            ]);
        }
    }
}
