<?php

namespace App\Http\Middleware;

use App\Application;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckApplication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $tree = collect(explode('/', $request->path()));
        $key = $tree->get(1);
        $token = $tree->get(2);
        $url = $request->get('url');
        $app = Application::fromKey($key);
//        if ($app && $app->checkSign($token, $url)) {
            return $next($request);
//        }
        abort(404, 'The specified token was invalid');
    }

}
