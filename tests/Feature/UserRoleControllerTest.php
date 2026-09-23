<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function userRolesActingAdmin(): User
{
    $admin = User::factory()->create();

    $admin->givePermissionTo(Permission::findOrCreate('manage roles and permissions'));

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

it('forbids managing users without the manage roles permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.index'))->assertForbidden();
});
