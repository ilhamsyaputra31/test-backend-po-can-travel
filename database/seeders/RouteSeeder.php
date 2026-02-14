<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            ['origin' => 'Jakarta', 'destination' => 'Bandung', 'distance_km' => 150],
            ['origin' => 'Jakarta', 'destination' => 'Surabaya', 'distance_km' => 800],
            ['origin' => 'Bandung', 'destination' => 'Yogyakarta', 'distance_km' => 450],
            ['origin' => 'Surabaya', 'destination' => 'Bali', 'distance_km' => 350],
        ];

        foreach ($routes as $route) {
            Route::create($route);
        }
    }
}
