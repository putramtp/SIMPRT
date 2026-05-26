<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCustomerHasSignature
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('customer')->user();

        if ($user && !$user->signature) {
            if (!$request->routeIs('customer.profile.signature.*')) {
                return redirect()->route('customer.profile.signature.show');
            }
        }

        return $next($request);
    }
}
