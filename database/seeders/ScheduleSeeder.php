<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [
            [
                'bus_id' => 1,
                'route_id' => 1,
                'departure_time' => Carbon::now()->addDays(1)->setTime(8, 0),
                'arrival_time' => Carbon::now()->addDays(1)->setTime(11, 0),
                'price' => 75000,
                'available_seats' => 40,
            ],
            [
                'bus_id' => 2,
                'route_id' => 2,
                'departure_time' => Carbon::now()->addDays(1)->setTime(9, 0),
                'arrival_time' => Carbon::now()->addDays(1)->setTime(21, 0),
                'price' => 250000,
                'available_seats' => 30,
            ],
            [
                'bus_id' => 3,
                'route_id' => 3,
                'departure_time' => Carbon::now()->addDays(2)->setTime(7, 0),
                'arrival_time' => Carbon::now()->addDays(2)->setTime(15, 0),
                'price' => 180000,
                'available_seats' => 20,
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }
    }
}
