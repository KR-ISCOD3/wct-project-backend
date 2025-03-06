<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;


class CourseController extends Controller
{
    // Fetch only active courses
    public function index()
    {
        return response()->json(Course::where('status', 1)->get());
    }

    // Store a new course (POST request)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255'
        ]);

        $course = Course::create([
            'name' => $request->name,
            'category' => $request->category,
            'status' => 1 // Default to Enabled
        ]);

        return response()->json(['message' => 'Course added successfully', 'course' => $course], 201);
    }

    // Change status instead of deleting (Soft Delete)
    public function changeStatus($id)
    {
        $course = Course::findOrFail($id);
        $course->update(['status' => 0]); // Set status to 0 (Disabled)

        return response()->json(['message' => 'Course disabled successfully']);
    }
}
