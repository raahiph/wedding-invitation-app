<?php

namespace App\Http\Controllers;

use App\Models\Rsvp;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'attending' => 'required|boolean',
        ]);

        Rsvp::updateOrCreate(
            ['guest_id' => session('guest_id')],
            [
                'guest_id'  => session('guest_id'),
                'attending' => $data['attending'],
            ]
        );

        $message = $data['attending']
            ? 'We can\'t wait to celebrate with you!'
            : 'Thank you for letting us know.';

        return response()->json(['ok' => true, 'message' => $message]);
    }
}
