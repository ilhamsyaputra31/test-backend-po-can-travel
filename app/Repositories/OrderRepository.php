<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Order
    {
        return $this->model->find($id);
    }

    public function findByIdWithRelations(int $id): ?Order
    {
        return $this->model->with([
            'tickets.schedule.bus',
            'tickets.schedule.route',
            'payment'
        ])->find($id);
    }

    public function findByUserIdWithRelations(int $userId, int $orderId): ?Order
    {
        return $this->model->with([
            'tickets.schedule.bus',
            'tickets.schedule.route',
            'payment'
        ])
        ->where('user_id', $userId)
        ->where('id', $orderId)
        ->first();
    }

    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with([
            'tickets.schedule.bus',
            'tickets.schedule.route',
            'payment'
        ])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function getAllOrders(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with([
            'user',
            'tickets.schedule.bus',
            'tickets.schedule.route',
            'payment'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }
}
