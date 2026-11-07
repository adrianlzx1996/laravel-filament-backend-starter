<?php

namespace App\Http\Middleware;

use App\Enums\RolesEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureBackendUserMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && Auth::user()->hasAnyRole(
            RolesEnum::BACKEND->value
        )) {
            return $next($request);
        }

        abort(403, 'Access denied');
    }
}
