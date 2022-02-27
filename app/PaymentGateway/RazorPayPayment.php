<?php 
namespace App\PaymentGateway;

use App\User;
use App\SmFeesAssign;
use App\SmFeesMaster;
use App\SmFeesPayment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Wallet\Entities\WalletAddMoney;
use Modules\Wallet\Entities\WalletTransaction;

class RazorPayPayment{

    public function handel($data)
    {
        try{
            $input = $data;
            if($data['type'] == "Wallet"){
                $addPayment = new WalletTransaction();
                $addPayment->amount= $data['amount'];
                $addPayment->payment_method= $data['payment_method'];
                $addPayment->user_id= $data['user_id'];
                $addPayment->type= $data['wallet_type'];
                $addPayment->school_id= Auth::user()->school_id;
                $addPayment->academic_id= getAcademicId();
                $addPayment->status = 'approve';
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
            }else{
                $fees_payment = new SmFeesPayment();
                $fees_payment->student_id = $data['student_id'];
                $fees_payment->fees_type_id = $data['fees_type_id'];
        
                $fees_payment->discount_amount = 0;
                $fees_payment->fine = 0;
                $fees_payment->amount = $data['amount'] / 100;
                $fees_payment->payment_date = date('Y-m-d', strtotime(date('Y-m-d')));
                $fees_payment->payment_mode = 'RP';
                $result = $fees_payment->save();
        
                $get_master_id=SmFeesMaster::join('sm_fees_assigns','sm_fees_assigns.fees_master_id','=','sm_fees_masters.id')
                ->where('sm_fees_masters.fees_type_id',$data['fees_type_id'])
                ->where('sm_fees_assigns.student_id',$data['student_id'])->first();
        
                $fees_assign= SmFeesAssign::where('fees_master_id',$get_master_id->fees_master_id)->first();
                $fees_assign->fees_amount-=$data['amount'];
                $fees_assign->save();
            }
            // Please check browser console.
            print_r($input);
            exit;
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}