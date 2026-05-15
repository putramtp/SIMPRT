<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function showSignatureSetup()
    {
        return view('profile.signature');
    }

    public function storeSignature(Request $request)
    {
        $request->validate(['signature' => 'required|string']);

        auth()->user()->update(['signature' => $request->signature]);

        return redirect()->intended(route('home'));
    }
}
