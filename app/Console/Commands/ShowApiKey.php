<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ShowApiKey extends Command
{
    protected $signature = 'apikey:show';
    protected $description = 'Display the API key for testing';

    public function handle()
    {
        $apiKey = ApiKey::where('status', true)->first();

        if (!$apiKey) {
            $this->error('No active API key found!');
            $this->info('Run: php artisan db:seed --class=ApiKeySeeder');
            return 1;
        }

        $this->info('========================================');
        $this->info('PO CAN Travel - API Key');
        $this->info('========================================');
        $this->line('');
        $this->line('App Name: ' . $apiKey->app_name);
        $this->line('API Key: ' . $apiKey->api_key);
        $this->line('Status: ' . ($apiKey->status ? 'Active' : 'Inactive'));
        $this->line('');
        $this->info('Use this in your requests:');
        $this->line('X-API-Key: ' . $apiKey->api_key);
        $this->line('');
        $this->info('========================================');

        return 0;
    }
}
