<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasSignature
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && !$user->signature) {
            if (!$request->routeIs('profile.signature.*')) {
                return redirect()->route('profile.signature.show');
            }
        }

        return $next($request);
    }
}
