<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BlockedUser
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
      $user = Auth::guard('api')->user();
      if($user && $user->status != 'active') {
        return response()->json(['success' => '0','status'=>'401','message' =>  trans('auth.user_inactive'),], 401);
      } else {
        return $next($request);
      }
    }
}
