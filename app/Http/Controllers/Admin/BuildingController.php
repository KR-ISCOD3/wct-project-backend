<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    private function calculateTotalRooms(string $roomNumber): int
    {
        if (strpos($roomNumber, '-') !== false) {
            [$start, $end] = explode('-', $roomNumber);
            return ((int)$end - (int)$start) + 1;
        }
        return 1;
    }
    public function index()
    {
        $buildings = Building::where('status', '!=', 'deleted')->get();
        return response()->json($buildings);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'buildingName' => 'required|string|max:255',
            'roomNumber' => 'required|string|max:50',
        ]);

        try {
            $totalRooms = $this->calculateTotalRooms($validated['roomNumber']);

            $building = Building::create([
                'name' => $validated['buildingName'],
                'room_number' => $validated['roomNumber'],
                'total_rooms' => $totalRooms,
                'status' => 'available',
            ]);

            return response()->json($building, 201);

        } catch (\Exception $e) {
            // Return the exception message directly (for debugging ONLY)
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function update(Request $request, $id)
    {
        $building = Building::findOrFail($id);

        $validated = $request->validate([
            'buildingName' => 'sometimes|string|max:255',
            'roomNumber' => 'sometimes|string|max:50',
        ]);

        if (isset($validated['roomNumber'])) {
            $validated['total_rooms'] = $this->calculateTotalRooms($validated['roomNumber']);
        }

        if (isset($validated['buildingName'])) {
            $building->name = $validated['buildingName'];
        }

        if (isset($validated['roomNumber'])) {
            $building->room_number = $validated['roomNumber'];
            $building->total_rooms = $validated['total_rooms'];
        }

        $building->save();

        return response()->json($building);
    }

    public function destroy($id)
    {
        $building = Building::findOrFail($id);
        $building->status = 'deleted';
        $building->save();

        return response()->json(['message' => 'Building marked as deleted.']);
    }
}
