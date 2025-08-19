<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $permissions = Permission::all();
            
            return $this->success(
                PermissionResource::collection($permissions),
                'Permissions retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve permissions',
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
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'string|max:255',
        ]);

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? 'web'
            ]);

            return $this->success(
                new PermissionResource($permission),
                'Permission created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to create permission',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        try {
            return $this->success(
                new PermissionResource($permission),
                'Permission retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve permission',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'guard_name' => 'string|max:255',
        ]);

        try {
            $permission->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? $permission->guard_name
            ]);

            return $this->success(
                new PermissionResource($permission),
                'Permission updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to update permission',
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        try {
            // Check if permission is being used by any roles
            $rolesCount = $permission->roles()->count();
            if ($rolesCount > 0) {
                return $this->error(
                    'Cannot delete permission. It is assigned to ' . $rolesCount . ' role(s).',
                    422
                );
            }

            $permission->delete();

            return $this->success(
                null,
                'Permission deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->error(
                'Failed to delete permission',
                500,
                $e->getMessage()
            );
        }
    }
}
