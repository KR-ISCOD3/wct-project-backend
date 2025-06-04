<?php

use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\RegisterStudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\StudentController;
use App\Http\Controllers\Teacher\ClassController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'store'])->name('register');

// Authenticated user route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ✅ Allow all authenticated users to fetch course and building lists
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/buildings', [BuildingController::class, 'index']);

    // 🛡️ Admin & Assistant Only
    Route::middleware(['role:admin,assistant'])->group(function () {

        // Course Management
        Route::prefix('courses')->group(function () {
            Route::post('/', [CourseController::class, 'store']);
            Route::put('/{id}', [CourseController::class, 'update']);
            Route::delete('/{id}', [CourseController::class, 'destroy']);
        });

        // Register Student Management
        Route::prefix('registercourse')->group(function () {
            Route::get('/', [RegisterStudentController::class, 'index']);
            Route::post('/', [RegisterStudentController::class, 'store']);
            Route::put('/{id}', [RegisterStudentController::class, 'update']);
            Route::delete('/{id}', [RegisterStudentController::class, 'destroy']);
            Route::put('/register-student/{id}/mark-printed', [RegisterStudentController::class, 'markAsPrinted']);
        });

        // Instructor Management
        Route::prefix('instructors')->group(function () {
            Route::get('/', [InstructorController::class, 'index']);
            Route::get('/{id}', [InstructorController::class, 'show']);
            Route::delete('/{id}', [InstructorController::class, 'destroy']);
        });

        // Building Management
        Route::prefix('buildings')->group(function () {
            Route::post('/', [BuildingController::class, 'store']);
            Route::put('/{id}', [BuildingController::class, 'update']);
            Route::delete('/{id}', [BuildingController::class, 'destroy']);
        });


    });

    // Optional: Teacher-only routes can go here
    Route::middleware(['role:teacher'])->group(function () {
        // Route::get('/teacher', function () {
        //     return response()->json(['message' => 'Welcome, Teacher!']);
        // });

         // Class Management
         Route::prefix('classes')->group(function () {
            Route::get('/teacher/{id}', [ClassController::class, 'getByTeacher']);
            Route::get('/', [ClassController::class, 'index']);
            Route::post('/', [ClassController::class, 'store']);
            Route::put('/{id}', [ClassController::class, 'update']);
            Route::delete('/{id}', [ClassController::class, 'destroy']);
        });

        // Student Management for Teachers
        Route::prefix('students')->group(function () {
            Route::get('/', [StudentController::class, 'index']);          // List all students
            Route::post('/', [StudentController::class, 'store']);         // Add a student
            Route::get('/{id}', [StudentController::class, 'show']);       // Get a student by ID
            Route::put('/{id}', [StudentController::class, 'update']);     // Update a student
            Route::delete('/{id}', [StudentController::class, 'destroy']); // Delete a student

            Route::get('/class/{classId}', [StudentController::class, 'getByClassId']);
        });
    });
});



