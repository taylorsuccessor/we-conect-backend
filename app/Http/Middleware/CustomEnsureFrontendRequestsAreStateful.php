<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomEnsureFrontendRequestsAreStateful
{
    protected function unauthenticated($request, array $guards)
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Redirect to custom URL
        return redirect()->guest('your-custom-url');
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return redirect()->guest('your-custom-url');
        return $next($request);
    }
}
