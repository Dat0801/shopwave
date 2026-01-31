<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FollowFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user()
    {
        $follower = User::factory()->create();
        $author = User::factory()->create();

        $response = $this->actingAs($follower)->post(route('users.follow', $author->id));

        $response->assertSessionHas('success');
        $this->assertTrue($follower->isFollowing($author));
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'following_id' => $author->id,
        ]);
    }

    public function test_user_can_unfollow_another_user()
    {
        $follower = User::factory()->create();
        $author = User::factory()->create();

        $follower->followings()->attach($author->id);

        $response = $this->actingAs($follower)->delete(route('users.unfollow', $author->id));

        $response->assertSessionHas('success');
        $this->assertFalse($follower->isFollowing($author));
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $follower->id,
            'following_id' => $author->id,
        ]);
    }

    public function test_followers_receive_notification_on_new_post()
    {
        Notification::fake();

        $follower = User::factory()->create();
        $author = User::factory()->create(['role' => 'admin']); // Assuming admin creates posts

        $follower->followings()->attach($author->id);

        $category = BlogCategory::create(['name' => 'News', 'slug' => 'news', 'is_active' => true]);

        $postData = [
            'title' => 'New Awesome Post',
            'content' => 'Content here',
            'blog_category_id' => $category->id,
            'status' => 'published',
            'image' => null,
        ];

        $response = $this->actingAs($author)->post(route('admin.blog.store'), $postData);

        $response->assertRedirect();
        
        Notification::assertSentTo(
            [$follower],
            NewPostNotification::class
        );

        // Ensure author is not notified (unless they follow themselves, which we prevented)
        Notification::assertNotSentTo(
            [$author],
            NewPostNotification::class
        );
    }
}
