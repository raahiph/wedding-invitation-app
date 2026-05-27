<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\AlbumPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AlbumController extends Controller
{
    // ── Public: view album by token (no auth required) ────────────────────────

    public function show(string $token)
    {
        $album = Album::where('token', $token)->firstOrFail();

        if ($album->gated && !session('guest_id')) {
            return redirect()->guest(route('gate.show'));
        }

        $photos = $album->photos()->orderBy('sort_order')->orderBy('id')->get();

        // Manual group_key wins; fall back to photobooth filename pattern.
        $groups = $photos->groupBy(function ($photo) {
            if ($photo->group_key) {
                return 'manual_' . $photo->group_key;
            }
            $name = basename($photo->dropbox_path ?? $photo->thumb_path ?? '');
            if (preg_match('/(\d{8}_\d{6}_\d+)_/', $name, $m)) {
                return 'auto_' . $m[1];
            }
            return 'solo_' . $photo->id;
        })->values();

        return view('album', compact('album', 'groups'));
    }

    // ── Admin: list all albums ────────────────────────────────────────────────

    public function index()
    {
        $albums = Album::withCount('photos')->latest()->get();
        return view('admin.albums', compact('albums'));
    }

    // ── Admin: create album ───────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:120']);

        $album = Album::create([
            'name'  => trim($request->name),
            'token' => Str::random(12),
        ]);

        return response()->json([
            'ok'       => true,
            'redirect' => route('admin.albums.manage', $album),
        ]);
    }

    // ── Admin: manage photos inside an album ──────────────────────────────────

    public function manage(Album $album)
    {
        $photos = $album->photos()->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.album-manage', compact('album', 'photos'));
    }

    // ── Admin: rename album ───────────────────────────────────────────────────

    public function rename(Request $request, Album $album)
    {
        $request->validate(['name' => 'required|string|max:120']);
        $album->update(['name' => trim($request->name)]);
        return response()->json(['ok' => true, 'name' => $album->name]);
    }

    // ── Admin: delete album (cascades to photos via FK) ───────────────────────

    public function destroy(Album $album)
    {
        $dbxDelete = $this->dropboxDeleteEnabled();
        foreach ($album->photos as $photo) {
            Storage::disk('public')->delete($photo->thumb_path);
            if ($dbxDelete && $photo->dropbox_path) {
                $this->deleteFromDropbox($photo->dropbox_path);
            }
        }
        $album->delete();
        return response()->json(['ok' => true]);
    }

    // ── Admin: upload photos to an album ─────────────────────────────────────

    public function uploadPhotos(Request $request, Album $album)
    {
        $request->validate([
            'photos'   => 'required|array|min:1|max:50',
            'photos.*' => 'required|file|mimes:jpg,jpeg,png,webp,gif,mp4|max:204800',
        ]);

        $thumbDir = storage_path('app/public/gallery');
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        $added = 0;
        foreach ($request->file('photos') as $file) {
            $uuid = (string) Str::uuid();
            $mime = $file->getMimeType();

            if ($mime === 'video/mp4') {
                $name = 'alb_' . $uuid . '.mp4';
                $file->move($thumbDir, $name);
                $type = 'video';
            } elseif ($mime === 'image/gif') {
                $name = 'alb_' . $uuid . '.gif';
                copy($file->getRealPath(), $thumbDir . '/' . $name);
                $type = 'gif';
            } else {
                $name = 'alb_' . $uuid . '.jpg';
                Image::read($file)->scaleDown(width: 2400)->toJpeg(quality: 88)->save($thumbDir . '/' . $name);
                $type = 'image';
            }

            AlbumPhoto::create([
                'album_id'   => $album->id,
                'thumb_path' => 'gallery/' . $name,
                'type'       => $type,
            ]);
            $added++;
        }

        return response()->json(['ok' => true, 'added' => $added]);
    }

    // ── Admin: delete a photo ─────────────────────────────────────────────────

    public function destroyPhoto(Album $album, AlbumPhoto $photo)
    {
        abort_if($photo->album_id !== $album->id, 404);
        if ($photo->thumb_path) {
            Storage::disk('public')->delete($photo->thumb_path);
        }
        if ($photo->dropbox_path && $this->dropboxDeleteEnabled()) {
            $this->deleteFromDropbox($photo->dropbox_path);
        }
        $photo->delete();
        return response()->json(['ok' => true]);
    }

    // ── Admin: save Dropbox folder for an album ───────────────────────────────

    public function setDropboxFolder(Request $request, Album $album)
    {
        $folder = trim($request->input('dropbox_folder', ''));
        // Normalise: ensure leading slash, no trailing slash
        if ($folder && !str_starts_with($folder, '/')) {
            $folder = '/' . $folder;
        }
        $album->update(['dropbox_folder' => $folder ?: null]);
        return response()->json(['ok' => true, 'dropbox_folder' => $album->dropbox_folder]);
    }

    // ── Admin: sync album from its Dropbox folder ─────────────────────────────

    public function syncAlbum(Album $album)
    {
        // Warm token
        Storage::disk('dropbox')->exists('__ping__');
        $token = cache()->get('dropbox_access_token');
        if (!$token) {
            return response()->json(['ok' => false, 'message' => 'Dropbox not authenticated — visit the app in a browser first to connect'], 503);
        }

        if (!$album->dropbox_folder) {
            return response()->json(['ok' => false, 'message' => 'No Dropbox folder set for this album — add a subfolder path before syncing'], 422);
        }

        $client    = new \Spatie\Dropbox\Client($token);
        $appFolder = rtrim(env('DROPBOX_APP_FOLDER', ''), '/');
        $folder    = $appFolder . rtrim($album->dropbox_folder, '/');

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4'];

        // List files
        try {
            $result  = $client->listFolder($folder);
            $entries = $result['entries'] ?? [];
            while ($result['has_more'] ?? false) {
                $result  = $client->listFolderContinue($result['cursor']);
                $entries = array_merge($entries, $result['entries'] ?? []);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'ok'          => false,
                'message'     => 'Dropbox error: ' . $e->getMessage(),
                'folder_used' => $folder,
            ], 500);
        }

        $allEntries = collect($entries)->filter(fn($e) => ($e['.tag'] ?? '') === 'file');
        $files = $allEntries
            ->filter(fn($e) => in_array(strtolower(pathinfo($e['name'], PATHINFO_EXTENSION)), $allowed))
            ->map(fn($e) => ['path' => $e['path_lower'], 'name' => $e['name'], 'ext' => strtolower(pathinfo($e['name'], PATHINFO_EXTENSION))])
            ->values()->all();

        // Known paths already synced for this album
        $known = $album->photos()->whereNotNull('dropbox_path')
            ->pluck('dropbox_path')->flip()->all();

        $thumbDir = storage_path('app/public/gallery');
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        $added = 0;
        $errorMessages = [];

        foreach ($files as $file) {
            if (isset($known[$file['path']])) continue;

            try {
                $thumbPath = null;

                if ($file['ext'] === 'mp4') {
                    $localName = 'alb_dbx_' . md5($file['path']) . '.mp4';
                    $localFile = $thumbDir . '/' . $localName;
                    $tmpLink = $client->getTemporaryLink($file['path']);
                    $guzzle  = new \GuzzleHttp\Client();
                    $guzzle->get($tmpLink, ['sink' => $localFile]);
                    $thumbPath = 'gallery/' . $localName;
                    $type      = 'video';
                } elseif ($file['ext'] === 'gif') {
                    $localName = 'alb_dbx_' . md5($file['path']) . '.gif';
                    $stream    = $client->download($file['path']);
                    Storage::disk('public')->put('gallery/' . $localName, $stream);
                    $thumbPath = 'gallery/' . $localName;
                    $type      = 'gif';
                } else {
                    $localName = 'alb_dbx_' . md5($file['path']) . '.jpg';
                    $bytes     = $client->getThumbnail($file['path'], 'jpeg', 'w1024h768');
                    if (!$bytes) throw new \RuntimeException('Empty thumbnail response');
                    Storage::disk('public')->put('gallery/' . $localName, $bytes);
                    $thumbPath = 'gallery/' . $localName;
                    $type      = 'image';
                }

                AlbumPhoto::create([
                    'album_id'     => $album->id,
                    'thumb_path'   => $thumbPath,
                    'type'         => $type,
                    'dropbox_path' => $file['path'],
                ]);

                $known[$file['path']] = true;
                $added++;
            } catch (\Throwable $e) {
                $errorMessages[] = $file['name'] . ': ' . ($e->getMessage() ?: get_class($e));
            }
        }

        return response()->json([
            'ok'          => true,
            'added'       => $added,
            'errors'      => count($errorMessages),
            'error_detail'=> array_slice($errorMessages, 0, 3),
            'files_found' => count($files),
            'total_files' => $allEntries->count(),
            'folder_used' => $folder,
        ]);
    }

    // ── Admin: manually group photos ─────────────────────────────────────────

    public function groupPhotos(Request $request, Album $album)
    {
        $ids     = $request->validate(['ids' => 'required|array|min:2', 'ids.*' => 'integer'])['ids'];
        $key     = (string) Str::uuid();
        $photos  = $album->photos()->whereIn('id', $ids)->get()->keyBy('id');

        // Preserve selection order; push MP4s to end
        $ordered = collect($ids)
            ->map(fn($id) => $photos->get($id))
            ->filter()
            ->sortBy(fn($p) => $p->type === 'video' ? 1 : 0, SORT_REGULAR, false)
            ->values();

        foreach ($ordered as $i => $photo) {
            $photo->update([
                'group_key'      => $key,
                'sort_order'     => $i,
                'is_group_cover' => $i === 0,   // first non-video (or first overall) is cover
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // ── Admin: bulk delete photos ─────────────────────────────────────────────

    public function bulkDestroyPhotos(Request $request, Album $album)
    {
        $ids       = $request->validate(['ids' => 'required|array|min:1', 'ids.*' => 'integer'])['ids'];
        $photos    = $album->photos()->whereIn('id', $ids)->get();
        $dbxDelete = $this->dropboxDeleteEnabled();
        foreach ($photos as $photo) {
            if ($photo->thumb_path) {
                Storage::disk('public')->delete($photo->thumb_path);
            }
            if ($dbxDelete && $photo->dropbox_path) {
                $this->deleteFromDropbox($photo->dropbox_path);
            }
            $photo->delete();
        }
        return response()->json(['ok' => true, 'deleted' => $photos->count()]);
    }

    // ── Admin: remove photos from their group ─────────────────────────────────

    public function ungroupPhotos(Request $request, Album $album)
    {
        $ids = $request->validate(['ids' => 'required|array|min:1', 'ids.*' => 'integer'])['ids'];
        $album->photos()->whereIn('id', $ids)->update(['group_key' => null]);
        return response()->json(['ok' => true]);
    }

    // ── Admin: set group cover photo (the tile shown in the album grid) ───────

    public function setGroupCover(Album $album, AlbumPhoto $photo)
    {
        abort_if($photo->album_id !== $album->id, 404);
        abort_if(!$photo->group_key, 422);

        // Clear flag on all other photos in the same group, then set on this one
        $album->photos()->where('group_key', $photo->group_key)->update(['is_group_cover' => false]);
        $photo->update(['is_group_cover' => true]);

        return response()->json(['ok' => true]);
    }

    // ── Admin: toggle album gate (requires mobile verification) ──────────────

    public function setGated(Request $request, Album $album)
    {
        $album->update(['gated' => $request->boolean('gated')]);
        return response()->json(['ok' => true, 'gated' => $album->gated]);
    }

    // ── Private: Dropbox deletion helpers ────────────────────────────────────

    private function dropboxDeleteEnabled(): bool
    {
        return \App\Models\Setting::where('key', 'dropbox_delete_on_remove')->value('value') === '1';
    }

    private function deleteFromDropbox(string $path): void
    {
        try {
            Storage::disk('dropbox')->exists('__ping__');
            $token = cache()->get('dropbox_access_token');
            if ($token) {
                (new \Spatie\Dropbox\Client($token))->delete($path);
            }
        } catch (\Throwable) {}
    }

    // ── Public: download original file ───────────────────────────────────────

    public function downloadProxy(AlbumPhoto $photo)
    {
        // Images synced from Dropbox: stream original (we only cached a 1024px thumb locally)
        if ($photo->dropbox_path && $photo->type === 'image') {
            Storage::disk('dropbox')->exists('__ping__');
            $token = cache()->get('dropbox_access_token');
            if (!$token) abort(503);

            try {
                $client   = new \Spatie\Dropbox\Client($token);
                $link     = $client->getTemporaryLink($photo->dropbox_path);
                $guzzle   = new \GuzzleHttp\Client();
                $response = $guzzle->get($link, ['stream' => true]);
                $body     = $response->getBody();
                $filename = basename($photo->dropbox_path);

                $headers = [
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Type'        => $response->getHeaderLine('Content-Type') ?: 'application/octet-stream',
                ];
                if ($cl = $response->getHeaderLine('Content-Length')) {
                    $headers['Content-Length'] = $cl;
                }

                return response()->stream(function () use ($body) {
                    while (!$body->eof()) {
                        echo $body->read(65536);
                        flush();
                    }
                }, 200, $headers);
            } catch (\Throwable $e) {
                abort(500);
            }
        }

        // All other types (local uploads, GIFs + videos synced from Dropbox): serve local file
        if (!$photo->thumb_path) abort(404);
        $localPath = storage_path('app/public/' . $photo->thumb_path);
        if (!file_exists($localPath)) abort(404);

        $filename = basename($photo->dropbox_path ?? $photo->thumb_path);
        return response()->download($localPath, $filename);
    }
}
