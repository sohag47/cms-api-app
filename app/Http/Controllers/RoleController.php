<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::with('permissions')->get();
            
            return $this->success(
                RoleResource::collection($roles),
                'Roles retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve roles',
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
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::create(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->givePermissionTo($permissions);
            }
            
            $role->load('permissions');

            return $this->success(
                new RoleResource($role),
                'Role created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to create role',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        try {
            $role->load('permissions');
            
            return $this->success(
                new RoleResource($role),
                'Role retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve role',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role->update(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }
            
            $role->load('permissions');

            return $this->success(
                new RoleResource($role),
                'Role updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to update role',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            // Check if role is being used by any users
            $usersCount = $role->users()->count();
            if ($usersCount > 0) {
                return $this->error(
                    'Cannot delete role. It is assigned to ' . $usersCount . ' user(s).',
                    422
                );
            }

            $role->delete();

            return $this->success(
                null,
                'Role deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to delete role',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id'
        ]);

        try {
            $permission = Permission::findById($request->permission_id);
            $role->givePermissionTo($permission);
            
            $role->load('permissions');

            return $this->success(
                new RoleResource($role),
                'Permission assigned to role successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to assign permission to role',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id'
        ]);

        try {
            $permission = Permission::findById($request->permission_id);
            $role->revokePermissionTo($permission);
            
            $role->load('permissions');

            return $this->success(
                new RoleResource($role),
                'Permission revoked from role successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to revoke permission from role',
                500,
                $e->getMessage()
            );
        }
    }
}
