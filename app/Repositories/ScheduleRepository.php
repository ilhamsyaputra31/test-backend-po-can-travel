<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    protected $model;

    public function __construct(Schedule $model)
    {
        $this->model = $model;
    }

    public function search(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['bus', 'route']);

        if (isset($filters['origin'])) {
            $query->whereHas('route', function ($q) use ($filters) {
                $q->where('origin', 'like', '%' . $filters['origin'] . '%');
            });
        }

        if (isset($filters['destination'])) {
            $query->whereHas('route', function ($q) use ($filters) {
                $q->where('destination', 'like', '%' . $filters['destination'] . '%');
            });
        }

        if (isset($filters['date'])) {
            $query->whereDate('departure_time', $filters['date']);
        }

        return $query->where('available_seats', '>', 0)
            ->orderBy('departure_time')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Schedule
    {
        return $this->model->find($id);
    }

    public function findWithRelations(int $id): ?Schedule
    {
        return $this->model->with(['bus', 'route'])->find($id);
    }

    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['bus', 'route'])
            ->orderBy('departure_time', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Schedule
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    public function decrementSeats(int $scheduleId, int $count): bool
    {
        return $this->model->where('id', $scheduleId)
            ->decrement('available_seats', $count) > 0;
    }

    public function incrementSeats(int $scheduleId, int $count): bool
    {
        return $this->model->where('id', $scheduleId)
            ->increment('available_seats', $count) > 0;
    }
}
