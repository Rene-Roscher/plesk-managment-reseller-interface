<?php

namespace App\Http\Middleware;

use Closure;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() == null) {
            return redirect()->guest('login');
        }

        if (!$request->user()->is('ADMIN')) {
            if ($request->ajax()) {
                return abort(404);
            } else {
                return redirect()->guest('login');
            }
        }

        return $next($request);
    }
}
