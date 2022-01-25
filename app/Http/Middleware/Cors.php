<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        return $next($request)
//            ->header('Access-Control-Allow-Origin', '*')
//            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//            ->header('Access-Control-Allow-Credentials', 'true')
//            ->header('Access-Control-Max-Age', '86400')
//            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, Locale, X-Requested-With, Application');
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, Locale, X-Requested-With, Application');

        return $response;
    }
}
