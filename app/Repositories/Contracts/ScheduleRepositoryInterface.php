<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Schedule;

interface ScheduleRepositoryInterface
{
    public function search(array $filters, int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?Schedule;
    public function findWithRelations(int $id): ?Schedule;
    public function getAll(int $perPage = 10): LengthAwarePaginator;
    public function create(array $data): Schedule;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function decrementSeats(int $scheduleId, int $count): bool;
    public function incrementSeats(int $scheduleId, int $count): bool;
}
