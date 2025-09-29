<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  mixed  $request
     * @param  \Closure  $next
     * @param  array  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Ensure Content-Type is set to application/json if it's missing
        if (!$request->headers->has('Content-Type')) {
            $request->headers->set('Content-Type', 'application/json');
        }

        $response = $next($request);
        $response->headers->set('Content-Type', 'application/json');
        // Authenticate the user and handle the request
        // $this->authenticate($request, $guards);

        // Return the response
        return $response;
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return null;
        }

        return null;
    }
}
