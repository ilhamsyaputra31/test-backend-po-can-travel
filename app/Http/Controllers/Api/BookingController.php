<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Exception;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function store(BookingRequest $request)
    {
        try {
            $result = $this->bookingService->createBooking(
                auth()->id(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => $result['order'],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function index(Request $request)
    {
        $orders = $this->bookingService->getUserBookings(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = $this->bookingService->getBookingDetail(auth()->id(), $id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function cancel($id)
    {
        try {
            $this->bookingService->cancelBooking(auth()->id(), $id);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
