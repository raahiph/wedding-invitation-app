<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Guest;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class GalleryController extends Controller
{
    // ── Guest gallery ─────────────────────────────────────────────────────────

    public function show()
    {
        $photos         = Photo::where('approved', true)->latest()->get();
        $fileRequestUrl = \App\Models\Setting::find('dropbox_file_request_url')?->value;
        $albums         = Album::has('photos')->withCount('photos')->with(['photos' => fn($q) => $q->oldest()->limit(1)])->latest()->get();
        return view('gallery', compact('photos', 'fileRequestUrl', 'albums'));
    }

    public function uploadPage()
    {
        $fileRequestUrl = \App\Models\Setting::find('dropbox_file_request_url')?->value;
        return view('upload', compact('fileRequestUrl'));
    }

    public function photos()
    {
        $photos = Photo::where('approved', true)
            ->latest()
            ->take(100)
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'thumb_url'  => $p->thumbUrl(),
                'created_at' => $p->created_at->diffForHumans(),
            ]);

        return response()->json($photos);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:30720',
        ]);

        $file = $request->file('photo');
        $uuid = (string) Str::uuid();

        $guestId = session('guest_id');
        $prefix  = '';
        if ($guestId) {
            $guest = Guest::find($guestId);
            $displayName = $guest->nickname ?: $guest->name;
            if ($guest && $displayName) {
                $slug   = preg_replace('/[^a-z0-9]+/', '-', strtolower($displayName));
                $prefix = trim($slug, '-') . '_';
            }
        }

        $baseName = $prefix . $uuid;

        $thumbDir = storage_path('app/public/gallery');
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        $thumbFile = $thumbDir . '/' . $baseName . '.jpg';
        Image::read($file)
            ->scaleDown(width: 1200)
            ->toJpeg(quality: 85)
            ->save($thumbFile);

        $thumbPath   = 'gallery/' . $baseName . '.jpg';
        $dropboxPath = '/' . $baseName . '_original.' . $file->getClientOriginalExtension();
        Storage::disk('dropbox')->put($dropboxPath, fopen($file->getRealPath(), 'r'));

        $photo = Photo::create([
            'guest_id'     => session('guest_id'),
            'dropbox_path' => $dropboxPath,
            'thumb_path'   => $thumbPath,
            'approved'     => true,
        ]);

        return response()->json([
            'ok'    => true,
            'photo' => [
                'id'        => $photo->id,
                'thumb_url' => $photo->thumbUrl(),
            ],
        ]);
    }

    // ── Admin ─────────────────────────────────────────────────────────────────

    public function adminIndex()
    {
        $photos = Photo::latest()->get();
        return view('admin.gallery', compact('photos'));
    }

    public function adminSync()
    {
        try {
            Artisan::call('gallery:sync-dropbox');
            $output = Artisan::output();

            preg_match('/(\d+) added, (\d+) errors/', $output, $m);
            return response()->json([
                'ok'     => true,
                'added'  => (int) ($m[1] ?? 0),
                'errors' => (int) ($m[2] ?? 0),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Photo $photo)
    {
        // Delete local thumbnail
        Storage::disk('public')->delete($photo->thumb_path);

        if ($photo->dropbox_path && $this->dropboxDeleteEnabled()) {
            try {
                $this->dropboxClient()->delete(
                    rtrim(env('DROPBOX_APP_FOLDER', ''), '/') . $photo->dropbox_path
                );
            } catch (\Throwable) {}
        }

        $photo->delete();
        return response()->json(['ok' => true]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function dropboxClient(): \Spatie\Dropbox\Client
    {
        Storage::disk('dropbox')->exists('__ping__');
        return new \Spatie\Dropbox\Client(cache()->get('dropbox_access_token'));
    }

    private function dropboxDeleteEnabled(): bool
    {
        return \App\Models\Setting::where('key', 'dropbox_delete_on_remove')->value('value') === '1';
    }
}
