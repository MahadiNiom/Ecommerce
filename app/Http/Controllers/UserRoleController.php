<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRolesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user's roles.
     */
    public function edit(User $user): View
    {
        return view('users.roles', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified user's roles in storage.
     */
    public function update(UpdateUserRolesRequest $request, User $user): RedirectResponse
    {
        $user->syncRoles(array_map('intval', $request->input('roles', [])));

        return redirect()->route('users.index')->with('success', 'Roles updated successfully.');
    }
}
