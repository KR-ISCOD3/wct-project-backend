<?php

namespace App\Services;

use App\Events\NewUserRegistered;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserRegistrationService
{
    /**
     * Validate the input fields.
     */
    public function validateInput(Request $request)
    {
        // Create a validator for the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@etec\.com$/', 'unique:users,email'],
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:admin,teacher,student,assistant',
            'gender_id' => 'nullable|integer|exists:genders,id',
            'image' => 'nullable',
            'work_status' => 'nullable|string|max:255',
            'shift' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        // // Check if validation fails
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        // Return the validated data
        return $validator;
    }




    /**
     * Store user data after validation.
     */
    public function storeUser(Request $request)
    {
        // Get the validated data from the request
        $validated = $request->only(['name', 'email', 'password', 'role', 'gender_id', 'image','work_status','shift','position','phone_number']);

        // Default role to 'teacher' if not provided
        $validated['role'] = $validated['role'] ?? 'teacher';

        
        $validated['password'] = Hash::make($validated['password']);


        return $validated;
    }

    /**
     * Create a new user in the database.
     */
    public function createUser(array $validated)
    {
          // Create the user
          $user = User::create($validated);

          event(new NewUserRegistered($user));
          // Generate token for the user using Sanctum
          $token = $user->createToken('auth_token')->plainTextToken;

          $role = $user->role;

          return ['role' => $role, 'token' => $token, 'user' => $user];
    }


}
