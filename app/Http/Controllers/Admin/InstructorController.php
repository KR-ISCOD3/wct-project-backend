<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    public function index()
    {
        // Retrieve only the users with the 'teacher' role
        $users = DB::table('users')
            ->select('id', 'name','email','image', 'create_date', 'update_date', 'work_status', 'shift','status','deleted_status', 'position','phone_number','status')
            ->where('role', 'teacher') // Filter for 'teacher' role only
            ->get();

        // Return the response with the filtered users
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('gender') // eager load gender
            ->where('id', $id)
            ->where('role', 'teacher')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Instructor not found'], 404);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        // Find the user with role 'teacher' and the given id
        $user = User::where('id', $id)
                    ->where('role', 'teacher')
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Instructor not found'], 404);
        }

        // Update the status to 'disabled' instead of deleting
        $user->deleted_status = 'unactive';
        $user->save();

        return response()->json(['message' => 'Instructor has been disabled successfully']);
    }

    
}
