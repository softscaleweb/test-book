<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Currency;

class UpdateFxRates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fx:update';

    /**
     * The console command description.
     */
    protected $description = 'Update FX rates from storage/app/mocks/fx.json into currencies table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('app/mocks/fx.json');

        if (!file_exists($path)) {
            $this->error("FX JSON file not found at: $path");
            return Command::FAILURE;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!is_array($data) || !isset($data['base'], $data['rates'])) {
            $this->error("Invalid FX JSON structure.");
            return Command::FAILURE;
        }

        $base = strtoupper($data['base']);
        $rates = $data['rates'];
        $updatedAt = $data['updated_at'] ?? now();

        // Base currency always 1.0
        Currency::updateOrCreate(
            ['code' => $base],
            ['value' => 1.0, 'updated_at' => $updatedAt]
        );

        // Other currencies
        foreach ($rates as $code => $val) {
            Currency::updateOrCreate(
                ['code' => strtoupper($code)],
                ['value' => (float) $val, 'updated_at' => $updatedAt]
            );
        }

        $this->info("FX rates updated successfully.");
        return Command::SUCCESS;
    }
}
