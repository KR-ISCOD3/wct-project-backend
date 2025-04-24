<?php

// use Illuminate\Http\Request;

use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\RegisterStudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\RegisterStudent;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;Route::middleware('auth.api')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// use App\Http\Controllers\CourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route to get user data, protected by Sanctum token
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'store'])->name('register');


// Protected routes (requires Sanctum token)
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


    // Teacher protected route
    Route::middleware(['role:teacher'])->get('/teacher', function () {
        return response()->json([
            'message' => 'Welcome, Teacher!',
        ]);
    });

    // Assistant or Admin protected route
    Route::middleware(['role:admin,assistant'])->group(function () {

        Route::prefix('courses')->group(function () {
            Route::get('/', [CourseController::class, 'index']);
            Route::post('/', [CourseController::class, 'store']);
            Route::put('/{id}', [CourseController::class, 'update']);
            Route::delete('/{id}', [CourseController::class, 'destroy']);
        });

        Route::prefix('registercourse')->group(function(){
            Route::get('/',[RegisterStudentController::class,'index']);
            Route::post('/',[RegisterStudentController::class,'store']);
        });
    });
});
