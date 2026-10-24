<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! collect($roles)->contains(fn (string $role): bool => $user->hasRole($role))) {
            abort(Response::HTTP_FORBIDDEN, 'No tenés permisos para acceder a esta función.');
        }

        return $next($request);
    }
}
