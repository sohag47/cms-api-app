<?php

// IDE Helper for Spatie Laravel Permission Package
// This file helps IDEs understand the methods added by the HasRoles trait

namespace App\Models {
    /**
     * @mixin \Spatie\Permission\Traits\HasRoles
     */
    class User
    {
        // These methods are provided by the HasRoles trait but IDEs don't always detect them
        
        /**
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function getAllPermissions() {}
        
        /**
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function getDirectPermissions() {}
        
        /**
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function getPermissionsViaRoles() {}
        
        /**
         * @param string|array|\Spatie\Permission\Contracts\Role $roles
         * @param string|null $guard
         * @return bool
         */
        public function hasRole($roles, $guard = null) {}
        
        /**
         * @param string|\Spatie\Permission\Contracts\Permission $permission
         * @param string|null $guard
         * @return bool
         */
        public function hasPermissionTo($permission, $guard = null) {}
        
        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function roles() {}
        
        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function permissions() {}
    }
}
