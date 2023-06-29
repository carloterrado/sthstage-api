<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckEndpointAccessMiddleware
{

    public function handle(Request $request, Closure $next)
    {

        $allowedEndpoints = json_decode(DB::table('user_roles')->where('role', Auth::user()->role)->value('endpoint_access'));

        foreach($allowedEndpoints as $allowedEndpoint){
            if ($request->path() == $allowedEndpoint){
                return response()->json(['error' =>  'Unauthorized'], 403);
            }
        }

        return $next($request);
    }
}
