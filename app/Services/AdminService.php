<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Models\Bus;
use App\Models\Route;
use Exception;

class AdminService
{
    protected $orderRepository;
    protected $scheduleRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ScheduleRepositoryInterface $scheduleRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * Get all orders for all users.
     */
    public function getAllOrders(int $perPage = 10)
    {
        return $this->orderRepository->getAllOrders($perPage);
    }

    /**
     * Confirm a pending order.
     */
    public function confirmOrder(int $orderId)
    {
        $order = $this->orderRepository->findByIdWithRelations($orderId);

        if (!$order) {
            throw new Exception('Order not found', 404);
        }

        if ($order->status !== 'pending') {
            throw new Exception('Only pending orders can be confirmed', 400);
        }

        // Update order status
        $this->orderRepository->updateStatus($orderId, 'paid');

        // Update payment status if exists
        if ($order->payment) {
            $order->payment->update(['payment_status' => 'success', 'paid_at' => now()]);
        }

        return $this->orderRepository->findByIdWithRelations($orderId);
    }

    /**
     * Get all schedules for admin view.
     */
    public function getAllSchedules(int $perPage = 10)
    {
        return $this->scheduleRepository->getAll($perPage);
    }

    /**
     * Create a new bus schedule.
     */
    public function createSchedule(array $data)
    {
        $schedule = $this->scheduleRepository->create($data);
        return $this->scheduleRepository->findWithRelations($schedule->id);
    }

    /**
     * Update an existing schedule.
     */
    public function updateSchedule(int $id, array $data)
    {
        $updated = $this->scheduleRepository->update($id, $data);

        if (!$updated) {
            throw new Exception('Schedule not found or not updated', 404);
        }

        return $this->scheduleRepository->findWithRelations($id);
    }

    /**
     * Delete a schedule.
     */
    public function deleteSchedule(int $id)
    {
        $deleted = $this->scheduleRepository->delete($id);

        if (!$deleted) {
            throw new Exception('Schedule not found', 404);
        }

        return true;
    }

    /**
     * Get all buses.
     */
    public function getAllBuses()
    {
        return Bus::all();
    }

    /**
     * Get all routes.
     */
    public function getAllRoutes()
    {
        return Route::all();
    }
}
