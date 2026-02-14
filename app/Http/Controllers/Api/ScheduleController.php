<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['bus', 'route']);

        if ($request->has('origin')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('origin', 'like', '%' . $request->origin . '%');
            });
        }

        if ($request->has('destination')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('destination', 'like', '%' . $request->destination . '%');
            });
        }

        if ($request->has('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        $schedules = $query->where('available_seats', '>', 0)
            ->orderBy('departure_time')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function show($id)
    {
        $schedule = Schedule::with(['bus', 'route'])->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }
}
