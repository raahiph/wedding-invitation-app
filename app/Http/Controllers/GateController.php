<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GateController extends Controller
{
    public function show()
    {
        if (session()->has('guest_id')) {
            return redirect()->intended('/');
        }

        return view('gate');
    }

    public function verify(Request $request)
    {
        $request->validate(['mobile' => 'required|string']);

        $mobile = Guest::normaliseMobile($request->input('mobile'));

        if (strlen($mobile) < 7 || strlen($mobile) > 15) {
            return back()->withErrors(['mobile' => 'That doesn\'t look like a valid mobile number.'])->withInput();
        }

        $guest = Guest::where('mobile', $mobile)->first();

        if (!$guest) {
            return back()->withErrors(['mobile' => 'Your number wasn\'t found on our guest list.'])->withInput();
        }

        session()->regenerate();
        session(['guest_id' => $guest->id, 'guest_name' => $guest->name]);

        return redirect()->intended('/');
    }
}
