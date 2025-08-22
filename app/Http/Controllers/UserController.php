<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with(['roles', 'permissions'])->paginate(10);

            return $this->success(
                UserCollection::make($users),
                'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve users',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            if ($request->has('roles')) {
                $roles = Role::whereIn('id', $request->roles)->get();
                $user->assignRole($roles);
            }

            $user->load(['roles', 'permissions']);

            return $this->success(
                new UserResource($user),
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to create user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            $user->load(['roles', 'permissions']);

            return $this->success(
                UserResource::make($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:8',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            if ($request->has('roles')) {
                $roles = Role::whereIn('id', $request->roles)->get();
                $user->syncRoles($roles);
            }

            $user->load(['roles', 'permissions']);

            return $this->success(
                new UserResource($user),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to update user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return $this->success(
                null,
                'User deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to delete user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $role = Role::findById($request->role_id);
            $user->assignRole($role);

            $user->load(['roles', 'permissions']);

            return $this->success(
                new UserResource($user),
                'Role assigned to user successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to assign role to user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $role = Role::findById($request->role_id);
            $user->removeRole($role);

            $user->load(['roles', 'permissions']);

            return $this->success(
                new UserResource($user),
                'Role removed from user successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to remove role from user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Give permission directly to user
     */
    public function givePermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        try {
            $permission = Permission::findById($request->permission_id);
            $user->givePermissionTo($permission);

            $user->load(['roles', 'permissions']);

            return $this->success(
                new UserResource($user),
                'Permission given to user successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to give permission to user',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Revoke permission from user
     */
    public function revokePermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        try {
            $permission = Permission::findById($request->permission_id);
            $user->revokePermissionTo($permission);

            $user->load(['roles', 'permissions']);

            return $this->success(
                new UserResource($user),
                'Permission revoked from user successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to revoke permission from user',
                500,
                $e->getMessage()
            );
        }
    }
}
