<?php

namespace App\Http\Controllers\Parent;

use App\YearCheck;
use App\SmStudent; 
use App\SmFeesAssign;
use App\SmFeesPayment;
use App\SmFeesAssignDiscount;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmFeesController extends Controller
{
    public function childrenFees($id)
    {
       
        try{
            $student = SmStudent::where('id', $id)->first();
            
            $fees_assigneds = SmFeesAssign::where('student_id', $student->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            $fees_discounts = SmFeesAssignDiscount::where('student_id', $student->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            
            $applied_discount = [];
            foreach ($fees_discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            
            return view('backEnd.parentPanel.childrenFees', compact('student', 'fees_assigneds', 'fees_discounts', 'applied_discount'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
