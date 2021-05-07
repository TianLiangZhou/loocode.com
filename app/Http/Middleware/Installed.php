<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class Installed
{

    const INSTALL_ROUTE = '/install';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $installed = base_path('bootstrap/cache/__installed');
        if (!file_exists($installed) && $request->getPathInfo() != self::INSTALL_ROUTE) {
            return redirect("/install");
        }
        return $next($request);
    }
}
