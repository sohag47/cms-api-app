<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate a Sanctum token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Define token expiration (in minutes)
        $expirationMinutes = config('sanctum.expiration', 60); // Default to 60 minutes

        return $this->respondWithCreated([
            'token'=> $token,
            'token_type' => 'Bearer',
            'expires_at' => $expirationMinutes,
            'user'=> $user,
        ], 'Registration successful');
    }

    public function login(Request $request)
    {
        $rules = [ 
            'email' => 'required|string|email',
            'password' => 'required',  
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        if (Auth::attempt($request->all())) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            $expirationMinutes = config('sanctum.expiration', 60); // Default to 60 minutes

            return $this->respondWithCreated([
                'token'=> $token,
                'token_type' => 'Bearer',
                'expires' => $expirationMinutes,
                'user'=> $user,
            ]);
        }else{
            return $this->respondUnauthorizedError('Invalid credentials');
        }
    }
    public function profile()
    {
        return $this->respondWithCreated([
            'user'=> Auth::user(),
        ]);
    }

    // Handle logout
    public function logout(Request $request)
    {
        if(Auth::check()){
            $request->user()->currentAccessToken()->delete();
            return $this->respondWithSuccess(null, 'Logged out successfully', Response::HTTP_NO_CONTENT);
        }
        return $this->respondUnauthorizedError();
        
    }
}
