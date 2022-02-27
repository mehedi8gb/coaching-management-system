<?php

namespace Modules\Fees\Http\Controllers;

use App\User;
use App\SmClass;
use App\SmParent;
use App\SmStudent;
use App\SmAddIncome;
use App\SmBankAccount;
use App\SmNotification;
use App\SmPaymentMethhod;
use Illuminate\Http\Request;
use App\SmPaymentGatewaySetting;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesInvoice;
use Illuminate\Support\Facades\Validator;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Entities\FmFeesTransactionChield;

class StudentFeesController extends Controller
{
    public function studentFeesList($id)
    {
        $student_id = SmStudent::find($id);

        $studentInvoices = FmFeesInvoice::where('student_id',$student_id->id)
                            ->where('school_id',Auth::user()->school_id)
                            ->where('academic_id',getAcademicId())
                            ->get();
                            
        return view('fees::student.feesInfo',compact('studentInvoices','student_id'));
    }

    public function studentAddFeesPayment($id)
    {
        try{
            $classes = SmClass::where('school_id',Auth::user()->school_id)
            ->where('academic_id',getAcademicId())
            ->get();

            $feesGroups = FmFeesGroup::where('school_id',Auth::user()->school_id)
                        ->where('academic_id', getAcademicId())
                        ->get();

            $feesTypes = FmFeesType::where('school_id',Auth::user()->school_id)
                        ->where('academic_id', getAcademicId())
                        ->get();

            $paymentMethods = SmPaymentMethhod::whereNotIn('method', ["Cash"])
                                ->where('school_id',Auth::user()->school_id)
                                ->get();
            
            $bankAccounts = SmBankAccount::where('school_id',Auth::user()->school_id)
                            ->where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->get();
            
            $invoiceInfo = FmFeesInvoice::find($id);
            $invoiceDetails = FmFeesInvoiceChield::where('fees_invoice_id',$invoiceInfo->id)
                            ->where('school_id', Auth::user()->school_id)
                            ->where('academic_id', getAcademicId())
                            ->get();

            $stripe_info = SmPaymentGatewaySetting::where('gateway_name', 'stripe')
                            ->where('school_id', Auth::user()->school_id)
                            ->first();

            return view('fees::student.studentAddPayment',compact('classes','feesGroups','feesTypes','paymentMethods','bankAccounts','invoiceInfo','invoiceDetails','stripe_info'));
        }catch(\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function studentFeesPaymentStore(Request $request)
    {
        if($request->total_paid_amount == null){
            Toastr::warning('Paid Amount Can Not Be Blank', 'Failed');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'payment_method' =>  'required',
            'bank' =>  'required_if:payment_method,Bank',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try{
            $destination = 'public/uploads/student/document/';
            $file = fileUpload($request->file('file'), $destination);
            $student=SmStudent::with('parents')->find($request->student_id);
            if($request->payment_method == "Wallet"){
                $currentBalance = User::find(Auth::user()->id);
                if($currentBalance->wallet_balance == 0){
                    Toastr::warning('Insufficiant Balance', 'Warning');
                    return redirect()->back();
                }elseif($currentBalance->wallet_balance >= $request->total_paid_amount){
                    $currentBalance->wallet_balance = $currentBalance->wallet_balance - $request->total_paid_amount;
                    $currentBalance->update();
                }else{
                    Toastr::warning('Total Amount Is Grater Than Wallet Amount', 'Warning');
                    return redirect()->back();
                }
                $addPayment = new WalletTransaction();
                $addPayment->amount= $request->total_paid_amount;
                $addPayment->payment_method= $request->payment_method;
                $addPayment->user_id= $currentBalance->id;
                $addPayment->type= 'expense';
                $addPayment->status= 'approve';
                $addPayment->note= 'Fees Payment';
                $addPayment->school_id= Auth::user()->school_id;
                $addPayment->academic_id= getAcademicId();
                $addPayment->save();

                $storeTransaction = new FmFeesTransaction();
                $storeTransaction->fees_invoice_id = $request->invoice_id;
                $storeTransaction->payment_note = $request->payment_note;
                $storeTransaction->payment_method = $request->payment_method;
                $storeTransaction->bank_id = $request->bank;
                $storeTransaction->student_id = $request->student_id;
                $storeTransaction->user_id = Auth::user()->id;
                $storeTransaction->file = $file;
                $storeTransaction->paid_status = 'approve';
                $storeTransaction->school_id = Auth::user()->school_id;
                $storeTransaction->academic_id = getAcademicId();
                $storeTransaction->save();

                foreach($request->fees_type as $key=>$type){
                    $id = FmFeesInvoiceChield::where('fees_invoice_id',$request->invoice_id)->where('fees_type',$type)->first('id')->id;
                
                    $storeFeesInvoiceChield = FmFeesInvoiceChield::find($id);
                    $storeFeesInvoiceChield->due_amount = $request->due[$key];
                    $storeFeesInvoiceChield->paid_amount = $storeFeesInvoiceChield->paid_amount + $request->paid_amount[$key];
                    $storeFeesInvoiceChield ->update();
                    
                    if($request->paid_amount[$key] > 0){
                        $storeTransactionChield = new FmFeesTransactionChield();
                        $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                        $storeTransactionChield->fees_type = $type;
                        $storeTransactionChield->paid_amount = $request->paid_amount[$key];
                        $storeTransactionChield->note = $request->note[$key];
                        $storeTransactionChield->school_id = Auth::user()->school_id;
                        $storeTransactionChield->academic_id = getAcademicId();
                        $storeTransactionChield->save();
                    }
                }

                // Income
                $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                $income_head = generalSetting();

                $add_income = new SmAddIncome();
                $add_income->name = 'Fees Collect';
                $add_income->date = date('Y-m-d');
                $add_income->amount = $request->total_paid_amount;
                $add_income->fees_collection_id = $storeTransaction->id;
                $add_income->active_status = 1;
                $add_income->income_head_id = $income_head->income_head_id;
                $add_income->payment_method_id = $payment_method->id;
                $add_income->created_by = Auth()->user()->id;
                $add_income->school_id = Auth::user()->school_id;
                $add_income->academic_id = getAcademicId();
                $add_income->save();
            }elseif($request->payment_method == "Cheque" || $request->payment_method == "Bank"){
                $storeTransaction = new FmFeesTransaction();
                $storeTransaction->fees_invoice_id = $request->invoice_id;
                $storeTransaction->payment_note = $request->payment_note;
                $storeTransaction->payment_method = $request->payment_method;
                $storeTransaction->add_wallet_money = $request->add_wallet;
                $storeTransaction->bank_id = $request->bank;
                $storeTransaction->student_id = $request->student_id;
                $storeTransaction->user_id = Auth::user()->id;
                $storeTransaction->file = $file;
                $storeTransaction->paid_status = 'pending';
                $storeTransaction->school_id = Auth::user()->school_id;
                $storeTransaction->academic_id = getAcademicId();
                $storeTransaction->save();
                
                foreach($request->fees_type as $key=>$type){
                    if($request->paid_amount[$key] > 0){
                        $storeTransactionChield = new FmFeesTransactionChield();
                        $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                        $storeTransactionChield->fees_type = $type;
                        $storeTransactionChield->paid_amount = $request->paid_amount[$key] - $request->extraAmount[$key];
                        $storeTransactionChield->note = $request->note[$key];
                        $storeTransactionChield->school_id = Auth::user()->school_id;
                        $storeTransactionChield->academic_id = getAcademicId();
                        $storeTransactionChield->save();
                    }
                }
            }else{
                $storeTransaction = new FmFeesTransaction();
                $storeTransaction->fees_invoice_id = $request->invoice_id;
                $storeTransaction->payment_note = $request->payment_note;
                $storeTransaction->payment_method = $request->payment_method;
                $storeTransaction->student_id = $request->student_id;
                $storeTransaction->add_wallet_money = $request->add_wallet;
                $storeTransaction->user_id = Auth::user()->id;
                $storeTransaction->paid_status = 'pending';
                $storeTransaction->school_id = Auth::user()->school_id;
                $storeTransaction->academic_id = getAcademicId();
                $storeTransaction->save();

                foreach($request->fees_type as $key=>$type){
                    if($request->paid_amount[$key] > 0){
                        $storeTransactionChield = new FmFeesTransactionChield();
                        $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                        $storeTransactionChield->fees_type = $type;
                        $storeTransactionChield->paid_amount = $request->paid_amount[$key]- $request->extraAmount[$key];
                        $storeTransactionChield->note = $request->note[$key];
                        $storeTransactionChield->school_id = Auth::user()->school_id;
                        $storeTransactionChield->academic_id = getAcademicId();
                        $storeTransactionChield->save();
                    }
                }

                $data = [];
                $data['invoice_id'] = $request->invoice_id;
                $data['amount'] = $request->total_paid_amount;
                $data['payment_method'] = $request->payment_method;
                $data['description'] = "Fees Payment";
                $data['type'] = "Fees";
                $data['student_id'] = $request->student_id;
                $data['stripeToken'] = $request->stripeToken;
                $data['transcationId'] = $storeTransaction->id;
                $classMap = config('paymentGateway.'.$data['payment_method']);
                $make_payment = new $classMap();
                $url = $make_payment->handle($data);
                if(!$url){
                    $url = 'fees/student-fees-list';
                }
                if($request->wantsJson()){
                    return response()->json(['goto'=>$url]);
                }else{
                    return redirect($url);
                }
            }

            //Notification
            sendNotification("Add Fees Payment", null, $student->user_id, 2);
            sendNotification("Add Fees Payment", null, $student->parents->user_id, 3);
            sendNotification("Add Fees Payment", null, 1, 1);
            
            Toastr::success('Save Successful', 'Success');
            return redirect()->back();
        }catch(\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
