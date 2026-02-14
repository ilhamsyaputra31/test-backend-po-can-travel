<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function findById(int $id): ?Order;
    public function findByIdWithRelations(int $id): ?Order;
    public function findByUserIdWithRelations(int $userId, int $orderId): ?Order;
    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function getAllOrders(int $perPage = 10): LengthAwarePaginator;
    public function update(int $id, array $data): bool;
    public function updateStatus(int $id, string $status): bool;
}
