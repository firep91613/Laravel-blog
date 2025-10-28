<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        Setting::factory()->adminLogo()->create();
        Setting::factory()->defaultUsersAvatar()->create();

        $this->admin = User::factory()->roleAdmin()->create();
        $this->actingAs($this->admin);
    }

    public function testShowListOfRoles(): void
    {
        Role::factory()->author()->count(20)->create();

        $response = $this->get(route('admin.roles.index'));
        $response->assertOk();
        $response->assertViewIs('admin.roles.index');
        $response->assertViewHas('roles');
        $this->assertCount(5, $response->original->getData()['roles']);
    }

    public function testShowRoleInfo(): void
    {
        $role = Role::factory()->author()->create();

        $response = $this->get(route('admin.roles.show', $role->id));
        $response->assertOk();
        $response->assertViewIs('admin.roles.show');
        $response->assertViewHas('role', function($viewRole) use ($role) {
            return $viewRole->name == $role->name;
        });
    }

    public function testShowRoleEditPage(): void
    {
        $role = Role::factory()->author()->create();

        $response = $this->get(route('admin.roles.edit', $role->id));
        $response->assertOk();
        $response->assertViewIs('admin.roles.edit');
        $response->assertSee($role->name);
    }

    public function testCanUpdateRole(): void
    {
        $role = Role::factory()->author()->create();
        $newRole = Role::factory()->editor()->make();

        $response = $this->put(route('admin.roles.update', $role->id), $newRole->toArray());
        $response->assertSessionHas('success', __('messages.role.updated'));
        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', [
            'name' => $newRole->name
        ]);
    }

    public function testCanDeleteRole(): void
    {
        User::factory()->roleUser()->create();
        $role = Role::factory()->author()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->delete(route('admin.roles.destroy', $role->id));
        $user->refresh();
        $this->assertTrue($user->role->name == 'user');
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success', __('messages.role.deleted'));
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id
        ]);
    }

    public function testCanAddRole(): void
    {
        $role = Role::factory()->author()->make();

        $response = $this->post(route('admin.roles.store'), [
            'name' => $role->name
        ]);
        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success', __('messages.role.added'));
        $this->assertDatabaseHas('roles', [
            'name' => $role->name
        ]);
    }
}
