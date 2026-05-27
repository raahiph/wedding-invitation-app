<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\RsvpController;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

// ── Gate (unprotected) ───────────────────────────────────────────────────────
Route::get('/gate', [GateController::class, 'show'])->name('gate.show');
Route::post('/gate', [GateController::class, 'verify'])->name('gate.verify')->middleware('throttle:10,1');

// ── Gallery ───────────────────────────────────────────────────────────────────
Route::get('/gallery', [GalleryController::class, 'show'])->name('gallery')->middleware('guest.verified');
Route::get('/upload', [GalleryController::class, 'uploadPage'])->name('gallery.upload-page');
Route::post('/gallery', [GalleryController::class, 'upload'])->name('gallery.upload');
Route::get('/gallery/photos', [GalleryController::class, 'photos'])->name('gallery.photos')->middleware('guest.verified');
Route::get('/gallery/dropbox-thumb/{encodedPath}', [GalleryController::class, 'dropboxThumb'])->name('gallery.dropbox-thumb')->middleware('guest.verified');

// ── Public album routes (no auth) ────────────────────────────────────────────
Route::get('/a/dl/{photo}', [AlbumController::class, 'downloadProxy'])->name('album.download');
Route::get('/a/{token}', [AlbumController::class, 'show'])->name('album.show');

// ── Shared: .ics calendar download ───────────────────────────────────────────
$calendarRoute = function () {
    $w     = array_merge(config('wedding'), Setting::all()->pluck('value', 'key')->toArray());
    $guest = \App\Models\Guest::find(session('guest_id'));
    $date  = $w['event_date'] ?? '2026-05-15';
    $tz    = $w['timezone']   ?? '+05:00';

    if ($guest && $guest->attends_ceremony) {
        $startRaw = $w['wedding_start_raw'] ?? '16:45';
        $endRaw   = $w['wedding_end_raw']   ?? '19:30';
    } elseif ($guest && $guest->session == 1) {
        $startRaw = $w['session1_start_raw'] ?? '17:30';
        $endRaw   = $w['session1_end_raw']   ?? '18:30';
    } elseif ($guest && $guest->session == 2) {
        $startRaw = $w['session2_start_raw'] ?? '18:30';
        $endRaw   = $w['session2_end_raw']   ?? '19:30';
    } else {
        $startRaw = $w['reception_time_raw'] ?? '17:00';
        $endRaw   = null;
    }

    $start = new \DateTime($date . 'T' . $startRaw . ':00' . $tz);
    $end   = $endRaw
        ? new \DateTime($date . 'T' . $endRaw . ':00' . $tz)
        : (clone $start)->modify('+2 hours');

    $ics = implode("\r\n", [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//' . $w['groom'] . ' & ' . $w['bride'] . ' Wedding//EN',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'DTSTART;TZID=Indian/Maldives:' . $start->format('Ymd\THis'),
        'DTEND;TZID=Indian/Maldives:'   . $end->format('Ymd\THis'),
        'SUMMARY:' . $w['groom'] . ' & ' . $w['bride'] . ' Wedding',
        'DESCRIPTION:We request the pleasure of your company at the celebration of our marriage.',
        'LOCATION:' . $w['venue'] . ', ' . $w['city'] . ', ' . $w['country'],
        'STATUS:CONFIRMED',
        'END:VEVENT',
        'END:VCALENDAR',
    ]);

    return response($ics, 200, [
        'Content-Type'        => 'text/calendar; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="wedding.ics"',
    ]);
};

// ── RSVP mode: verified guests see save-the-date + RSVP page ─────────────────
if (file_exists(storage_path('app/rsvp_mode'))) {
    Route::middleware('guest.verified')->group(function () use ($calendarRoute) {
        Route::get('/calendar.ics', $calendarRoute)->name('calendar.ics');
        Route::get('/{any?}', function () {
            $guest = \App\Models\Guest::with('category')->find(session('guest_id'));
            $rsvp  = $guest?->rsvp;
            if ($guest?->rsvp_bypass) {
                $recentPhotos  = \App\Models\Photo::where('approved', true)->latest()->take(4)->get();
                $claimedColors = \App\Models\Category::whereNotNull('color')->pluck('color')->map(fn($c) => strtolower($c))->all();
                return view('invitation', compact('rsvp', 'guest', 'recentPhotos', 'claimedColors'));
            }
            return view('rsvp', compact('guest', 'rsvp'));
        })->where('any', '(?!gate|admin|gallery|upload).*');
        Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
    });
} else {
    // ── Invitation pages (protected) ─────────────────────────────────────────
    Route::middleware('guest.verified')->group(function () use ($calendarRoute) {
        Route::get('/', function () {
            $guest         = \App\Models\Guest::with('category')->find(session('guest_id'));
            $rsvp          = $guest?->rsvp;
            $recentPhotos  = \App\Models\Photo::where('approved', true)->latest()->take(4)->get();
            $claimedColors = \App\Models\Category::whereNotNull('color')->pluck('color')->map(fn($c) => strtolower($c))->all();
            return view('invitation', compact('rsvp', 'guest', 'recentPhotos', 'claimedColors'));
        })->name('invitation');
        Route::get('/rsvp', function () {
            $guest = \App\Models\Guest::find(session('guest_id'));
            $rsvp  = $guest?->rsvp;
            return view('rsvp', compact('guest', 'rsvp'));
        })->name('rsvp');
        Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
        Route::get('/calendar.ics', $calendarRoute)->name('calendar.ics');
    });
}

