<?php

namespace App\Http\Middleware;

use App\Models\Guest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class GuestVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guestId = session('guest_id');

        $guest = $guestId ? Guest::find($guestId) : null;

        if (!$guest) {
            session()->forget('guest_id');
            return redirect()->guest(route('gate.show'));
        }

        if (!file_exists(storage_path('app/rsvp_mode'))) {
            $rsvp = $guest->rsvp;
            if (!$rsvp || !$rsvp->attending) {
                session()->forget('guest_id');
                return redirect()->route('gate.show')
                    ->withErrors(['mobile' => 'RSVPs are now closed. Only confirmed attendees can access the invitation.']);
            }
        }

        return $next($request);
    }
}
