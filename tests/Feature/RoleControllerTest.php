<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function rolesActingAdmin(): User
{
    $admin = User::factory()->create();

    $admin->givePermissionTo(Permission::findOrCreate('manage roles and permissions'));

    test()->actingAs($admin);

    return $admin;
}

it('lists roles for a user with the manage roles permission', function () {
    rolesActingAdmin();
    Role::create(['name' => 'editor']);

    $this->get(route('roles.index'))
        ->assertOk()
        ->assertSee('editor')
        ->assertSee('Create Role');
});

it('creates a role with its permissions', function () {
    rolesActingAdmin();
    $permission = Permission::create(['name' => 'publish posts']);

    $this->post(route('roles.store'), [
        'name' => 'editor',
        'permissions' => [$permission->id],
    ])->assertRedirect(route('roles.index'));

    $role = Role::where('name', 'editor')->first();

    expect($role)->not->toBeNull()
        ->and($role->hasPermissionTo('publish posts'))->toBeTrue();
});

it('updates a role name and replaces its permissions', function () {
    rolesActingAdmin();
    $role = Role::create(['name' => 'editor']);
    $old = Permission::create(['name' => 'edit drafts']);
    $new = Permission::create(['name' => 'publish posts']);
    $role->givePermissionTo($old);

    $this->put(route('roles.update', $role), [
        'name' => 'senior-editor',
        'permissions' => [$new->id],
    ])->assertRedirect(route('roles.index'));

    $role->refresh();

    expect($role->name)->toBe('senior-editor')
        ->and($role->permissions->pluck('name'))->toContain('publish posts')
        ->and($role->permissions->pluck('name'))->not->toContain('edit drafts');
});

it('rejects creating a role with a duplicate name', function () {
    rolesActingAdmin();
    Role::create(['name' => 'editor']);

    $this->post(route('roles.store'), ['name' => 'editor'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('roles', 1);
});

it('rejects assigning a permission that does not exist', function () {
    rolesActingAdmin();

    $this->post(route('roles.store'), [
        'name' => 'editor',
        'permissions' => [999],
    ])->assertSessionHasErrors('permissions.0');
});

it('does not delete the protected admin and user roles', function () {
    rolesActingAdmin();
    $adminRole = Role::create(['name' => 'admin']);
    $userRole = Role::create(['name' => 'user']);

    foreach ([$adminRole, $userRole] as $role) {
        $this->from(route('roles.index'))
            ->delete(route('roles.destroy', $role))
            ->assertSessionHas('error');
    }

    $this->assertDatabaseCount('roles', 2);
});

it('deletes a custom role', function () {
    rolesActingAdmin();
    $role = Role::create(['name' => 'editor']);

    $this->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'));

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

it('forbids listing roles without the manage roles permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('roles.index'))->assertForbidden();
});
