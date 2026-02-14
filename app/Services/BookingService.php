<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class BookingService
{
    protected $orderRepository;
    protected $ticketRepository;
    protected $paymentRepository;
    protected $scheduleRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        TicketRepositoryInterface $ticketRepository,
        PaymentRepositoryInterface $paymentRepository,
        ScheduleRepositoryInterface $scheduleRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->ticketRepository = $ticketRepository;
        $this->paymentRepository = $paymentRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    public function createBooking(int $userId, array $data): array
    {
        DB::beginTransaction();

        try {
            $schedule = $this->scheduleRepository->findWithRelations($data['schedule_id']);

            if (!$schedule) {
                throw new Exception('Schedule not found');
            }

            $ticketCount = count($data['tickets']);

            if ($schedule->available_seats < $ticketCount) {
                throw new Exception('Not enough available seats');
            }

            $totalPrice = $schedule->price * $ticketCount;

            // Create order
            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'order_code' => $this->generateOrderCode(),
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // Create tickets
            foreach ($data['tickets'] as $ticketData) {
                $this->ticketRepository->create([
                    'order_id' => $order->id,
                    'schedule_id' => $schedule->id,
                    'seat_number' => $ticketData['seat_number'],
                    'passenger_name' => $ticketData['passenger_name'],
                ]);
            }

            // Create payment
            $this->paymentRepository->create([
                'order_id' => $order->id,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
            ]);

            // Update available seats
            $this->scheduleRepository->decrementSeats($schedule->id, $ticketCount);

            DB::commit();

            return [
                'success' => true,
                'order' => $this->orderRepository->findByIdWithRelations($order->id),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUserBookings(int $userId, int $perPage = 10)
    {
        return $this->orderRepository->getUserOrders($userId, $perPage);
    }

    public function getBookingDetail(int $userId, int $orderId)
    {
        return $this->orderRepository->findByUserIdWithRelations($userId, $orderId);
    }

    public function cancelBooking(int $userId, int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->findByUserIdWithRelations($userId, $orderId);

            if (!$order) {
                throw new Exception('Order not found');
            }

            if ($order->status !== 'pending') {
                throw new Exception('Only pending orders can be cancelled');
            }

            // Update order status
            $this->orderRepository->updateStatus($order->id, 'cancelled');

            // Restore available seats
            $ticketCount = $order->tickets->count();
            $scheduleId = $order->tickets->first()->schedule_id;
            $this->scheduleRepository->incrementSeats($scheduleId, $ticketCount);

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function generateOrderCode(): string
    {
        return 'ORD-' . strtoupper(Str::random(10));
    }
}
