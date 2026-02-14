<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(BookingRequest $request)
    {
        try {
            DB::beginTransaction();

            $schedule = Schedule::with('bus')->findOrFail($request->schedule_id);
            $ticketCount = count($request->tickets);

            if ($schedule->available_seats < $ticketCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough available seats',
                ], 400);
            }

            $totalPrice = $schedule->price * $ticketCount;

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_code' => 'ORD-' . strtoupper(Str::random(10)),
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($request->tickets as $ticketData) {
                Ticket::create([
                    'order_id' => $order->id,
                    'schedule_id' => $schedule->id,
                    'seat_number' => $ticketData['seat_number'],
                    'passenger_name' => $ticketData['passenger_name'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            $schedule->decrement('available_seats', $ticketCount);

            DB::commit();

            $order->load(['tickets.schedule.bus', 'tickets.schedule.route', 'payment']);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $orders = Order::with(['tickets.schedule.bus', 'tickets.schedule.route', 'payment'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['tickets.schedule.bus', 'tickets.schedule.route', 'payment'])
            ->where('user_id', auth()->id())
            ->find($id);

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
        $order = Order::where('user_id', auth()->id())->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'cancelled']);
            
            $ticketCount = $order->tickets->count();
            $schedule = $order->tickets->first()->schedule;
            $schedule->increment('available_seats', $ticketCount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Cancellation failed',
            ], 500);
        }
    }
}
