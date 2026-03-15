<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the settings table from config/wedding.php defaults.
 * Only inserts rows that don't already exist — existing DB values are preserved.
 * Use --force to overwrite everything with the config defaults.
 *
 * Usage:
 *   php artisan db:seed --class=SettingsSeeder           # safe — skips existing keys
 *   php artisan db:seed --class=SettingsSeeder -- --force # overwrites all keys
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $force    = in_array('--force', $_SERVER['argv'] ?? []);
        $defaults = config('wedding');

        foreach ($defaults as $key => $value) {
            if ($force) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            } else {
                Setting::firstOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        $this->command->info('Settings seeded from config/wedding.php (' . count($defaults) . ' keys).');
    }
}
