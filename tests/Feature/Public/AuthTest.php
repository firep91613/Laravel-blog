<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testGuestsCanSeeAuthForm(): void
    {
        $response = $this->get(route('public.auth.showForm'));
        $response->assertOk();
        $response->assertViewIs('public.auth.showForm');
    }

    public function testAuthUsersCannotSeeAuthForm(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('public.auth.showForm'));
        $response->assertFound();
    }

    public function testAuthenticate(): void
    {
        $user = User::factory()->create();
        $response = $this->post(route('public.auth.authenticate'), [
            'email' => $user->email,
            'password' => '123456789',
        ]);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    public function testUsersCannotAuthenticateWithInvalidData(): void
    {
        $user = User::factory()->create();

        $this->post(route('public.auth.authenticate'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function testAuthenticationErrors(): void
    {
        $response = $this->post(route('public.auth.authenticate'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function testUsersCanLogout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('public.auth.logout'));
        $this->assertGuest();
        $response->assertRedirect(route('public.auth.showForm'));
    }
}
