<?php

namespace App\Console\Commands;

use App\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncDropboxGallery extends Command
{
    protected $signature   = 'gallery:sync-dropbox';
    protected $description = 'Sync Dropbox file-request photos into the database and cache thumbnails locally';

    public function handle(): int
    {
        // Warm Dropbox token cache
        Storage::disk('dropbox')->exists('__ping__');
        $token = cache()->get('dropbox_access_token');

        if (!$token) {
            $this->error('No Dropbox access token cached. Load the app in a browser first to authenticate.');
            return self::FAILURE;
        }

        $client    = new \Spatie\Dropbox\Client($token);
        $folder    = env('DROPBOX_APP_FOLDER', '');
        $appFolder = strtolower(rtrim($folder, '/'));
        $allowed   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // ── List all files in the Dropbox folder ──────────────────────────────
        $this->info('Fetching file list from Dropbox…');
        try {
            $result  = $client->listFolder($folder);
            $entries = $result['entries'] ?? [];
            while ($result['has_more'] ?? false) {
                $result  = $client->listFolderContinue($result['cursor']);
                $entries = array_merge($entries, $result['entries'] ?? []);
            }
        } catch (\Throwable $e) {
            $this->error('Dropbox API error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $files = collect($entries)
            ->filter(fn($e) => ($e['.tag'] ?? '') === 'file'
                && in_array(strtolower(pathinfo($e['name'], PATHINFO_EXTENSION)), $allowed))
            ->map(fn($e) => ['path' => $e['path_lower'], 'name' => $e['name']])
            ->values()
            ->all();

        $this->info(count($files) . ' image(s) found in Dropbox.');

        // ── Build set of already-synced paths ─────────────────────────────────
        $knownFullPaths = Photo::whereNotNull('dropbox_path')
            ->pluck('dropbox_path')
            ->map(fn($p) => $appFolder . strtolower($p))
            ->flip()
            ->all();

        // ── Sync new files ─────────────────────────────────────────────────────
        $added  = 0;
        $errors = 0;
        $thumbDir = storage_path('app/public/gallery');
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        foreach ($files as $file) {
            if (isset($knownFullPaths[$file['path']])) {
                $this->line('  skip  ' . $file['name']);
                continue;
            }

            try {
                // Relative path stored in DB (strip app folder prefix)
                $relativePath = substr($file['path'], strlen($appFolder));

                // Download and cache thumbnail
                $encoded   = rtrim(strtr(base64_encode($file['path']), '+/', '-_'), '=');
                $localFile = 'gallery/dbx_' . $encoded . '.jpg';

                if (!Storage::disk('public')->exists($localFile)) {
                    $bytes = $client->getThumbnail($file['path'], 'jpeg', 'w640h480');
                    if ($bytes) {
                        Storage::disk('public')->put($localFile, $bytes);
                    }
                }

                Photo::create([
                    'guest_id'     => null,
                    'dropbox_path' => $relativePath,
                    'thumb_path'   => $localFile,
                    'approved'     => true,
                ]);

                // Mark as known so a duplicate entry in this run is skipped
                $knownFullPaths[$file['path']] = true;
                $added++;
                $this->info('  added ' . $file['name']);
            } catch (\Throwable $e) {
                $errors++;
                $this->warn('  error ' . $file['name'] . ' — ' . $e->getMessage());
            }
        }

        cache()->forget('dropbox_gallery_files');

        $this->info("Done: {$added} added, {$errors} errors.");
        return self::SUCCESS;
    }
}
