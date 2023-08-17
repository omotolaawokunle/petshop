<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;
use App\Services\Traits\Responsable;
use App\Enums\ResponseCodes;

class IsAdmin
{
    use Responsable;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_admin) return $next($request);
        return $this->error(message: 'You are not authorized to access this page!', statusCode: ResponseCodes::HTTP_UNAUTHORIZED);
    }
}
