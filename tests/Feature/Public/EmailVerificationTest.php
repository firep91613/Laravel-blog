<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testUnverifiedAuthUserCanShowVerifyPage(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));
        $response->assertOk();
        $response->assertViewIs('public.email.verify-email');
    }

    public function testVerifiedAuthUserCannotShowVerifyPage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));
        $response->assertRedirect('/');
        $response->assertSessionHas('error', __('messages.common.email_already_confirmed'));
    }

    public function testGuestCannotShowVerifyPage(): void
    {
        $response = $this->get(route('verification.notice'));
        $response->assertRedirect(route('public.auth.showForm'));
    }

    public function testUnverifiedAuthUserCanVerifyEmail(): void
    {
        $user = User::factory()->unverified()->create();
        $signedUrl = URL::signedRoute('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ], now()->addMinutes(60));

        $response = $this->actingAs($user)->get($signedUrl);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/');
    }

    public function testVerifiedAuthUserCannotVerifyEmail(): void
    {
        $user = User::factory()->create();
        $date = $user->getEmailVerifiedAt();
        $signedUrl = URL::signedRoute('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ], now()->addMinutes(60));

        $response = $this->actingAs($user)->get($signedUrl);
        $this->assertEquals($date, $user->fresh()->getEmailVerifiedAt());
        $response->assertRedirect('/');
    }

    public function testGuestCannotVerifyEmail(): void
    {
        $user = User::factory()->unverified()->create();
        $signedUrl = URL::signedRoute('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email)
        ]);

        $response = $this->get($signedUrl);
        $response->assertRedirect(route('public.auth.showForm'));
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function testUnverifiedAuthUserCanResendVerificationEmail(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('verification.send'));
        $response->assertRedirect();
        $response->assertSessionHas('status', __('messages.common.verification_link_sent'));
    }

    public function testGuestCannotResendVerificationEmail(): void
    {
        $response = $this->post(route('verification.send'));
        $response->assertStatus(302);
        $response->assertRedirect(route('public.auth.showForm'));

    }
}
