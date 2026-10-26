<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Role\App\Models\Role;
use Modules\Role\App\Services\RoleService;
use Modules\User\App\Models\User;

class RoleAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:'.config('permissions_list.USER_ROLE_ASSIGNMENT', false))->only(['edit', 'update']);
    }

    public function edit(User $user, RoleService $roleService): View
    {
        $roles = Role::latest()->get();

        return view('user::role-assignment', compact('user', 'roles', 'roleService'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['roles' => 'required|array|min:1', 'roles.*' => 'required|integer|exists:roles,id']);
        $adminRole = Role::findByName(Role::ADMIN);
        if ($user->hasRole(Role::ADMIN) && ! in_array($adminRole->id, array_map('intval', $data['roles']), true) && User::query()->role(Role::ADMIN)->count() <= 1) {
            throw ValidationException::withMessages(['roles' => 'Nie można odebrać uprawnień ostatniemu administratorowi.']);
        }
        $user->syncRoles(array_map('intval', $data['roles']));

        return to_route(config('app.panel_prefix', 'panel').'.users.index')->with('success', __('user::messages.role_assignment'));
    }
}
