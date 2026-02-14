<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminScheduleRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Exception;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Get all orders (for all users).
     */
    public function getAllOrders(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $orders = $this->adminService->getAllOrders($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ]);
    }

    /**
     * Get all buses.
     */
    public function getAllBuses()
    {
        $buses = $this->adminService->getAllBuses();

        return response()->json([
            'success' => true,
            'message' => 'Buses retrieved successfully',
            'data' => $buses
        ]);
    }

    /**
     * Get all routes.
     */
    public function getAllRoutes()
    {
        $routes = $this->adminService->getAllRoutes();

        return response()->json([
            'success' => true,
            'message' => 'Routes retrieved successfully',
            'data' => $routes
        ]);
    }

    /**
     * Confirm an order (simulate payment success).
     */
    public function confirmOrder($id)
    {
        try {
            $order = $this->adminService->confirmOrder($id);

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully',
                'data' => $order
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * Get all schedules (admin view).
     */
    public function getAllSchedules(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $schedules = $this->adminService->getAllSchedules($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Schedules retrieved successfully',
            'data' => $schedules
        ]);
    }

    /**
     * Create a new schedule.
     */
    public function storeSchedule(AdminScheduleRequest $request)
    {
        $schedule = $this->adminService->createSchedule($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $schedule
        ], 201);
    }

    /**
     * Update an existing schedule.
     */
    public function updateSchedule(AdminScheduleRequest $request, $id)
    {
        try {
            $schedule = $this->adminService->updateSchedule($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'data' => $schedule
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * Delete a schedule.
     */
    public function deleteSchedule($id)
    {
        try {
            $this->adminService->deleteSchedule($id);

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }
}
