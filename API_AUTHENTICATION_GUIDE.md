# API Authentication Guide

## Overview

This API uses Laravel Sanctum for authentication with role-based access control powered by Spatie Laravel Permission package. The system provides secure token-based authentication with granular permission management.

## Authentication Endpoints

### Base URL

```
http://localhost/api
```

### 1. User Registration

**Endpoint:** `POST /api/auth/register`

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "User" // Optional, defaults to "User"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "code": 201,
    "message": "Registration successful",
    "data": {
        "token": "1|abc123...",
        "token_type": "Bearer",
        "expires_in": "1440 minutes",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "roles": [...],
            "permissions": [...],
            "created_at": "2025-08-22T10:00:00.000000Z",
            "updated_at": "2025-08-22T10:00:00.000000Z"
        }
    }
}
```

### 2. User Login

**Endpoint:** `POST /api/auth/login`

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "code": 201,
    "message": "Login successful",
    "data": {
        "token": "2|xyz789...",
        "token_type": "Bearer",
        "expires_in": "1440 minutes",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "roles": [
                {
                    "id": 6,
                    "name": "User",
                    "permissions": [...]
                }
            ],
            "permissions": [...],
            "created_at": "2025-08-22T10:00:00.000000Z",
            "updated_at": "2025-08-22T10:00:00.000000Z"
        }
    }
}
```

### 3. Get User Profile

**Endpoint:** `GET /api/auth/profile`

**Headers:**

```
Authorization: Bearer {your-token}
```

**Success Response (200):**

```json
{
    "success": true,
    "code": 200,
    "message": "Profile retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": [...],
        "permissions": [...],
        "profile": {...},
        "created_at": "2025-08-22T10:00:00.000000Z",
        "updated_at": "2025-08-22T10:00:00.000000Z"
    }
}
```

### 4. Logout

**Endpoint:** `POST /api/auth/logout`

**Headers:**

```
Authorization: Bearer {your-token}
```

**Success Response (200):**

```json
{
    "success": true,
    "code": 200,
    "message": "Logged out successfully",
    "data": null
}
```

### 5. Refresh Token

**Endpoint:** `POST /api/auth/refresh-token`

**Headers:**

```
Authorization: Bearer {your-token}
```

**Success Response (201):**

```json
{
    "success": true,
    "code": 201,
    "message": "Token refreshed successfully",
    "data": {
        "token": "3|new-token...",
        "token_type": "Bearer",
        "expires_in": "1440 minutes",
        "user": {...}
    }
}
```

### 6. Revoke All Tokens

**Endpoint:** `POST /api/auth/revoke-all-tokens`

**Headers:**

```
Authorization: Bearer {your-token}
```

**Success Response (200):**

```json
{
    "success": true,
    "code": 200,
    "message": "All tokens revoked successfully",
    "data": null
}
```

### 7. Change Password

**Endpoint:** `POST /api/auth/change-password`

**Headers:**

```
Authorization: Bearer {your-token}
```

**Request Body:**

```json
{
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "code": 200,
    "message": "Password changed successfully. Please login again.",
    "data": null
}
```

### 8. Get User Permissions

**Endpoint:** `GET /api/auth/permissions`

**Headers:**

```
Authorization: Bearer {your-token}
```

**Success Response (200):**

```json
{
    "success": true,
    "code": 200,
    "message": "User permissions retrieved successfully",
    "data": {
        "roles": ["User"],
        "permissions": ["view-products", "create-comments"],
        "direct_permissions": [],
        "role_permissions": ["view-products", "create-comments"]
    }
}
```

## Using Authentication

### 1. Making Authenticated Requests

Include the token in the Authorization header:

```javascript
// JavaScript/Axios example
const config = {
    headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
        Accept: "application/json",
    },
};

axios.get("/api/auth/profile", config);
```

```php
// PHP/cURL example
$headers = [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_URL, 'http://localhost/api/auth/profile');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
```

### 2. Handling Token Expiration

Tokens expire after 24 hours (1440 minutes) by default. When a token expires:

1. You'll receive a 401 Unauthorized response
2. Use the refresh token endpoint to get a new token
3. Or redirect user to login again

## Role-Based Access Control

