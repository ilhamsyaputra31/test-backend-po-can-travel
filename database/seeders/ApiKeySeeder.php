<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApiKeySeeder extends Seeder
{
    public function run(): void
    {
        ApiKey::create([
            'app_name' => 'Mobile App',
            'api_key' => Str::random(32),
            'status' => true,
        ]);
    }
}
