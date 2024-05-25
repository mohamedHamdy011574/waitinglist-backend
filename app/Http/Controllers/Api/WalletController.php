<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletLog;
use App\Models\Setting;
use App\Http\Resources\WalletHistoryResource;
use DB,Validator,Auth;

class WalletController extends BaseController
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
     * Wallet History.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function wallet_history(Request $request) {
        try{
            $wallet_history = WalletLog::where('customer_id',Auth::user()->id)->orderBy('created_at','desc')->paginate();
            if(count($wallet_history) > 0)
            {
                return $this->sendPaginateResponse(WalletHistoryResource::collection($wallet_history),trans('wallet.wallet_history')); 
            }
            else
            {
                return $this->sendPaginateResponse(WalletHistoryResource::collection($wallet_history),trans('wallet.no_history_found'));
            }
            
        }catch(\Exception $e){
          return $this->sendError('',$e->getMessage()); 
        }
    }

}