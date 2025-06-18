<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|integer',
            'attendances.*.attendance_status' => 'required|string|in:P,A,PM',
            'attendances.*.reason' => 'nullable|string',
        ]);

        $records = [];
        foreach ($validated['attendances'] as $attendance) {
            $records[] = [
                'class_id' => $validated['class_id'],
                'teacher_id' => $validated['teacher_id'],
                'student_id' => $attendance['student_id'],
                'attendance_status' => $attendance['attendance_status'],
                'reason' => $attendance['reason'] ?? null,
                'date' => $validated['date'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('attendances')->insert($records);

        return response()->json(['message' => 'Attendance recorded successfully']);
    }

    public function getAttendance($class_id)
    {
        // Fetch attendance grouped by student and status
        $records = DB::table('attendances')
            ->select('student_id', 'attendance_status', DB::raw('COUNT(*) as count'))
            ->where('class_id', $class_id)
            ->groupBy('student_id', 'attendance_status')
            ->get();

        // Organize the data by student_id
        $attendanceSummary = [];

        foreach ($records as $record) {
            $studentId = $record->student_id;
            $status = $record->attendance_status;
            $count = $record->count;

            if (!isset($attendanceSummary[$studentId])) {
                $attendanceSummary[$studentId] = [
                    'present' => 0,
                    'permission' => 0,
                    'absent' => 0,
                    'total' => 0,
                ];
            }

            if ($status === 'P') {
                $attendanceSummary[$studentId]['present'] = $count;
            } elseif ($status === 'PM') {
                $attendanceSummary[$studentId]['permission'] = $count;
            } elseif ($status === 'A') {
                $attendanceSummary[$studentId]['absent'] = $count;
            }

            // Total score = Present + Permission
            $attendanceSummary[$studentId]['total'] =
                $attendanceSummary[$studentId]['present'] + $attendanceSummary[$studentId]['permission'];
        }

        return $attendanceSummary;
    }

    public function getStudentAttendance($class_id, $student_id)
    {
        $records = DB::table('attendances')
            ->select('attendance_status', DB::raw('COUNT(*) as count'))
            ->where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->groupBy('attendance_status')
            ->get();

        // Initialize summary
        $summary = [
            'present' => 0,
            'permission' => 0,
            'absent' => 0,
            'total' => 0,
        ];

        // Process records
        foreach ($records as $record) {
            if ($record->attendance_status === 'P') {
                $summary['present'] = $record->count;
            } elseif ($record->attendance_status === 'PM') {
                $summary['permission'] = $record->count;
            } elseif ($record->attendance_status === 'A') {
                $summary['absent'] = $record->count;
            }
        }

        $summary['total'] = $summary['present'] + $summary['permission'];

        return response()->json([
            'student_id' => $student_id,
            'class_id' => $class_id,
            'attendance' => $summary,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
