<?php

namespace App\Repositories\Contracts;

use App\Models\Ticket;
use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    public function create(array $data): Ticket;
    public function createMultiple(array $tickets): Collection;
    public function getByOrderId(int $orderId): Collection;
}
