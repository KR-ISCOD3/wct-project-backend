<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */

    protected $userRegistrationService;

    public function __construct(UserRegistrationService $userRegistrationService)
    {
        $this->userRegistrationService = $userRegistrationService;
    }

    public function store(Request $request)
    {
        // Validate input fields
        $validator = $this->userRegistrationService->validateInput($request);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Store the user data if validation passes
        try {
            $validated = $this->userRegistrationService->storeUser($request);

            // Create the user
            $user = $this->userRegistrationService->createUser($validated);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $user,
            ], 201);

        } catch (\Exception $e) {
            // Return error response without logging
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering the user.',
            ], 500);
        }
    }


    public function getallUser()
    {
        $users = User::all();

        return response()->json([
            'success'=>true,
            'message'=>'User retrieved successfully',
            'data'=>$users
        ],200);
    }


}
