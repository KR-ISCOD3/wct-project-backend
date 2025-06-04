<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    // Laravel API route in your controller
    public function getByTeacher($id)
    {
        $classes = DB::table('classes')
            ->leftJoin('users as teachers', 'classes.teacher_id', '=', 'teachers.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('buildings', 'classes.building_id', '=', 'buildings.id')
            ->where('classes.teacher_id', $id)
            ->select(
                'classes.*',
                'teachers.name as teacher_name',
                'courses.name as course_name',
                'buildings.name as building_name'
            )
            ->orderBy('classes.id')
            ->get();

        return response()->json($classes);
    }

    public function index()
    {
        $classes = DB::table('classes')
            ->leftJoin('users as teachers', 'classes.teacher_id', '=', 'teachers.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('buildings', 'classes.building_id', '=', 'buildings.id')
            ->select(
                'classes.id',
                'classes.time',
                'classes.study_term',
                'classes.chapter',
                'classes.status',
                'teachers.name as teacher_name',
                'courses.name as course_name',
                'buildings.name as building_name'
            )
            ->get();

        return response()->json($classes);
    }

    // Create a new class
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'integer',
            'course_id' => 'integer',
            'building_id' => 'integer',
            'time' => 'string|max:50',
            'study_term'=>'string|max:50',
            'chapter' => 'string|max:255',
            'status' => 'string|max:50',
            // 'status_class' => 'string|max:50',
        ]);

        $validated['status_class'] = 'Progress';
        $class = ClassModel::create($validated);

        return response()->json($class, 201);
    }

    // Get one class by ID
    public function show($id)
    {
        $class = ClassModel::findOrFail($id);
        return response()->json($class);
    }

    // Update a class by ID
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $validated = $request->validate([
            'teacher_id' => 'integer',
            'course_id' => 'integer',
            'building_id' => 'integer',
            'time' => 'string|max:50',
            'study_term' => 'string|max:50',
            'chapter' => 'string|max:255',
            'status' => 'string|max:50',
            'status_class' => 'string|max:50',
        ]);

        $class->update($validated);

        return response()->json($class);
    }


    // Delete a class by ID
    public function destroy($id)
    {
        ClassModel::destroy($id);

        return response()->json(['message' => 'Class deleted successfully']);
    }
}
