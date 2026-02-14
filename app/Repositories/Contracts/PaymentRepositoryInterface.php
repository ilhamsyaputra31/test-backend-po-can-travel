<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;
    public function findByOrderId(int $orderId): ?Payment;
    public function updateStatus(int $id, string $status): bool;
}
