<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Generate authentication token with user data and roles
     */
    public function generateToken($user, $message)
    {
        // Load user with roles and permissions for the response
        $user->load(['roles', 'permissions']);

        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(config('sanctum.expiration', 1440)))->plainTextToken;
        $expirationMinutes = config('sanctum.expiration', 1440); // Default to 24 hours

        return $this->respondWithCreated([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expirationMinutes,
            'user' => new UserResource($user),
        ], $message);
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'sometimes|string|exists:roles,name',
            ]);

            if ($validator->fails()) {
                return $this->respondValidationError($validator->errors());
            }

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto-verify for API registration
            ]);

            // Assign default role or requested role
            $roleName = $request->role ?? 'User'; // Default role is 'User'
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $user->assignRole($role);
            }

            return $this->generateToken($user, 'Registration successful');

        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Registration failed: '.$e->getMessage());
        }
    }

    /**
     * Login user and generate token
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->respondValidationError($validator->errors());
            }

            $credentials = $request->only('email', 'password');

            if (! Auth::attempt($credentials)) {
                return $this->respondUnauthorizedError('Invalid credentials');
            }

            $user = Auth::user();

            // Revoke all previous tokens for security
            $user->tokens()->delete();

            return $this->generateToken($user, 'Login successful');

        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Login failed: '.$e->getMessage());
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile()
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return $this->respondUnauthorizedError('User not authenticated');
            }

            return $this->respondWithSuccess(
                new UserResource($user),
                'Profile retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Failed to retrieve profile: '.$e->getMessage());
        }
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        try {
            if (Auth::check()) {
                // Revoke current token
                $request->user()->currentAccessToken()->delete();

                return $this->respondWithSuccess(null, 'Logged out successfully');
            }

            return $this->respondUnauthorizedError('User not authenticated');
        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Logout failed: '.$e->getMessage());
        }
    }

    /**
     * Revoke all tokens for the authenticated user
     */
    public function revokeAllTokens(Request $request)
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();

            return $this->respondWithSuccess(null, 'All tokens revoked successfully');
        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Failed to revoke tokens: '.$e->getMessage());
        }
    }

    /**
     * Refresh user token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = Auth::user();

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Generate new token
            return $this->generateToken($user, 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Token refresh failed: '.$e->getMessage());
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->respondValidationError($validator->errors());
            }

            $user = Auth::user();

            if (! Hash::check($request->current_password, $user->password)) {
                return $this->respondValidationError(['current_password' => ['Current password is incorrect']]);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Revoke all tokens for security
            $user->tokens()->delete();

            return $this->respondWithSuccess(null, 'Password changed successfully. Please login again.');
        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Password change failed: '.$e->getMessage());
        }
    }

    /**
     * Get user permissions and roles
     */
    public function permissions()
    {
        try {
            $user = Auth::user();
            $allPermissions = $user->getAllPermissions();
            $directPermissions = $user->getDirectPermissions();
            $rolePermissions = $user->getPermissionsViaRoles();

            return $this->respondWithSuccess([
                'roles' => $user->roles->pluck('name'),
                'permissions' => [
                    'all' => $allPermissions->pluck('name'),
                    'direct' => $directPermissions->pluck('name'),
                    'via_roles' => $rolePermissions->pluck('name'),
                ],
                'grouped_permissions' => $this->groupUserPermissions($allPermissions),
                'permissions_summary' => [
                    'total_permissions' => $allPermissions->count(),
                    'direct_permissions' => $directPermissions->count(),
                    'role_permissions' => $rolePermissions->count(),
                    'total_roles' => $user->roles->count(),
                ],
            ], 'User permissions retrieved successfully');
        } catch (\Exception $e) {
            return $this->respondServerError(null, 'Failed to retrieve permissions: '.$e->getMessage());
        }
    }

    /**
     * Group user permissions by modules
     */
    private function groupUserPermissions($permissions): array
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);
            $action = $parts[0] ?? 'other';
            $module = isset($parts[1]) ? implode('-', array_slice($parts, 1)) : 'general';

            // Group by module name (capitalize for display)
            $group = ucwords(str_replace('-', ' ', $module));

            if (! isset($grouped[$group])) {
                $grouped[$group] = [
                    'group_name' => $group,
                    'permissions' => [],
                    'count' => 0,
                ];
            }

            $grouped[$group]['permissions'][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'module' => $module,
            ];

            $grouped[$group]['count']++;
        }

        // Sort groups by name
        ksort($grouped);

        return array_values($grouped);
    }
}
