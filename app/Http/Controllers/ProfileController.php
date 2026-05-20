<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function showSignatureSetup()
    {
        return view('profile.signature');
    }

    public function storeSignature(Request $request)
    {
        $request->validate(['signature' => 'required|string']);

        $value = $request->signature;

        // Only process when a new drawing is submitted (base64 data URL)
        if (str_starts_with($value, 'data:image/')) {
            $user = auth()->user();
            $path = 'signatures/' . $user->id . '.png';

            // Delete previous file if it exists
            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }

            // Decode and save PNG
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $value));
            Storage::disk('public')->put($path, $imageData);

            $user->update(['signature' => $path]);
        }

        return redirect()->intended(route('home'))->with('success', 'Tanda tangan berhasil disimpan.');
    }

    public function showPasswordEdit()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
