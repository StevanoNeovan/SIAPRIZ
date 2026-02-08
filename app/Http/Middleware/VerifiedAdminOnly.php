<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedAdminOnly
{
    public function handle($request, Closure $next)
{
    $user = auth()->user();

    if ($user->id_role == 1 && ! $user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    return $next($request);
}

}
