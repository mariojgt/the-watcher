<?php

namespace Mariojgt\TheWatcher\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TheWatcher
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
        if (Auth::guard('skeleton_admin')->check()) {
            return $next($request);
        }

        return redirect(route('the-watcher.home'));
    }
}
