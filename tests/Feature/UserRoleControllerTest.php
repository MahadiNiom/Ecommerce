<?php

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function userRolesActingAdmin(): User
{
    $admin = User::factory()->create();

    foreach (Permissions::users() as $name) {
        $admin->givePermissionTo(Permission::findOrCreate($name));
    }

    test()->actingAs($admin);

    return $admin;
}

it('lists users with their roles', function () {
    userRolesActingAdmin();
    $user = User::factory()->create();
    $role = Role::create(['name' => 'editor']);
    $user->assignRole($role);

    $this->get(route('users.index'))
        ->assertOk()
        ->assertSee($user->email)
        ->assertSee('editor');
});

it('assigns roles to a user', function () {
    userRolesActingAdmin();
    $user = User::factory()->create();
    $role = Role::create(['name' => 'editor']);

    $this->put(route('users.roles.update', $user), ['roles' => [$role->id]])
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->hasRole('editor'))->toBeTrue();
});

it('removes all roles when none are selected', function () {
    userRolesActingAdmin();
    $user = User::factory()->create();
    $role = Role::create(['name' => 'editor']);
    $user->assignRole($role);

    $this->put(route('users.roles.update', $user), ['roles' => []])
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->roles)->toBeEmpty();
});

it('rejects assigning a role that does not exist', function () {
    userRolesActingAdmin();
    $user = User::factory()->create();

    $this->put(route('users.roles.update', $user), ['roles' => [999]])
        ->assertSessionHasErrors('roles.0');

    $this->assertDatabaseCount('model_has_roles', 0);
});

it('accepts role ids submitted as strings from the checkbox form', function () {
    userRolesActingAdmin();
    $user = User::factory()->create();
    $role = Role::create(['name' => 'editor']);

    $this->put(route('users.roles.update', $user), ['roles' => [(string) $role->id]])
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->hasRole('editor'))->toBeTrue();
});

it('grants the union of permissions across multiple roles', function () {
    $user = User::factory()->create();
    $productRole = Role::create(['name' => 'product viewer']);
    $productRole->givePermissionTo(Permission::findOrCreate('view products'));
    $orderRole = Role::create(['name' => 'order viewer']);
    $orderRole->givePermissionTo(Permission::findOrCreate('view orders'));
    $user->assignRole([$productRole, $orderRole]);
    $this->actingAs($user);

    $this->get(route('products.index'))->assertOk();
    $this->get(route('admin.orders.index'))->assertOk();
    $this->get(route('users.index'))->assertForbidden();
});

it('forbids managing users without the manage roles permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.index'))->assertForbidden();
});
