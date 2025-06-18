<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index()
    {
        $students = DB::table('students')->get();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'teacher_id' => 'nullable|integer',
                'class_id' => 'nullable|integer',
                'attendance_id' => 'nullable|integer',
                'name' => 'required|string',
                'gender_id' => 'nullable|integer',
                'score_id' => 'nullable|integer',
                'tel' => 'required|string|max:20',
            ]);

            Log::info('Student Data:', $data);

            DB::beginTransaction();

            $id = DB::table('students')->insertGetId([
                'teacher_id' => $data['teacher_id'] ?? null,
                'class_id' => $data['class_id'] ?? null,
                'attendance_id' => $data['attendance_id'] ?? null,
                'name' => $data['name'],
                'gender_id' => $data['gender_id'] ?? null,
                'score_id' => $data['score_id'] ?? null,
                'tel' => $data['tel'],
                'create_date' => now(),
            ]);

            $totalStudents = null;
            if (!empty($data['class_id'])) {
                // Use a single raw query instead of two queries (faster)
                $totalStudents = DB::table('students')
                    ->where('class_id', $data['class_id'])
                    ->count();

                DB::table('classes')
                    ->where('id', $data['class_id'])
                    ->update(['total_students' => $totalStudents]);
            }

            DB::commit();

            // Avoid a separate query, use the already inserted data
            $student = array_merge($data, ['id' => $id]);

            return response()->json([
                'message' => 'Student created successfully',
                'student' => $student,
                'total_students' => $totalStudents,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to store student', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
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
            ->orderByDesc('students.id')
            ->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No students found for this class'], 404);
        }

        return response()->json($students);
    }



}
