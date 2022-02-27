<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\SmClass;
use App\SmFeesAssign;
use App\SmFeesMaster;
use App\ApiBaseMethod;
use App\SmBankAccount;
use App\SmFeesPayment;
use App\SmPaymentMethhod;
use Illuminate\Http\Request;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\FeesCollection\SmFeesCollectSearchRequest;
use Illuminate\Support\Facades\Validator;

class SmSearchFeesPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }

    public function index(Request $request)
    {
        try {


            if(auth()->user()->role_id ==1 || auth()->user()->role_id ==5){
                $fees_payments = SmFeesPayment::with('studentInfo')->where('active_status',1)->where('school_id', auth()->user()->school_id)->orderby('id','DESC')->get();

            }else{
                $fees_payments = SmFeesPayment::with('studentInfo')->where('created_by',auth()->user()->id)->where('school_id', auth()->user()->school_id)->where('active_status',1)->orderby('id','DESC')->get();
            }


            $classes = SmClass::where('active_status', 1)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($fees_payments, null);
            }
            return view('backEnd.feesCollection.search_fees_payment', compact('classes','fees_payments'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(SmFeesCollectSearchRequest $request)
    {


        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $classes = SmClass::where('active_status', 1)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $fees_payments = SmFeesPayment::with('studentInfo')->whereHas('studentInfo', function ($q) use($request){
                return $q->where('class_id', $request->class)->where('section_id', $request->section)->when($request->keyword, function ($q) use ($request){
                    return $q->where(function($q) use($request) {
                        return $q->where('full_name', 'like', '%' . @$request->keyword . '%')->orWhere('admission_no', 'like', '%' . @$request->keyword . '%')->orWhere('roll_no','like',  '%' . @$request->keyword . '%');
                    });
                });
            })->where('active_status',1)->orderby('id','DESC')->where('school_id', Auth::user()->school_id);

            if(auth()->user()->role_id != 1 && auth()->user()->role_id != 5) {
                $fees_payments = $fees_payments->where('created_by', auth()->user()->id);
            }

            $fees_payments = $fees_payments->get();
            return view('backEnd.feesCollection.search_fees_payment', compact('fees_payments', 'classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //added by nayem fees edit delete

    public function editFeesPayment($id){

        try {
            $fees_payment = SmFeesPayment::find($id);

            if(auth()->user()->role_id !=1){
                if($fees_payment->created_by !=  auth()->user()->id ){
                    Toastr::error('Payment recieved Other person,You Can not Edit', 'Failed');
                    return redirect()->back();
                }
            }
            $data['bank_info'] = SmPaymentGatewaySetting::where('gateway_name', 'Bank')->where('school_id', Auth::user()->school_id)->first();
            $data['cheque_info'] = SmPaymentGatewaySetting::where('gateway_name', 'Cheque')->where('school_id', Auth::user()->school_id)->first();

            $banks = SmBankAccount::where('school_id', Auth::user()->school_id)
                ->get();
            $method['bank_info'] = SmPaymentMethhod::where('method', 'Bank')->where('school_id', Auth::user()->school_id)->first();
            $method['cheque_info'] = SmPaymentMethhod::where('method', 'Cheque')->where('school_id', Auth::user()->school_id)->first();

            return view('backEnd.feesCollection.edit_fees_payment_modal', compact('fees_payment','data','method','banks'));

        } catch (\Throwable $th) {
            // throw $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }


    public function updateFeesPayment(Request $request){

        try {

            $assignCourseFees=SmFeesAssign::find($request->fees_assign_id);
            $fees_master = SmFeesMaster::find($assignCourseFees->fees_master_id);
            $amount_check = $assignCourseFees->fees_amount - $request->amount;

            if( $fees_master->amount <= $request->amount  ){
                Toastr::warning('Payment amount will not greater than fees assign amount', 'Warning');
                return redirect()->back();
            }elseif( $amount_check < 0){
                $payment=SmFeesPayment::find($request->fees_payment_id);
                $payment->payment_mode = $request->payment_mode;
                $payment->bank_id= $request->payment_mode=='bank' ? $request->bank_id : null;
                $payment->save();
                Toastr::warning('Fees Payment already full paid, Can not Change Amount', 'Warning');
                return redirect()->back();

            }

            if($assignCourseFees->fees_amount==0){

                $pre_amount = $assignCourseFees->fees_amount;

            }else{

                $diff_amount=$request->amount-$request->pre_amount;


                if($diff_amount > 0 ){

                    $pre_amount = $assignCourseFees->fees_amount-$diff_amount;


                }else{

                    $pre_amount = $assignCourseFees->fees_amount-($diff_amount);

                }

            }



            $assignCourseFees->fees_amount=$pre_amount;
            $result= $assignCourseFees->save();
            if($result){
                $payment=SmFeesPayment::find($request->fees_payment_id);
                $payment->amount=$request->amount;
                $payment->payment_mode = $request->payment_mode;
                $payment->bank_id= $request->payment_mode=='bank' ? $request->bank_id : null;
                $payment->save();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }



            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}
