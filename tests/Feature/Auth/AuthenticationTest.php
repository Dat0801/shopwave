<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('shop.index', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_users_are_redirected_to_google_for_authentication(): void
    {
        Socialite::fake('google');

        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect();
    }

    public function test_users_can_authenticate_with_google(): void
    {
        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => 'google-123',
            'name' => 'Jamie Nguyen',
            'email' => 'jamie@example.com',
        ]));

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticated();
        $response->assertRedirect(route('shop.index', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'jamie@example.com',
            'google_id' => 'google-123',
        ]);
    }

    public function test_google_login_links_existing_user_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'linked@example.com',
            'google_id' => null,
        ]);

        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => 'google-789',
            'name' => 'Linked User',
            'email' => 'linked@example.com',
        ]));

        $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google-789',
        ]);
    }

    public function test_google_login_requires_email(): void
    {
        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => 'google-456',
            'name' => 'No Email',
        ]));

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }
}
