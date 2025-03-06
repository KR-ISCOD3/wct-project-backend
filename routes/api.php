<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;


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


Route::get('courses', [CourseController::class, 'index']); // Get all active courses
Route::post('courses', [CourseController::class, 'store']); // Add a new course
Route::put('courses/{id}/status', [CourseController::class, 'changeStatus']); // Soft delete (disable)
