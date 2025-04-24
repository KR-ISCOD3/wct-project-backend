<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = Course::all();
        return response()->json([
            'status' => 'success',
            'data' => $courses
        ], 200); // 200 is for OK
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'category' => 'required|array|min:1', // category should always be an array, even if it's one item
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new Course object
        $course = new Course();
        $course->name = $request->name;
        $course->category = $request->category;  // Just pass the array directly

        $course->save();

        return response()->json([
            'message' => 'Success...',
        ], 201);
    }


    /**
     * Display the specified course.
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 200); // 200 is for OK
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|array',
            'category.*' => 'in:programming,networking,design',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $course->update([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Course updated successfully',
            'data' => $course
        ], 200);
    }

    /**
     * Change course status to disabled instead of deleting.
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->update(['status' => 'disabled']);

        return response()->json([
            'status' => 'success',
            'message' => 'Course disabled successfully',
            'category' => $course
        ], 200); // 200 is for OK
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $course->update(['status' => 'enalble']);

        // Refresh the model to get the latest data from the database
        $course->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Course disabled successfully',
            'category' => $course
        ], 200); // 200 is for OK
    }
}
