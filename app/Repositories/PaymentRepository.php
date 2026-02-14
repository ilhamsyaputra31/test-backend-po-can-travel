<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    protected $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function findByOrderId(int $orderId): ?Payment
    {
        return $this->model->where('order_id', $orderId)->first();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->model->where('id', $id)->update(['payment_status' => $status]);
    }
}
