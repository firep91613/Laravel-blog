<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testGuestCanSeeEmailForm(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertOk();
        $response->assertViewIs('public.auth.forgot-password');
    }

    public function testAuthUserCannotSeeEmailForm(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('password.request'));
        $response->assertRedirect('/');
    }

    public function testGuestCanStoreEmailForm(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function testAuthUserCannotStoreEmailForm(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('password.email'), ['email' => $user->email]);
        $response->assertRedirect();
    }

    public function testGuestCanShowResetForm(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', ['token' => $token]));
        $response->assertOk();
        $response->assertViewIs('public.auth.reset-password');
    }

    public function testAuthUserCannotShowResetForm(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->actingAs($user)->get(route('password.reset', ['token' => $token]));
        $response->assertRedirect('/');
        $response->assertStatus(302);
    }

    public function testGuestCanUpdatePassword(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $newPassword = 'aaaaaaaaaaaaaaaaaaaaaaaaa';

        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ]);
        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
        $response->assertRedirect(route('public.auth.showForm'));
    }

    public function testAuthUserCannotUpdatePassword(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $newPassword = 'aaaaaaaaaaaaaaaaaaaaaaaaa';

        $response = $this->actingAs($user)->post(route('password.update'), [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ]);
        $response->dump();
        $this->assertFalse(Hash::check($newPassword, $user->password));
        $response->assertRedirect();
    }
}
