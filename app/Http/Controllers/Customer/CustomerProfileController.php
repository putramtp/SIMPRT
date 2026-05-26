<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerProfileController extends Controller
{
    public function showSignature()
    {
        return view('customer.profile.signature');
    }

    public function storeSignature(Request $request)
    {
        $request->validate(['signature' => 'required|string']);

        $value = $request->signature;

        if (str_starts_with($value, 'data:image/')) {
            $user = Auth::guard('customer')->user();
            $path = 'signatures/' . uniqid('SIGN-') . '.png';

            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }

            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $value));
            Storage::disk('public')->put($path, $imageData);

            $user->update(['signature' => $path]);
        }

        return redirect()->intended(route('customer.dashboard'))->with('success', 'Tanda tangan berhasil disimpan.');
    }

    public function showPassword()
    {
        return view('customer.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
