<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['origin', 'destination', 'date']);
        $schedules = $this->scheduleService->searchSchedules($filters);

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function show($id)
    {
        $schedule = $this->scheduleService->getScheduleById($id);

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
