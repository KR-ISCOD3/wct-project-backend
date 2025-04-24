<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Error response
     */
    protected function errorResponse($message, $status = 400, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Respond with JWT token
     */
    protected function respondWithToken($token, $role)
    {
        return $this->successResponse('Token generated successfully', [
            'role' => $role,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null
        ]);
    }

    /**
     * Exception response
     */
    protected function responseError(Exception $e, $status = 500)
    {
        return $this->errorResponse('An error occurred', $status, [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'message' => $e->getMessage(),
        ]);
    }

    public function responseWithLogout(){

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ],200);
    }
}
