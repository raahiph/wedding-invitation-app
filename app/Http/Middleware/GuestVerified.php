<?php

namespace App\Http\Middleware;

use App\Models\Guest;
use Closure;
use Illuminate\Http\Request;
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

        if (!$guestId || !Guest::where('id', $guestId)->exists()) {
            session()->forget('guest_id');
            return redirect()->guest(route('gate.show'));
        }

        return $next($request);
    }
}
