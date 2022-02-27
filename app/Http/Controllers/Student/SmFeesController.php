<?php

namespace App\Http\Controllers\Student;

use App\SmStudent;
use App\SmFeesAssign;
use App\SmFeesPayment;
use App\SmNotification;
use App\SmPaymentMethhod;
use Illuminate\Http\Request;
use App\SmFeesAssignDiscount;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Session;

class SmFeesController extends Controller
{
    public $paystack;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->paystack = new Paystack();
    }


    public function studentFees()
    {
        try {
            $id = Auth::user()->id;
            $student = SmStudent::where('user_id', $id)->where('school_id',Auth::user()->school_id)->first();

            $payment_gateway = SmPaymentMethhod::where('active_status', 1)->where('school_id',Auth::user()->school_id)->first();

            $fees_assigneds = SmFeesAssign::where('student_id', $student->id)->where('school_id',Auth::user()->school_id)->get();
            $fees_discounts = SmFeesAssignDiscount::where('student_id', $student->id)->where('school_id',Auth::user()->school_id)->get();

            $applied_discount = [];
            foreach ($fees_discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->where('school_id',Auth::user()->school_id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            
            return view('backEnd.studentPanel.fees_pay', compact('student', 'fees_assigneds', 'fees_discounts', 'applied_discount', 'payment_gateway'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function redirectToGateway(Request $request)
    {
        try {

            Session::put('fees_type_id', $request->fees_type_id);
            Session::put('student_id', $request->student_id);
            Session::put('amount', $request->amount);
            Session::put('payment_mode', $request->payment_mode);
            return $this->paystack->getAuthorizationUrl()->redirectNow();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        try {
            $paymentDetails = $this->paystack->getPaymentData();
            $user = Auth::User();
   
            // $student = SmStudent::where('user_id', $id)->where('school_id',Auth::user()->school_id)->first();

            $amount = Session::get('amount');
            $amount = $amount / 100;
            $fees_payment = new SmFeesPayment();
            $fees_payment->student_id = Session::get('student_id');
            $fees_payment->fees_type_id = Session::get('fees_type_id');

            $fees_payment->discount_amount = 0;
            $fees_payment->fine = 0;
            $fees_payment->amount = $amount;
            $fees_payment->payment_date = date('Y-m-d', strtotime(date('Y-m-d')));
            $fees_payment->payment_mode = 'PS';
            $fees_payment->school_id = Auth::user()->school_id;
            $result = $fees_payment->save();

            // $notification=new SmNotification();
            // $notification->date=$fees_payment->created_at;
            // $notification->url=$fees_payment->created_at;

            if ($result) {
                if ($user->role_id == 2) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('student-fees');
                    // return redirect('student-fees')->with('message-success', 'Fees payment has been collected  successfully');
                } else {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('parent-fees/' . Session::get('student_id'));
                    // return redirect('parent-fees/'.Session::get('student_id'))->with('message-success', 'Fees payment has been collected  successfully');
                }
            } else {
                if ($user->role_id == 2) {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect('student-fees');
                    // return redirect('student-fees')->with('message-danger', 'Something went wrong, please try again');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect('student-fees');
                    // return redirect('student-fees')->with('message-danger', 'Something went wrong, please try again');
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