// ── Admin (unprotected login, protected dashboard) ───────────────────────────
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post')->middleware('throttle:5,1');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::post('/guests', [AdminController::class, 'store'])->name('guests.store');
    Route::post('/guests/import', [AdminController::class, 'importCsv'])->name('guests.import');
    Route::post('/guests/bulk-destroy', [AdminController::class, 'bulkDestroy'])->name('guests.bulk-destroy');
    Route::post('/guests/bulk-bypass',  [AdminController::class, 'bulkBypass'])->name('guests.bulk-bypass');
    Route::delete('/guests/{guest}', [AdminController::class, 'destroy'])->name('guests.destroy');
    Route::post('/countdown-toggle', [AdminController::class, 'toggleCountdown'])->name('countdown.toggle');
    Route::post('/guests/{guest}/update', [AdminController::class, 'adminUpdateGuest'])->name('guests.update');
    Route::post('/guests/{guest}/ceremony', [AdminController::class, 'toggleCeremony'])->name('guests.ceremony');
    Route::post('/guests/{guest}/rsvp-sent', [AdminController::class, 'toggleRsvpSent'])->name('guests.rsvp-sent');
    Route::post('/guests/{guest}/invitation-sent', [AdminController::class, 'toggleInvitationSent'])->name('guests.invitation-sent');
    Route::get('/guests/groom', [AdminController::class, 'guestList'])->defaults('side', 'groom')->name('guests.groom');
    Route::get('/guests/bride', [AdminController::class, 'guestList'])->defaults('side', 'bride')->name('guests.bride');
    Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::post('/categories/{category}/color', [AdminController::class, 'updateCategoryColor'])->name('categories.color');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/gallery', [GalleryController::class, 'adminIndex'])->name('gallery');
    Route::post('/gallery/sync', [GalleryController::class, 'adminSync'])->name('gallery.sync');
    Route::delete('/gallery/{photo}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
    Route::post('/albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::get('/albums/{album}', [AlbumController::class, 'manage'])->name('albums.manage');
    Route::post('/albums/{album}/rename', [AlbumController::class, 'rename'])->name('albums.rename');
    Route::delete('/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('/albums/{album}/photos', [AlbumController::class, 'uploadPhotos'])->name('albums.photos.store');
    Route::post('/albums/{album}/photos/group', [AlbumController::class, 'groupPhotos'])->name('albums.photos.group');
    Route::post('/albums/{album}/photos/ungroup', [AlbumController::class, 'ungroupPhotos'])->name('albums.photos.ungroup');
    Route::post('/albums/{album}/photos/bulk-delete', [AlbumController::class, 'bulkDestroyPhotos'])->name('albums.photos.bulk-delete');
    Route::delete('/albums/{album}/photos/{photo}', [AlbumController::class, 'destroyPhoto'])->name('albums.photos.destroy');
    Route::post('/albums/{album}/dropbox', [AlbumController::class, 'setDropboxFolder'])->name('albums.dropbox');
    Route::post('/albums/{album}/sync', [AlbumController::class, 'syncAlbum'])->name('albums.sync');
    Route::post('/albums/{album}/gate', [AlbumController::class, 'setGated'])->name('albums.gate');
    Route::post('/albums/{album}/photos/{photo}/group-cover', [AlbumController::class, 'setGroupCover'])->name('albums.photos.group-cover');
    Route::post('/albums/{album}/photos/{photo}/toggle-hidden', [AlbumController::class, 'toggleHidden'])->name('albums.photos.toggle-hidden');
    Route::post('/albums/{album}/photos/group-hidden', [AlbumController::class, 'toggleGroupHidden'])->name('albums.photos.group-hidden');
});
