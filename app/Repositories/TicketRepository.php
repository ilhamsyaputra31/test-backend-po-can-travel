<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Support\Collection;

class TicketRepository implements TicketRepositoryInterface
{
    protected $model;

    public function __construct(Ticket $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Ticket
    {
        return $this->model->create($data);
    }

    public function createMultiple(array $tickets): Collection
    {
        $createdTickets = collect();

        foreach ($tickets as $ticketData) {
            $createdTickets->push($this->create($ticketData));
        }

        return $createdTickets;
    }

    public function getByOrderId(int $orderId): Collection
    {
        return $this->model->where('order_id', $orderId)->get();
    }
}
