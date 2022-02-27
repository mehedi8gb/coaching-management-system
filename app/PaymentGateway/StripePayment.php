<?php 
namespace App\PaymentGateway;

use App\User;
use Stripe\Charge;
use Stripe\Stripe;
use App\SmAddIncome;
use App\SmPaymentMethhod;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Controllers\FeesController;
use Modules\Fees\Entities\FmFeesTransactionChield;

class StripePayment{

    public function handle($data)
    {
        try{
            $payment_setting = SmPaymentGatewaySetting::where('gateway_name', 'Stripe')
                            ->where('school_id', Auth::user()->school_id)
                            ->first();

            if(!$payment_setting || !$payment_setting->gateway_secret_key){
                Toastr::warning('Stripe Credentials Can Not Be Blank', 'Warning');
                return redirect()->send()->back();
            }

            Stripe::setApiKey($payment_setting->gateway_secret_key);

            Charge::create([
                "amount" => $data['amount'] * 100,
                "currency" => "usd",
                "source" => $data['stripeToken'],
                "description" => $data['description']
            ]);

            if($data['type'] == "Wallet"){
                $addPayment = new WalletTransaction();
                $addPayment->amount= $data['amount'];
                $addPayment->payment_method= $data['payment_method'];
                $addPayment->user_id= $data['user_id'];
                $addPayment->type= $data['wallet_type'];
                $addPayment->school_id= Auth::user()->school_id;
                $addPayment->academic_id= getAcademicId();
                $result = $addPayment->save();
    
                if($result){
                    $user = User::find($addPayment->user_id);
                    $currentBalance = $user->wallet_balance;
                    $user->wallet_balance = $currentBalance + $data['amount'];
                    $user->update();
                    $gs = generalSetting();
                    $compact['full_name'] =  $user->full_name;
                    $compact['method'] =  $addPayment->payment_method;
                    $compact['create_date'] =  date('Y-m-d');
                    $compact['school_name'] =  $gs->school_name;
                    $compact['current_balance'] =  $user->wallet_balance;
                    $compact['add_balance'] =  $data['amount'];
    
                    @send_mail($user->email, $user->full_name, "wallet_approve", $compact);
                }
            }elseif($data['type'] == "Fees"){
                $addAmount = new FeesController;
                $addAmount->addFeesAmount($data['transcationId'], null);
            }
        }catch(\Exception $e){
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->send()->back();
        }
    }
}