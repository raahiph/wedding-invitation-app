<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync Dropbox file-request photos into the DB every hour.
// Ensure the system cron runs: * * * * * php /path/to/artisan schedule:run
Schedule::command('gallery:sync-dropbox')->everyTenMinutes();
