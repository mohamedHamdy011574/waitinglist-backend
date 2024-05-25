<?php

namespace App\Http\Middleware;

use Closure;

class VendorBusinessDetails
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
        $vendor = $request->user();
        if($vendor->user_type == 'Vendor'){
            if(!$vendor->business) {
                return redirect()->route('businesses.create')->with('error', trans('vendors.business_details_required'));
            }
        }
        return $next($request);
    }
}
