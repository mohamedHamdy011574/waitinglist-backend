<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GemsLog;
use App\Models\WalletLog;
use App\Models\Setting;
use App\Models\Sponsor;
use App\Models\Advertisement;
use App\Http\Resources\GemsResource;
use App\Http\Resources\WalletResource;
use DB,Validator,Auth;
use App\Models\Helpers\CommonHelpers;


class GemsController extends BaseController
{
    use CommonHelpers;
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
     * Get Gems by watching sponsor ad.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_gems_by_watch_ad(Request $request) {
        try{
            
            $validator = Validator::make($request->all(),[
                'id' => 'required|numeric',
                'type' => 'required|min:3|max:100'
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());
            }

            if($request->type != 'sponsor' && $request->type != 'advertisement')
            {
                return $this->sendValidationError('', trans('gems.type_not_valid'));
            }
            $data = [];
            if($request->type == 'sponsor')
            {
                $sponsor = Sponsor::find($request->id);
                if(!$sponsor)
                {
                    return $this->sendValidationError('', trans('gems.sponsor_not_found'));
                }
                $data['sponsor_id'] = $request->id;
                $type = 'sponsor';
                $gems = Setting::get('gems_earning_per_sponsor_ad');
                $adAlreadyWatched = GemsLog::where('sponsor_id',$request->id)->where('customer_id',Auth::user()->id)->first();
            }
            else
            {
                $advertisement = Advertisement::find($request->id);
                if(!$advertisement)
                {
                    return $this->sendValidationError('', trans('gems.advertisement_not_found'));
                }
                $data['advertisement_id'] = $request->id;
                $type = 'advertisement';
                $gems = Setting::get('gems_earning_per_ad');
                $adAlreadyWatched = GemsLog::where('advertisement_id',$request->id)->where('customer_id',Auth::user()->id)->first();
            }
            if($adAlreadyWatched)
            {
                return $this->sendError('', trans('gems.ad_already_watched'));
            }

            $data['customer_id'] = Auth::user()->id;
            $data['type'] = $type;
            $data['earned_gems'] = $gems;

            // echo "<pre>";print_r($data);exit;
            DB::beginTransaction();

            $gemsLog = GemsLog::create($data);
            $user =  User::find(Auth::user()->id);
            $gemsAdded = $user->increment('gems',$gems);
            if($gemsLog && $gemsAdded)
            {
                DB::commit();
                return $this->sendResponse(new GemsResource($user),trans('gems.gems_added')); 
            }
            else
            {
                DB::rollback();
                return $this->sendError('',trans('common.something_went_wrong')); 
            }
            
        }catch(\Exception $e){          
          DB::rollback();
          return $this->sendError('',$e->getMessage()); 
        }
    }

    /**
     * Convert gems to wallet.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function convert_gems_to_wallet(Request $request) {
        try{
            $min_gems = Setting::get('currency_exchange_rate');
            $max_gems = Auth::user()->gems;

            if(Auth::user()->gems == 0)
            {
                return $this->sendValidationError('', trans('gems.you_have_no_gems'));
            }
            
            $validator = Validator::make($request->all(),[
                'gems' => 'required|numeric|min:'.$min_gems.'|max:'.$max_gems
            ]);

            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());
            }
            
            $exchange_rate = Setting::get('currency_exchange_rate');
            $amount = $request->gems / $exchange_rate;
            $data = [
                'customer_id' => Auth::user()->id,
                'amount' => $amount,
                'type' => 'added',
            ];
            DB::beginTransaction();

            $walletLog    = WalletLog::create($data);
            $user         = User::find(Auth::user()->id);
            $decreaseGems = $user->decrement('gems',$request->gems);
            $addtoWallet  = $user->increment('e_wallet_amount',$amount);

            $currency = Setting::get('currency');
            if($walletLog && $decreaseGems && $addtoWallet)
            {
                DB::commit();

                //send notification
                $title = trans('gems.notification_title');
                if($walletLog->type == 'added'){
                    $body = trans('gems.notification_body_for_added',['amount' => Setting::get('currency').' '.$walletLog->amount]);
                }else{
                    $body = trans('gems.notification_body_for_removed',['amount' => Setting::get('currency').' '.$walletLog->amount]);
                }
                $nres = $this->sendNotification($user,$title,$body,"e_wallet_balance","");

                return $this->sendResponse(new WalletResource($user),trans('gems.gems_to_wallet_converted',['amount' => $currency.' '.number_format((float)$amount, 3, '.', '')])); 
            }
            else
            {
                DB::rollback();
                return $this->sendError('',trans('common.something_went_wrong')); 
            }
            
        }catch(\Exception $e){          
          DB::rollback();
          return $this->sendError('',$e->getMessage()); 
        }
    }

    /**
     * Convert gems to wallet.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gems_to_wallet_exchange_rate(Request $request) {
        $data['gems_to_wallet_exchange_rate'] = Setting::get('currency_exchange_rate');
        return $this->sendResponse($data,trans('gems.gems_to_wallet_exchange_rate'));
    }
    
    
}