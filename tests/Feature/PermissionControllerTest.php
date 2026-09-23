<?php

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

function permissionsActingAdmin(): User
{
    $admin = User::factory()->create();

    foreach (Permissions::permissions() as $name) {
        $admin->givePermissionTo(Permission::findOrCreate($name));
    }

    test()->actingAs($admin);

    return $admin;
}

it('lists permissions for a user with the manage roles permission', function () {
    permissionsActingAdmin();
    Permission::create(['name' => 'publish posts']);

    $this->get(route('permissions.index'))
        ->assertOk()
        ->assertSee('publish posts')
        ->assertSee('Create permission');
});

it('creates a permission', function () {
    permissionsActingAdmin();

    $this->post(route('permissions.store'), ['name' => 'publish posts'])
        ->assertRedirect(route('permissions.index'));

    $this->assertDatabaseHas('permissions', ['name' => 'publish posts']);
});

it('rejects creating a duplicate permission', function () {
    permissionsActingAdmin();
    Permission::create(['name' => 'publish posts']);

    $this->post(route('permissions.store'), ['name' => 'publish posts'])
        ->assertSessionHasErrors('name');

    expect(Permission::where('name', 'publish posts')->count())->toBe(1);
});

it('updates a permission name', function () {
    permissionsActingAdmin();
    $permission = Permission::create(['name' => 'edit posts']);

    $this->put(route('permissions.update', $permission), ['name' => 'edit drafts'])
        ->assertRedirect(route('permissions.index'));

    expect($permission->fresh()->name)->toBe('edit drafts');
});

it('deletes a permission', function () {
    permissionsActingAdmin();
    $permission = Permission::create(['name' => 'temporary access']);

    $this->delete(route('permissions.destroy', $permission))
        ->assertRedirect(route('permissions.index'));

    $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
});

it('forbids listing permissions without the manage roles permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('permissions.index'))->assertForbidden();
});
