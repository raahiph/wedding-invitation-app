<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class GalleryController extends Controller
{
    public function show()
    {
        $photos = Photo::where('approved', true)->latest()->get();
        return view('gallery', compact('photos'));
    }

    public function uploadPage()
    {
        return view('upload');
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

        // Build a name prefix from the guest if signed in
        $guestId = session('guest_id');
        $prefix  = '';
        if ($guestId) {
            $guest = Guest::find($guestId);
            if ($guest && $guest->name) {
                $slug   = preg_replace('/[^a-z0-9]+/', '-', strtolower($guest->name));
                $prefix = trim($slug, '-') . '_';
            }
        }

        $baseName = $prefix . $uuid;

        // Generate web-optimised thumbnail (1200px wide) and save locally
        $thumbDir  = storage_path('app/public/gallery');
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        $thumbFile = $thumbDir . '/' . $baseName . '.jpg';
        Image::read($file)
            ->scaleDown(width: 1200)
            ->toJpeg(quality: 85)
            ->save($thumbFile);

        $thumbPath = 'gallery/' . $baseName . '.jpg';

        // Upload original to Dropbox
        $dropboxPath = '/' . $baseName . '_original.' . $file->getClientOriginalExtension();
        Storage::disk('dropbox')->put($dropboxPath, file_get_contents($file->getRealPath()));

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

    public function adminIndex()
    {
        $photos = Photo::latest()->get();
        return view('admin.gallery', compact('photos'));
    }

    public function destroy(Photo $photo)
    {
        // Remove local thumbnail
        Storage::disk('public')->delete($photo->thumb_path);

        // Remove original from Dropbox
        // try {
        //     Storage::disk('dropbox')->delete($photo->dropbox_path);
        // } catch (\Exception) {
        //     // Continue even if Dropbox delete fails
        // }

        $photo->delete();

        return response()->json(['ok' => true]);
    }
}
