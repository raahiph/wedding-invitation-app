<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GateController;
use App\Http\Controllers\RsvpController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

// ── Gate (unprotected) ───────────────────────────────────────────────────────
Route::get('/gate', [GateController::class, 'show'])->name('gate.show');
Route::post('/gate', [GateController::class, 'verify'])->name('gate.verify');

// ── Gallery ───────────────────────────────────────────────────────────────────
Route::get('/gallery', [GalleryController::class, 'show'])->name('gallery')->middleware('guest.verified');
Route::get('/upload', [GalleryController::class, 'uploadPage'])->name('gallery.upload-page');
Route::post('/gallery', [GalleryController::class, 'upload'])->name('gallery.upload');
Route::get('/gallery/photos', [GalleryController::class, 'photos'])->name('gallery.photos')->middleware('guest.verified');

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
            $guest = \App\Models\Guest::find(session('guest_id'));
            $rsvp  = $guest?->rsvp;
            return view('rsvp', compact('guest', 'rsvp'));
        })->where('any', '(?!gate|admin|gallery|upload).*');
        Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
    });
} else {
    // ── Invitation pages (protected) ─────────────────────────────────────────
    Route::middleware('guest.verified')->group(function () use ($calendarRoute) {
        Route::get('/', function () {
            $guest        = \App\Models\Guest::find(session('guest_id'));
            $rsvp         = $guest?->rsvp;
            $recentPhotos = \App\Models\Photo::where('approved', true)->latest()->take(4)->get();
            return view('invitation', compact('rsvp', 'guest', 'recentPhotos'));
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
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::post('/guests', [AdminController::class, 'store'])->name('guests.store');
    Route::post('/guests/import', [AdminController::class, 'importCsv'])->name('guests.import');
    Route::post('/guests/bulk-destroy', [AdminController::class, 'bulkDestroy'])->name('guests.bulk-destroy');
    Route::delete('/guests/{guest}', [AdminController::class, 'destroy'])->name('guests.destroy');
    Route::post('/countdown-toggle', [AdminController::class, 'toggleCountdown'])->name('countdown.toggle');
    Route::post('/guests/{guest}/update', [AdminController::class, 'adminUpdateGuest'])->name('guests.update');
    Route::post('/guests/{guest}/ceremony', [AdminController::class, 'toggleCeremony'])->name('guests.ceremony');
    Route::get('/guests/groom', [AdminController::class, 'guestList'])->defaults('side', 'groom')->name('guests.groom');
    Route::get('/guests/bride', [AdminController::class, 'guestList'])->defaults('side', 'bride')->name('guests.bride');
    Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/gallery', [GalleryController::class, 'adminIndex'])->name('gallery');
    Route::delete('/gallery/{photo}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
});
