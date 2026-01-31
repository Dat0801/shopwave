<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_notification_as_read_and_redirects()
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $category = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);
        
        $post = BlogPost::create([
            'title' => 'New Post',
            'slug' => 'new-post',
            'content' => 'Content',
            'author_id' => $author->id,
            'blog_category_id' => $category->id,
            'status' => 'published',
        ]);

        // Manually send notification
        $user->notify(new NewPostNotification($post));

        $notification = $user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)->post(route('notifications.read', $notification->id));

        $response->assertRedirect(route('blog.show', $post->slug));
        
        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_user_can_see_read_notifications()
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $category = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);
        
        $post = BlogPost::create([
            'title' => 'Post',
            'slug' => 'post',
            'content' => 'Content',
            'author_id' => $author->id,
            'blog_category_id' => $category->id,
            'status' => 'published',
        ]);

        $user->notify(new NewPostNotification($post));
        $notification = $user->notifications()->first();
        $notification->markAsRead();

        // Check if Inertia shared props contain the read notification
        $response = $this->actingAs($user)->get('/');
        
        $page = $response->viewData('page');
        $notifications = $page['props']['auth']['notifications'];
        
        $this->assertCount(1, $notifications);
        $this->assertEquals($notification->id, $notifications[0]['id']);
        $this->assertNotNull($notifications[0]['read_at']);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $category = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);
        
        $post1 = BlogPost::create([
            'title' => 'Post 1',
            'slug' => 'post-1',
            'content' => 'Content',
            'author_id' => $author->id,
            'blog_category_id' => $category->id,
            'status' => 'published',
        ]);

        $post2 = BlogPost::create([
            'title' => 'Post 2',
            'slug' => 'post-2',
            'content' => 'Content',
            'author_id' => $author->id,
            'blog_category_id' => $category->id,
            'status' => 'published',
        ]);

        $user->notify(new NewPostNotification($post1));
        $user->notify(new NewPostNotification($post2));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifications.read-all'));

        $response->assertRedirect(); // Back
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
