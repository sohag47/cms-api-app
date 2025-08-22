<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => RoleResource::collection($this->roles),
            'permissions' => $this->getGroupedPermissions(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Group permissions by their modules
     */
    private function getGroupedPermissions(): array
    {
        $permissions = $this->getAllPermissions();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);
            $action = $parts[0] ?? 'other';
            $module = isset($parts[1]) ? implode('-', array_slice($parts, 1)) : 'general';

            // Group by module name (capitalize for display)
            $group = ucwords(str_replace('-', ' ', $module));

            if (! isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            $grouped[$group][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'module' => $module,
            ];
        }

        return $grouped;
    }
}
