<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function clear_cache()
    {
      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('view:clear');
      Artisan::call('optimize');
      return "Application Cache is cleared"; 
    }
    
}