### Available Roles (Hierarchical)

1. **Super Admin** - Full system access
2. **Admin** - Administrative access
3. **Manager** - Management level access
4. **Editor** - Content management access
5. **Sales** - Sales and client focused access
6. **User** - Basic user access
7. **Customer** - External user access

### Testing Role-Based Access

#### Test Endpoints

**Test Admin Access:**

```
GET /api/test/admin-only
Headers: Authorization: Bearer {super-admin-token}
```

**Test Permission:**

```
GET /api/test/can-view-users
Headers: Authorization: Bearer {token-with-view-users-permission}
```

**Get User Info:**

```
GET /api/test/user-info
Headers: Authorization: Bearer {any-valid-token}
```

**Test Specific Role:**

```
GET /api/test/role/{role-name}
Headers: Authorization: Bearer {any-valid-token}
```

**Test Specific Permission:**

```
GET /api/test/permission/{permission-name}
Headers: Authorization: Bearer {any-valid-token}
```

### Test User Credentials

After running the seeders, you can use these test accounts:

```json
{
    "email": "admin@tenderhub.com",
    "password": "password"
}
```

```json
{
    "email": "manager@tenderhub.com",
    "password": "password"
}
```

```json
{
    "email": "editor@tenderhub.com",
    "password": "password"
}
```

```json
{
    "email": "sales@tenderhub.com",
    "password": "password"
}
```

## Admin Panel Endpoints

### Admin Dashboard

**Endpoint:** `GET /api/admin/dashboard`

**Required Permission:** `view-admin-dashboard`

**Headers:**

```
Authorization: Bearer {admin-token}
```

### Role Management

**List Roles:**

```
GET /api/admin/roles
Required Permission: view-roles
```

**Create Role:**

```
POST /api/admin/roles
Required Permission: create-roles
Body: {"name": "New Role", "permissions": [1,2,3]}
```

**Update Role:**

```
PUT /api/admin/roles/{id}
Required Permission: edit-roles
```

**Delete Role:**

```
DELETE /api/admin/roles/{id}
Required Permission: delete-roles
```

### User Role Management

**Assign Role to User:**

```
POST /api/admin/users/{id}/assign-role
Required Permission: edit-users
Body: {"role_id": 2}
```

**Remove Role from User:**

```
POST /api/admin/users/{id}/remove-role
Required Permission: edit-users
Body: {"role_id": 2}
```

## Error Responses

### 401 Unauthorized

```json
{
    "success": false,
    "code": 401,
    "message": "Unauthenticated",
    "errors": "Token not provided or invalid",
    "data": null
}
```

### 403 Forbidden

```json
{
    "success": false,
    "code": 403,
    "message": "Access denied",
    "errors": "Insufficient permissions",
    "data": null
}
```

### 422 Validation Error

```json
{
    "success": false,
    "code": 422,
    "message": "Validation Error",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    },
    "data": null
}
```

### 500 Server Error

```json
{
    "success": false,
    "code": 500,
    "message": "Server Error",
    "errors": "Internal server error occurred",
    "data": null
}
```

## Best Practices

### 1. Token Storage

-   Store tokens securely (HttpOnly cookies for web apps)
-   Never expose tokens in URLs or logs
-   Clear tokens on logout

### 2. Token Management

-   Implement automatic token refresh
-   Handle token expiration gracefully
-   Revoke tokens when suspicious activity detected

### 3. API Security

-   Always use HTTPS in production
-   Implement rate limiting
-   Validate all input data
-   Log security events

### 4. Permission Checking

-   Check permissions server-side, not just client-side
-   Use middleware for route protection
-   Implement granular permissions

## Postman Collection

Import this collection to test the API:

```json
{
    "info": {
        "name": "Tender Hub API Authentication",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "auth": {
        "type": "bearer",
        "bearer": [
            {
                "key": "token",
                "value": "{{auth_token}}",
                "type": "string"
            }
        ]
    },
    "variable": [
        {
            "key": "base_url",
            "value": "http://localhost/api"
        },
        {
            "key": "auth_token",
            "value": ""
        }
    ]
}
```

## Support

For technical support or questions about the authentication system, please refer to the Laravel Sanctum and Spatie Permission documentation or contact the development team.
