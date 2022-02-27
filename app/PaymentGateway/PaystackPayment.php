<?php 

namespace App\PaymentGateway;

use App\User;
use App\SmParent;
use App\SmStudent;
use App\SmAddIncome;
use App\SmPaymentMethhod;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Modules\Lms\Entities\CoursePurchaseLog;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Controllers\FeesController;
use Modules\Fees\Entities\FmFeesTransactionChield;

class PaystackPayment{

    public $paystack;

    public function __construct()
    {
        $this->paystack = new Paystack();
    }

    public function handle($data)
    {
        try {
            $payStackData = [];
            $email = "";
            if($data['type'] == "Fees"){
                $student = SmStudent::find($data['student_id']);
                if(!($student->email)){
                    $parent = SmParent::find($student->parent_id);
                    $email =  $parent->guardians_email;
                }else{
                    $email =   $student->email;
                }
            }elseif($data['type'] == "Wallet" || $data['type'] =="Lms" ) {
                $user= User::find($data['user_id']);
                $email=$user->email;
            }
           

            $paystack_info = SmPaymentGatewaySetting::where('gateway_name', 'Paystack')
                            ->where('school_id', Auth::user()->school_id)
                            ->first();

            if(!$paystack_info || !$paystack_info->gateway_secret_key){
                Toastr::warning('Paystack Credentials Can Not Be Blank', 'Warning');
                return redirect()->send()->back();
            }

            Config::set('paystack.publicKey', $paystack_info->gateway_publisher_key);
            Config::set('paystack.secretKey', $paystack_info->gateway_secret_key);
            Config::set('paystack.merchantEmail', $paystack_info->gateway_username);

            if($data['type'] == "Wallet"){
                Session::put('payment_type', "Wallet");
                Session::put('amount',  $data['amount']);
                Session::put('payment_mode', "Paystack");
                Session::put('wallet_type', $data['wallet_type']);
                $payStackData= [
                    "amount" => intval($data['amount']*100),
                    "email" => $email,
                    "callback_url" => '/payment_gateway_success_callback/Paystack',
                    "currency" => (generalSetting()->currency != ""  ? generalSetting()->currency : "ZAR")
                ];

            }elseif($data['type'] == "Fees"){
                Session::forget('amount');
                Session::put('payment_type', $data['type']);
                Session::put('invoice_id', $data['invoice_id']);
                Session::put('amount', $data['amount']);
                Session::put('payment_method',  $data['payment_method']);
                Session::put('transcation_id',  $data['transcationId']);

                $payStackData= [
                        "amount" => intval($data['amount']*100),
                        "email" => $email,
                        "callback_url" => '/payment_gateway_success_callback/Paystack',
                        "currency" => (generalSetting()->currency != ""  ? generalSetting()->currency : "ZAR")
                ];
            }
            elseif($data['type'] == "Lms"){
                Session::put('payment_type', "Lms");
                Session::put('amount',  $data['amount']*100);
                Session::put('payment_mode', "Paystack");
                Session::put('purchase_log_id', $data['purchase_log_id']);
                $payStackData= [
                    "amount" => intval($data['amount']*100),
                    "email" => $email,
                    "callback_url" => '/payment_gateway_success_callback/Paystack',
                    "currency" => (generalSetting()->currency != ""  ? generalSetting()->currency : "ZAR")
            ];
            
            }
            $this->paystack = new Paystack();
            $url = $this->paystack->getAuthorizationResponse($payStackData)['data']['authorization_url'];
            return $url;
        } catch (\Exception $e) {            
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->send()->back();
        }
    }
//url = url + payment_gateway_success_callback/Paystack
    public function successCallBack()
    {
        $user = Auth::User();
        DB::beginTransaction();
        try {
            $user = Auth::User();
            $walletType = Session::get('wallet_type');
            $amount = Session::get('amount');

            if(Session::get('payment_type') == "Wallet") {
                $addPayment = new WalletTransaction();
                $addPayment->amount= $amount;
                $addPayment->payment_method= "Paystack";
                $addPayment->user_id= $user->id;
                $addPayment->type= $walletType;
                $addPayment->school_id= Auth::user()->school_id;
                $addPayment->academic_id= getAcademicId();
                $addPayment->status = 'approve';
                $result = $addPayment->save();
                if($result){
                    $user = User::find($user->id);
                    $currentBalance = $user->wallet_balance;
                    $user->wallet_balance = $currentBalance + $amount;
                    $user->update();
                    $gs = generalSetting();
                    $compact['full_name'] =  $user->full_name;
                    $compact['method'] =  $addPayment->payment_method;
                    $compact['create_date'] =  date('Y-m-d');
                    $compact['school_name'] =  $gs->school_name;
                    $compact['current_balance'] =  $user->wallet_balance;
                    $compact['add_balance'] =  $amount;

                    @send_mail($user->email, $user->full_name, "wallet_approve", $compact);
                }
                DB::commit();

                Session::forget('payment_type');
                Session::forget('amount');
                Session::forget('payment_mode');
                Session::forget('wallet_type');

                return redirect()->route('wallet.my-wallet');
            }elseif(Session::get('payment_type') == "Fees"){
                $transcation= FmFeesTransaction::find(Session::get('transcation_id'));

                $addAmount = new FeesController;
                $addAmount->addFeesAmount(Session::get('fees_payment_id'), null);
                
                DB::commit();

                Session::forget('amount');
                Session::forget('payment_type');
                Session::forget('invoice_id');
                Session::forget('amount');
                Session::forget('payment_method');
                Session::forget('transcation_id');

                Toastr::success('Operation successful', 'Success');
                return redirect()->to(url('fees/student-fees-list',$transcation->student_id));
            }elseif(Session::get('payment_type') == "Lms"){
                if(Session::get('purchase_log_id')){
                    $coursePurchase = CoursePurchaseLog::find(Session::get('purchase_log_id'));
                    $coursePurchase->active_status = 1;
                    $coursePurchase->save();
                    DB::commit();
                    return redirect('lms/student/purchase-log');
                }
                Session::forget('payment_type');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->send()->back();
        }
    }
}