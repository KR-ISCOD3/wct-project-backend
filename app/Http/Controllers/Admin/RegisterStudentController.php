<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\RegisterStudent;
use Illuminate\Http\Request;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterStudentController extends Controller
{
    protected $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Display a listing of the registered students.
     */
    public function index()
    {
        $students = DB::table('register_students')
        ->leftJoin('genders', 'register_students.gender_id', '=', 'genders.id')
        ->leftJoin('courses', 'register_students.course_id', '=', 'courses.id')
        ->select(
            'register_students.*',
            'genders.gender as gender_name',
            'courses.name as course_title'
        )
        ->get();
        return response()->json($students);
    }

    /**
     * Store a newly created student registration.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'gender_id' => 'required|exists:genders,id',
            'course_id' => 'nullable|exists:courses,id|required_without:custom_course',
            'custom_course' => 'nullable|string|max:255|required_without:course_id',
            'price' => 'required|numeric|min:0',
            'document_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'startdate' => 'required|date|after_or_equal:today',

            'status' => 'nullable|in:enabled,disabled', // The status can be 'enabled' or 'disabled'
            'print_status' => 'nullable|in:printed,not printed', // The print status can be 'printed' or 'not printed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        if (!$validatedData['course_id'] && !$validatedData['custom_course']) {
            return response()->json(['error' => 'Either course_id or custom_course must be provided'], 400);
        }

        $courseId = $validatedData['course_id'];

        if (! $courseId && $validatedData['custom_course']) {
            $newCourse = Course::create([
                'name'     => $validatedData['custom_course'],
                'category' => ['custom'],        // ← explicitly set it here
                'status'   => 'enabled',
                'create_date' => now(),
            ]);
            $courseId = $newCourse->id;
        }

        $exchangeRate = $this->exchangeRateService->getExchangeRate();
        $coursePrice = $validatedData['price'];
        $documentFee = $validatedData['document_fee'] ?? 0;

        $totalPrice = $coursePrice + $documentFee;
        $totalPriceInRiel = $totalPrice * $exchangeRate;
        $formattedPrice = number_format($totalPrice, 2) . "$ / " . number_format($totalPriceInRiel, 0, '.', '') . "៛";


        $register = RegisterStudent::create([
            'student_name' => $validatedData['student_name'],
            'gender_id' => $validatedData['gender_id'],
            'course_id' => $courseId,
            'custom_course' => $validatedData['custom_course'],
            'price' => $validatedData['price'],
            'document_fee' => $documentFee,
            'payment_method' => $validatedData['payment_method'],
            'total_price' => $formattedPrice,
            'startdate' => $validatedData['startdate'],
            'status' => $validatedData['status'] ?? 'enabled', // Default 'enabled' status
            'print_status' => $validatedData['print_status'] ?? 'not printed',
        ]);

        return response()->json(['message' => 'Student registered successfully', 'data' => $register], 201);
    }

    /**
     * Display the specified student's details.
     */
    public function show(string $id)
    {
        $student = RegisterStudent::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        return response()->json($student);
    }

    /**
     * Update the specified student's registration.
     */
    public function update(Request $request, string $id)
    {
        $student = RegisterStudent::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $student->update($request->all());
        return response()->json(['message' => 'Student updated successfully', 'data' => $student]);
    }

    /**
     * Remove the specified student registration.
     */
    public function destroy(string $id)
    {
        $student = RegisterStudent::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }
}
