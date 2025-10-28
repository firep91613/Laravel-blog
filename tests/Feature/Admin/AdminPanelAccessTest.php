<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();
    }

    public function testAdminUserCanAccessToAdminPanel(): void
    {
        $admin = User::factory()->roleAdmin()->create();

        $response = $this->actingAs($admin)->get('/admin');;
        $response->assertStatus(200);
        $response->assertSee('Панель администратора');
    }

    public function testNotAdminUserRedirectedToLogin()
    {
        $user = User::factory()->roleEditor()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function testGuestRedirectedToLogin()
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    public function testAdminUserCanLogout(): void
    {
        $admin = User::factory()->roleAdmin()->create();

        $response = $this->actingAs($admin)->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }
}
