<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PrivilegeAdmin
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
        $level = false;
        if(Auth::check()){
            $level = Auth::user()->level;
        } else {
            return redirect('home');
        }
        return $next($request, $level);
    }
}
