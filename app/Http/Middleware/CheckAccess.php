<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    public function handle(Request $request, Closure $next)
    {
        // TODO[HASHIM]: use custom logic to access user
        if ($request->user() && true) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
