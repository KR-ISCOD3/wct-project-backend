<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = DB::table('students')->get();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' =>  'nullable|integer',
            'class_id' => 'nullable|integer',
            'attendance_id' => 'nullable|integer',
            'name' => 'required|string',
            'gender_id'=>'nullable|integer',
            'score_id' => 'nullable|integer',
            'tel' => 'required|string|max:20',
        ]);

        $id = DB::table('students')->insertGetId([
            'teacher_id' =>  $data['teacher_id'],
            'class_id' => $data['class_id'] ?? null,
            'attendance_id' => $data['attendance_id'] ?? null,
            'name' => $data['name'],
            'gender_id'=> $data['gender_id'],
            'score_id' => $data['score_id'] ?? null,
            'tel' => $data['tel'],
            'create_date' => now(),
        ]);

        // Update total_students in classes table
        if (!empty($data['class_id'])) {
            $studentCount = DB::table('students')
                ->where('class_id', $data['class_id'])
                ->count();

            DB::table('classes')
                ->where('id', $data['class_id'])
                ->update(['total_students' => $studentCount]);
        }

        $student = DB::table('students')->where('id', $id)->first();

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student,
            'total_students' => $studentCount ?? null,
        ], 201);
    }


    public function show($id)
    {
        $student = DB::table('students')->where('id', $id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $student = DB::table('students')->where('id', $id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $data = $request->validate([
            'class_id' => 'nullable|integer',
            'attendance_id' => 'nullable|integer',
            'name' => 'required|string',
            'score_id' => 'nullable|integer',
            'tel' => 'required|string|max:20',
            'gender_id' => 'nullable|integer',
        ]);

        DB::table('students')->where('id', $id)->update($data);

        return response()->json(['message' => 'Student updated']);
    }

    public function destroy($id)
    {
        $student = DB::table('students')->where('id', $id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        DB::table('students')->where('id', $id)->delete();

        return response()->json(['message' => 'Student deleted']);
    }

    public function getByClassId($classId)
    {
        $students = DB::table('students')
            ->join('genders', 'students.gender_id', '=', 'genders.id')
            ->select(
                'students.*',
                'genders.gender as gender_name'
            )
            ->where('students.class_id', $classId)
            ->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No students found for this class'], 404);
        }

        return response()->json($students);
    }
}
