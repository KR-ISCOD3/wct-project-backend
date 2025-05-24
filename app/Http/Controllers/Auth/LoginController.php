<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(Request $request)
    {
        $validator = $this->loginService->validateLogin($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = $validator->validated();
        $authData = $this->loginService->authenticateUser($credentials);

        if ($authData === 'deactivated') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deleted. Please contact the administrator.',
            ], 403);
        }

        if (!$authData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email/username or password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $authData['token'],
            'role' => $authData['role'],
            'user' => $authData['user']
        ]);
    }


    public function logout(Request $request)
    {
        $this->loginService->logoutUser($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
