<?php

namespace Database\Seeders;

use App\Models\Bus;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $buses = [
            ['bus_code' => 'BUS001', 'bus_name' => 'Express Jaya', 'seat_capacity' => 40, 'class' => 'economy'],
            ['bus_code' => 'BUS002', 'bus_name' => 'Luxury Trans', 'seat_capacity' => 30, 'class' => 'business'],
            ['bus_code' => 'BUS003', 'bus_name' => 'Executive Plus', 'seat_capacity' => 20, 'class' => 'executive'],
            ['bus_code' => 'BUS004', 'bus_name' => 'Economy Fast', 'seat_capacity' => 45, 'class' => 'economy'],
        ];

        foreach ($buses as $bus) {
            Bus::create($bus);
        }
    }
}
