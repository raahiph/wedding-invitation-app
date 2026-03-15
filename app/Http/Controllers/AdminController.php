<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Photo;
use App\Models\Rsvp;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (session()->has('admin_authed')) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $validUser = hash_equals(env('ADMIN_USER', ''), $request->input('username'));
        $validPass = hash_equals(env('ADMIN_PASS', ''), $request->input('password'));

        if ($validUser && $validPass) {
            session()->regenerate();
            session(['admin_authed' => true]);
            return redirect()->route('admin.index');
        }

        return back()->withErrors(['password' => 'Incorrect username or password.']);
    }

    public function logout()
    {
        session()->forget('admin_authed');
        return redirect()->route('admin.login');
    }

    public function index()
    {
        $guests = Guest::with('rsvp')->latest()->get();

        $totalGuests    = $guests->count();
        $totalAttending = $guests->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count();
        $totalHeads     = $guests->filter(fn($g) => $g->rsvp && $g->rsvp->attending)
                                 ->sum(fn($g) => 1 + $g->plus_ones);
        $groomCount     = $guests->where('side', 'groom')->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count();
        $brideCount     = $guests->where('side', 'bride')->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count();

        $rsvpMode = file_exists(storage_path('app/rsvp_mode'));

        return view('admin.index', compact('guests', 'totalGuests', 'totalAttending', 'totalHeads', 'groomCount', 'brideCount', 'rsvpMode'));
    }

    public function toggleCountdown()
    {
        $flag = storage_path('app/rsvp_mode');

        if (file_exists($flag)) {
            unlink($flag);
            $msg = 'RSVP mode disabled. Full invitation is now active.';
        } else {
            touch($flag);
            $msg = 'RSVP mode enabled. Verified guests will see the RSVP page.';
        }

        return redirect()->route('admin.index')->with('admin_msg', $msg);
    }

    public function store(Request $request)
    {
        $request->validate(['mobile' => 'required|string']);

        $mobile = Guest::normaliseMobile($request->input('mobile'));
        $name   = substr(trim($request->input('name', '')), 0, 120);
        $notes  = substr(trim($request->input('notes', '')), 0, 255);
        $side   = in_array($request->input('side'), ['groom', 'bride', 'other']) ? $request->input('side') : 'other';

        if (strlen($mobile) < 7) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'Invalid mobile number.'], 422);
            }
            return back()->withErrors(['mobile' => 'Invalid mobile number.']);
        }

        $created = Guest::firstOrCreate(['mobile' => $mobile], ['name' => $name, 'notes' => $notes, 'side' => $side]);

        $msg = $created->wasRecentlyCreated ? 'Guest added successfully.' : 'That mobile number is already on the list.';

        if ($request->wantsJson()) {
            return response()->json([
                'ok'      => true,
                'created' => $created->wasRecentlyCreated,
                'message' => $msg,
                'guest'   => [
                    'id'               => $created->id,
                    'mobile'           => $created->mobile,
                    'name'             => $created->name ?? '',
                    'notes'            => $created->notes ?? '',
                    'side'             => $created->side,
                    'attends_ceremony' => (bool) $created->attends_ceremony,
                    'session'          => $created->session,
                    'plus_ones'        => (int) $created->plus_ones,
                    'ceremony_url'     => route('admin.guests.ceremony', $created),
                    'update_url'       => route('admin.guests.update', $created),
                    'destroy_url'      => route('admin.guests.destroy', $created),
                ],
            ]);
        }

        return back()->with('admin_msg', $msg);
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('admin_msg', 'Guest removed.');
    }

    public function adminUpdateGuest(Request $request, Guest $guest)
    {
        $name            = substr(trim($request->input('name', '')), 0, 120);
        $notes           = substr(trim($request->input('notes', '')), 0, 255);
        $side            = in_array($request->input('side'), ['groom', 'bride', 'other']) ? $request->input('side') : 'other';
        $attending       = $request->input('attending'); // 'yes', 'no', or ''
        $plusOnes        = min(4, max(0, (int) $request->input('plus_ones', 0)));
        $attendsCeremony = $request->input('attends_ceremony') === '1';
        $fullName        = substr(trim($request->input('full_name', '')), 0, 120);
        $sessionRaw      = $request->input('session', '');
        $session         = in_array($sessionRaw, ['1', '2']) ? (int) $sessionRaw : null;

        $newMobile = Guest::normaliseMobile($request->input('mobile', $guest->mobile));
        if (strlen($newMobile) >= 7 && $newMobile !== $guest->mobile) {
            $exists = Guest::where('mobile', $newMobile)->where('id', '!=', $guest->id)->exists();
            if ($exists) {
                return response()->json(['ok' => false, 'message' => 'That mobile number is already assigned to another guest.']);
            }
            $guest->mobile = $newMobile;
        }

        $guest->update(['name' => $name, 'notes' => $notes, 'side' => $side, 'attends_ceremony' => $attendsCeremony, 'session' => $session, 'plus_ones' => $plusOnes]);

        if ($attending === '') {
            $guest->rsvp()->delete();
        } else {
            Rsvp::updateOrCreate(
                ['guest_id' => $guest->id],
                [
                    'attending'  => $attending === 'yes',
                    'full_name'  => $fullName ?: ($guest->rsvp?->full_name ?? $name),
                ]
            );
        }

        return response()->json(['ok' => true, 'mobile' => $guest->mobile]);
    }

    public function toggleCeremony(Guest $guest)
    {
        $guest->update(['attends_ceremony' => !$guest->attends_ceremony]);
        if (request()->wantsJson()) {
            return response()->json(['ok' => true, 'attends_ceremony' => (bool) $guest->attends_ceremony]);
        }
        return back();
    }

    public function guestList(string $side)
    {
        $guests = Guest::with('rsvp')->where('side', $side)->latest()->get();

        $totalGuests    = $guests->count();
        $totalAttending = $guests->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count();
        $totalHeads     = $guests->filter(fn($g) => $g->rsvp && $g->rsvp->attending)
                                 ->sum(fn($g) => 1 + $g->plus_ones);

        return view('admin.guests', compact('guests', 'side', 'totalGuests', 'totalAttending', 'totalHeads'));
    }

    public function stats()
    {
        $guests = Guest::with('rsvp')->get();

        return response()->json([
            'total'     => $guests->count(),
            'attending' => $guests->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count(),
            'heads'     => $guests->filter(fn($g) => $g->rsvp && $g->rsvp->attending)
                                  ->sum(fn($g) => 1 + $g->rsvp->plus_ones),
            'pending'   => $guests->filter(fn($g) => !$g->rsvp)->count(),
            'groom'     => $guests->where('side', 'groom')->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count(),
            'bride'     => $guests->where('side', 'bride')->filter(fn($g) => $g->rsvp && $g->rsvp->attending)->count(),
        ]);
    }

    public function bulkDestroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $deleted = Guest::whereIn('id', $ids)->delete();
        return response()->json(['ok' => true, 'deleted' => $deleted]);
    }

    public function importCsv(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['csv' => 'required|file|mimes:csv,txt|max:512']);

        $handle   = fopen($request->file('csv')->getRealPath(), 'r');
        $header   = array_map('strtolower', array_map('trim', fgetcsv($handle)));
        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        while (($cols = fgetcsv($handle)) !== false) {
            $row++;
            if (count($cols) !== count($header)) {
                $errors[] = "Row $row: column count mismatch";
                continue;
            }
            $data = array_combine($header, array_map('trim', $cols));

            $mobile = Guest::normaliseMobile($data['mobile'] ?? '');
            if (strlen($mobile) < 7) {
                $errors[] = "Row $row: invalid mobile";
                continue;
            }

            $name     = substr($data['name']  ?? '', 0, 120);
            $notes    = substr($data['notes'] ?? '', 0, 255);
            $side     = in_array($data['side'] ?? '', ['groom', 'bride', 'other']) ? $data['side'] : 'other';
            $ceremony = in_array(strtolower($data['attends_ceremony'] ?? ''), ['1', 'yes', 'true']);
            $plusOnes = max(0, min(4, (int) ($data['plus_ones'] ?? 0)));

            $guest = Guest::firstOrCreate(
                ['mobile' => $mobile],
                ['name' => $name, 'notes' => $notes, 'side' => $side, 'attends_ceremony' => $ceremony, 'plus_ones' => $plusOnes]
            );

            if ($guest->wasRecentlyCreated) {
                $imported++;
            } else {
                $skipped++;
            }
        }

        fclose($handle);

        return response()->json(compact('imported', 'skipped', 'errors'));
    }

    public function showSettings()
    {
        $defaults = config('wedding');
        $saved    = Setting::all()->pluck('value', 'key')->toArray();
        $settings = array_merge($defaults, $saved);

        // Back-derive raw picker values from stored display values (first-time / legacy data)
        if (empty($settings['event_date']) && !empty($settings['datetime_iso'])) {
            try {
                $dt = new \DateTime($settings['datetime_iso']);
                $settings['event_date'] = $dt->format('Y-m-d');
                if (empty($settings['reception_time_raw'])) {
                    $settings['reception_time_raw'] = $dt->format('H:i');
                }
                if (empty($settings['timezone'])) {
                    $offset = $dt->getOffset();
                    $sign   = $offset >= 0 ? '+' : '-';
                    $abs    = abs($offset);
                    $settings['timezone'] = $sign . sprintf('%02d:%02d', intdiv($abs, 3600), ($abs % 3600) / 60);
                }
            } catch (\Exception $e) {}
        }

        if (empty($settings['rsvp_by_raw']) && !empty($settings['rsvp_by'])) {
            $dt = \DateTime::createFromFormat('F j, Y', $settings['rsvp_by']);
            if ($dt) $settings['rsvp_by_raw'] = $dt->format('Y-m-d');
        }

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        // Store manual / raw fields as-is
        $manualKeys = [
            'groom', 'bride', 'venue', 'city', 'country', 'venue_map_url',
            'dress_code', 'dress_note', 'dress_code_ladies', 'dress_note_ladies', 'dress_code_gents', 'dress_note_gents',
            'reception_time_words',
            'wedding_time_words', 'session1_time_words', 'session2_time_words',
            'event_date', 'reception_time_raw', 'timezone', 'rsvp_by_raw',
            'wedding_start_raw', 'wedding_end_raw',
            'session1_start_raw', 'session1_end_raw',
            'session2_start_raw', 'session2_end_raw',
        ];
        foreach ($manualKeys as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, '')]);
        }

        // Derive and save display formats from raw picker values
        $eventDate        = $request->input('event_date', '');
        $receptionTimeRaw = $request->input('reception_time_raw', '');
        $timezone         = $request->input('timezone', '+05:00');
        $rsvpByRaw        = $request->input('rsvp_by_raw', '');

        if ($eventDate) {
            $dt = new \DateTime($eventDate);
            Setting::updateOrCreate(['key' => 'date'],       ['value' => $dt->format('F j, Y')]);
            Setting::updateOrCreate(['key' => 'date_short'], ['value' => $dt->format('d') . ' | ' . $dt->format('m') . ' | ' . $dt->format('y')]);
            Setting::updateOrCreate(['key' => 'date_day'],   ['value' => $dt->format('l')]);
        }

        if ($receptionTimeRaw) {
            $t = new \DateTime('2000-01-01 ' . $receptionTimeRaw);
            Setting::updateOrCreate(['key' => 'reception_time'], ['value' => $t->format('g:i a')]);
        }

        if ($eventDate && $receptionTimeRaw) {
            Setting::updateOrCreate(['key' => 'datetime_iso'], ['value' => $eventDate . 'T' . $receptionTimeRaw . ':00' . $timezone]);
        }

        if ($rsvpByRaw) {
            $dt = new \DateTime($rsvpByRaw);
            Setting::updateOrCreate(['key' => 'rsvp_by'], ['value' => $dt->format('F j, Y')]);
        }

        // Derive session / wedding window display times
        $timeRawKeys = [
            'wedding_start_raw' => 'wedding_start',
            'wedding_end_raw'   => 'wedding_end',
            'session1_start_raw' => 'session1_start',
            'session1_end_raw'   => 'session1_end',
            'session2_start_raw' => 'session2_start',
            'session2_end_raw'   => 'session2_end',
        ];
        foreach ($timeRawKeys as $rawKey => $displayKey) {
            $raw = $request->input($rawKey, '');
            if ($raw) {
                $t = new \DateTime('2000-01-01 ' . $raw);
                Setting::updateOrCreate(['key' => $displayKey], ['value' => $t->format('g:i a')]);
            }
        }

        return back()->with('settings_saved', 'Settings saved successfully.');
    }
}
