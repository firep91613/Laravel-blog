<?php

namespace Tests\Feature\Public;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->siteTitle()->create();
        Setting::factory()->siteSubtitle()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testGuestsCanSeeRegisterForm(): void
    {
        $response = $this->get(route('public.register.showForm'));
        $response->assertOk();
        $response->assertViewIs('public.register.showForm');
    }

    public function testAuthUsersCannotSeeRegisterForm(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('public.register.showForm'));
        $response->assertFound();
    }

    public function testUsersCanRegister(): void
    {
        Role::factory()->user()->create();

        $response = $this->post(route('public.register.register'), [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);
        $user = User::where('email', 'test@test.com')->first();
        $this->assertTrue($user->isUser());
        $response->assertFound();
        $response->assertSessionHas('success', __('messages.common.registration_success'));
        $response->assertRedirect(route('verification.notice'));
    }

    public function testUsersAlreadyRegisteredCannotRegister(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('public.register.register'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect(url()->previous());
        $response->assertSessionHasErrors('email');
    }
}
