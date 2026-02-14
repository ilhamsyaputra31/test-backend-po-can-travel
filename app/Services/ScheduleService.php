<?php

namespace App\Services;

use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Schedule;

class ScheduleService
{
    protected $scheduleRepository;

    public function __construct(ScheduleRepositoryInterface $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    public function searchSchedules(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->scheduleRepository->search($filters, $perPage);
    }

    public function getScheduleById(int $id): ?Schedule
    {
        return $this->scheduleRepository->findWithRelations($id);
    }

    public function checkAvailability(int $scheduleId, int $requiredSeats): bool
    {
        $schedule = $this->scheduleRepository->findById($scheduleId);

        if (!$schedule) {
            return false;
        }

        return $schedule->available_seats >= $requiredSeats;
    }
}
