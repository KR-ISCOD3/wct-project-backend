<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginService
{
    /**
     * Validate the login request data.
     */
    public function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email_or_username' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
    }

    /**
     * Authenticate the user and return a token.
     */
    public function authenticateUser(array $credentials)
{
    // Find user by email or username
    $user = User::where('email', $credentials['email_or_username'])
                ->orWhere('name', $credentials['email_or_username'])
                ->first();

    // ✅ Check if user exists
    if (!$user) {
        return null;
    }

    // ✅ Block login if account is deactivated
    if ($user->deleted_status === 'unactive') {
        return 'deactivated';
    }

    // ✅ Check if password is correct
    if (!Hash::check($credentials['password'], $user->password)) {
        return null;
    }

    // ✅ Generate token using Laravel Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    // ✅ Get role
    $role = UserRole::tryFrom($user->role) ?? UserRole::TEACHER;

    return [
        'token' => $token,
        'role' => $role->value,
        'user' => $user
    ];
}


    /**
     * Logout user by revoking all tokens.
     */
    public function logoutUser($user)
    {
        $user->tokens()->delete();
    }
}
