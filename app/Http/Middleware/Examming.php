<?php

namespace App\Http\Middleware;

use Closure;

class Examming
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
        $user = session('user');

        if ($user->start_exam == 0) {
            return redirect('index');
        }
        return $next($request);
    }
}
