<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Storage::extend('dropbox', function ($app, $config) {
            // Exchange the long-lived refresh token for a short-lived access token.
            // Cache it for 3.5 hours (tokens last 4 hours).
            $accessToken = cache()->remember('dropbox_access_token', 210 * 60, function () use ($config) {
                $response = (new \GuzzleHttp\Client)->post('https://api.dropboxapi.com/oauth2/token', [
                    'form_params' => [
                        'grant_type'    => 'refresh_token',
                        'refresh_token' => $config['token'],
                        'client_id'     => $config['app_key'],
                        'client_secret' => $config['app_secret'],
                    ],
                ]);
                $data = json_decode($response->getBody(), true);
                return $data['access_token'];
            });

            $client  = new DropboxClient($accessToken);
            $adapter = new DropboxAdapter($client, $config['root'] ?? '');
            return new FilesystemAdapter(
                new Filesystem($adapter, ['case_sensitive' => false]),
                $adapter,
                $config
            );
        });


        View::composer('*', function ($view) {
            $defaults = config('wedding');
            try {
                $saved = Setting::all()->pluck('value', 'key')->toArray();
            } catch (\Exception) {
                $saved = [];
            }
            $merged = array_merge($defaults, $saved);

            // Decode palette JSON into an array for views
            $merged['palette_items'] = json_decode($merged['palette'] ?? '[]', true) ?: [];

            $view->with('wedding', $merged);
        });
    }
}
