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
        // Validate input fields
        $validator = $this->loginService->validateLogin($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Authenticate user and generate token
        $credentials = $validator->validated();
        $authData = $this->loginService->authenticateUser($credentials);

        if (!$authData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email/username or password',
            ], 401);
        }

        return $this->respondWithToken($authData['token'], $authData['role']);
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
