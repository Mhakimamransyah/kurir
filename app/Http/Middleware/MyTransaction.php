<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class MyTransaction
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
        DB::beginTransaction(); 
        $response = $next($request);
        return $response;
    }
}
