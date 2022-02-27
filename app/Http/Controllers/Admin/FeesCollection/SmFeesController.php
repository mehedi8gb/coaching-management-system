<?php


namespace App\Http\Controllers\Admin\FeesCollection;


use App\SmClass;
use App\SmParent;
use App\SmStudent;
use App\SmAddIncome;
use App\SmsTemplate;
use App\SmFeesAssign;
use App\SmFeesMaster;
use App\SmSmsGateway;
use App\ApiBaseMethod;
use App\SmBankAccount;
use App\SmFeesPayment;
use Twilio\Rest\Client;
use App\SmBankStatement;
use App\SmPaymentMethhod;
use Illuminate\Http\Request;
use App\SmFeesAssignDiscount;
use App\SmPaymentGatewaySetting;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\Accounts\SmFineReportSearchRequest;


class SmFeesController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

 


    public function feesGenerateModal(Request $request, $amount, $student_id, $type,$master,$assign_id)
    {
        try {
            $amount = $amount;
            $master = $master;
            $fees_type_id = $type;
            $student_id = $student_id;

            $banks = SmBankAccount::where('school_id', Auth::user()->school_id)
                    ->get();

            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)
                        ->where('fees_type_id', $fees_type_id)
                        ->where('school_id',Auth::user()->school_id)
                        ->first(); 
            
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['amount'] = $amount;
                $data['discounts'] = $discounts;
                $data['fees_type_id'] = $fees_type_id;
                $data['student_id'] = $student_id;
                return ApiBaseMethod::sendResponse($data, null);
            }

            $data['bank_info'] = SmPaymentGatewaySetting::where('gateway_name', 'Bank')
                                ->where('school_id', Auth::user()->school_id)
                                ->first();

            $data['cheque_info'] = SmPaymentGatewaySetting::where('gateway_name', 'Cheque')
                                ->where('school_id', Auth::user()->school_id)
                                ->first();

            $method['bank_info'] = SmPaymentMethhod::where('method', 'Bank')
                                ->where('school_id', Auth::user()->school_id)
                                ->first();

            $method['cheque_info'] = SmPaymentMethhod::where('method', 'Cheque')
                                    ->where('school_id', Auth::user()->school_id)
                                    ->first();

            return view('backEnd.feesCollection.fees_generate_modal', compact('amount','assign_id','master', 'discounts', 'fees_type_id', 'student_id', 'data', 'method','banks'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function feesGenerateModalChild(Request $request, $amount, $student_id, $type)
    {
        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->where('school_id',Auth::user()->school_id)->get();

            $applied_discount = [];
            foreach ($discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('active_status',1)->where('fees_discount_id', $fees_discount->id)->where('school_id',Auth::user()->school_id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['amount'] = $amount;
                $data['discounts'] = $discounts;
                $data['fees_type_id'] = $fees_type_id;
                $data['student_id'] = $student_id;
                $data['applied_discount'] = $applied_discount;
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.feesCollection.fees_generate_modal_child', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function feesPaymentStore(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            $fileName = "";
            if ($request->file('slip') != "") {
                $file = $request->file('slip');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/bankSlip/', $fileName);
                $fileName = 'public/uploads/bankSlip/' . $fileName;
            }


            $discount_group = explode('-', $request->discount_group);
            $user = Auth::user();
            $fees_payment = new SmFeesPayment();
            $fees_payment->student_id = $request->student_id;
            $fees_payment->fees_type_id = $request->fees_type_id;
            $fees_payment->fees_discount_id = !empty($request->fees_discount_id) ? $request->fees_discount_id : "";
            $fees_payment->discount_amount = !empty($request->applied_amount) ? $request->applied_amount : 0;
            $fees_payment->fine = !empty($request->fine) ? $request->fine : 0;
            $fees_payment->assign_id = $request->assign_id;
            $fees_payment->amount = !empty($request->amount) ? $request->amount : 0;
            $fees_payment->assign_id = $request->assign_id;
            $fees_payment->payment_date = date('Y-m-d', strtotime($request->date));
            $fees_payment->payment_mode = $request->payment_mode;
            $fees_payment->created_by = $user->id;
            $fees_payment->note = $request->note;
            $fees_payment->fine_title = $request->fine_title;
            $fees_payment->school_id = Auth::user()->school_id;
            $fees_payment->slip = $fileName;
            $fees_payment->academic_id = getAcademicid();
            $result = $fees_payment->save();

            

            $payment_mode_name=ucwords($request->payment_mode);
            $payment_method=SmPaymentMethhod::where('method',$payment_mode_name)->first();
            $income_head=generalSetting();

            $add_income = new SmAddIncome();
            $add_income->name = 'Fees Collect';
            $add_income->date = date('Y-m-d', strtotime($request->date));
            $add_income->amount = !empty($request->amount) ? $request->amount : 0;
            $add_income->fees_collection_id = $fees_payment->id;
            $add_income->active_status = 1;
            $add_income->income_head_id = $income_head->income_head_id;
            $add_income->payment_method_id = $payment_method->id;
            if($payment_method->id==3){
                $add_income->account_id = $request->bank_id;
            }
            $add_income->created_by = Auth()->user()->id;
            $add_income->school_id = Auth::user()->school_id;
            $add_income->academic_id = getAcademicId();
            $add_income->save();


            if($payment_method->id==3){
                    $bank=SmBankAccount::where('id',$request->bank_id)
                    ->where('school_id',Auth::user()->school_id)
                    ->first();
                    $after_balance= $bank->current_balance + $request->amount;

                    $bank_statement= new SmBankStatement();
                    $bank_statement->amount= $request->amount;
                    $bank_statement->after_balance= $after_balance;
                    $bank_statement->type= 1;
                    $bank_statement->details= "Fees Payment";
                    $bank_statement->payment_date= date('Y-m-d', strtotime($request->date));
                    $bank_statement->bank_id= $request->bank_id;
                    $bank_statement->school_id= Auth::user()->school_id;
                    $bank_statement->payment_method= $payment_method->id;
                    $bank_statement->save();
    
                    $current_balance= SmBankAccount::find($request->bank_id);
                    $current_balance->current_balance=$after_balance;
                    $current_balance->update();
            }




                // if ($request->discount_group) {
                //     $discount_assign=SmFeesAssignDiscount::where('fees_discount_id',$request->discount_group)->where('student_id',$request->student_id)->first();
                //     $discount_assign->applied_amount+=$request->discount_amount;
                //     $discount_assign->unapplied_amount-=$request->discount_amount;
                //     $discount_assign->save();
                // }
           
            $fees_assign=SmFeesAssign::where('fees_master_id',$request->master_id)->where('student_id',$request->student_id)->first();
            $fees_assign->fees_amount-=floatval($request->amount);
            $fees_assign->save();
            if (!empty($request->fine)) {
                $fees_assign=SmFeesAssign::where('fees_master_id',$request->master_id)->where('student_id',$request->student_id)->first();
                $fees_assign->fees_amount+=$request->fine;
                $fees_assign->save();
            }
            
            
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return Redirect::route('fees_collect_student_wise', array('id' => $request->student_id));
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return Redirect::route('fees_collect_student_wise', array('id' => $request->student_id));
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    


   

    public function feesPaymentDelete(Request $request)
    {
        try {
                $assignFee=SmFeesAssign::find($request->assign_id);

                if($assignFee){
                    $newAmount=$assignFee->fees_amount+$request->amount;
                    $assignFee->fees_amount=$newAmount;
                    $assignFee->save();
                }
                if (checkAdmin()) {
                   
                    $result = SmFeesPayment::destroy($request->id);
                }else{
                   
                    $result = SmFeesPayment::where('active_status',1)->where('id',$request->id)->where('school_id',Auth::user()->school_id)->delete();
                }
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Fees payment has been deleted  successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
           
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function searchFeesDue(Request $request)
    {
        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
            $fees_masters = SmFeesMaster::select('fees_group_id')->where('active_status', 1)->distinct('fees_group_id')->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['fees_masters'] = $fees_masters->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            //
            $students = SmStudent::where('active_status', 1)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $fees_dues = [];
            foreach ($students as $student) {
                $fees_master = SmFeesMaster::select('id', 'amount','date')->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->first();



                $total_amount = @$fees_master->amount;

                $fees_assign = SmFeesAssign::where('student_id', $student->id)->where('fees_master_id', @$fees_master->id)
                    ->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->first();
                $discount_amount = SmFeesAssign::where('student_id', $student->id)->where('academic_id', getAcademicId())->where('fees_master_id', @$fees_master->id)->sum('applied_discount');
                $amount = SmFeesPayment::where('active_status',1)->where('student_id', $student->id)->where('academic_id', getAcademicId())->sum('amount');

                $paid = $discount_amount + $amount;

                if ($fees_assign != "") {
                    if ($total_amount > $paid) {

                        $due_date= strtotime($fees_master->date);
                        $now =strtotime(date('Y-m-d'));
                        if ($due_date > $now ) {
                            continue;
                        }
                        $fees_dues[] = $fees_assign;
                    }
                }
            }
            //
            return view('backEnd.feesCollection.search_fees_due', compact('classes', 'fees_masters','fees_dues'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesDueSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'fees_group' => 'required',
            'class' => 'required'
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
            $fees_group = explode('-', $request->fees_group);
            // $fees_master = SmFeesMaster::select('id', 'amount')->where('fees_group_id', $fees_group[0])->where('fees_type_id', $fees_group[1])->where('school_id',Auth::user()->school_id)->first();
            $fees_master = SmFeesMaster::select('id', 'amount')->where('fees_group_id', $fees_group[0])->where('fees_type_id', $fees_group[1])->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->first();
                if($request->section == ""){
                    $students = SmStudent::where('class_id', $request->class)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
                }else{
                    $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
                }


           
            $fees_dues = [];
            foreach ($students as $student) {
                   $fees_master = SmFeesMaster::select('id', 'amount','date')->where('fees_group_id', $fees_group[0])->where('fees_type_id', $fees_group[1])->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->first();
                    $total_amount = $fees_master->amount;
              
                $fees_assign = SmFeesAssign::where('student_id', $student->id)->where('fees_master_id', $fees_master->id)->where('school_id',Auth::user()->school_id)->where('academic_id', getAcademicId())->first();
                $discount_amount = SmFeesAssign::where('student_id', $student->id)->where('academic_id', getAcademicId())->where('fees_master_id', $fees_master->id)->sum('applied_discount');
                $amount = SmFeesPayment::where('active_status',1)->where('student_id', $student->id)->where('academic_id', getAcademicId())->where('fees_type_id', $fees_group[1])->sum('amount');

                $paid = $discount_amount + $amount;

                if ($fees_assign != "") {
                    if ($total_amount > $paid) {

                        // $due_date= strtotime($fees_master->date);
                        // $now =strtotime(date('Y-m-d'));
                        // if ($due_date > $now ) {
                        //    continue;
                        // }
                        $fees_dues[] = $fees_assign;
                    }
                }
            }
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
            $fees_masters = SmFeesMaster::select('fees_group_id')->where('active_status', 1)->distinct('fees_group_id')->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

            $class_id = $request->class;
            $fees_group_id = $fees_group[1];

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['fees_masters'] = $fees_masters;
                $data['fees_dues'] = $fees_dues;
                $data['class_id'] = $class_id;
                $data['fees_group_id'] = $fees_group_id;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.feesCollection.search_fees_due', compact('classes', 'fees_masters', 'fees_dues', 'class_id', 'fees_group_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function sendDuesFeesEmail(Request $request){
        try{
            
            if(isset($request->send_email)){
                
                $systemEmail = SmsTemplate::first();
                foreach($request->student_list as $student){
                    $student_detail = SmStudent::where('id', $student)->first();
                    $fees_info['dues_fees'] = $request->dues_amount[$student];
                    $fees_info['fees_master'] = $request->fees_master;

                    $compact['student_detail']=$student_detail;
                    $compact['fees_info']=$fees_info;

                    if($student_detail->email != ""){


                       send_mail($student_detail->email, $student_detail->full_name, 'Dues Payment' , 'backEnd.feesCollection.dues_fees_email', $compact);

                      
                    }

                    $parent_detail = SmParent::where('id', $student_detail->parent_id)->first();


                    if($parent_detail->guardians_email != ""){
                       send_mail($parent_detail->guardians_email, $parent_detail->guardians_name, 'Dues Payment' , 'backEnd.feesCollection.dues_fees_email', $compact);

                     
                    }
                }
                

            }elseif(isset($request->send_sms)){
                

                foreach($request->student_list as $student){

                    $student_detail = SmStudent::find($student);
                    $parent_detail = SmParent::find($student_detail->parent_id);

                    $fees_info['dues_fees'] = $request->dues_amount[$student];
                    $fees_info['fees_master'] = $request->fees_master;

                    $email_template = SmsTemplate::where('id',1)->first();

                    $body = $email_template->dues_fees_message_sms;

                    $chars = preg_split('/[\s,]+/', $body, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    foreach($chars as $item){
                        if(strstr($item[0],"[")){

                            $str= str_replace('[','',$item);
                            $str= str_replace(']','',$str);
                            $str= str_replace('.','',$str);

                            $custom_array[$item]= SmsTemplate::getValueByStringDuesFees($student_detail, $str, $fees_info);
                        }

                    }

                    foreach($custom_array as $key=>$value){
                        $body= str_replace($key,$value,$body);
                    }

                    $activeSmsGateway = SmSmsGateway::where('active_status', 1)->first();
                    

                    if($activeSmsGateway->gateway_name == 'Twilio'){
                        
                        $account_id         = $activeSmsGateway->twilio_account_sid; // Your Account SID from www.twilio.com/console
                        $auth_token         = $activeSmsGateway->twilio_authentication_token; // Your Auth Token from www.twilio.com/console
                        $from_phone_number  = $activeSmsGateway->twilio_registered_no;

                        $client = new Client($account_id, $auth_token);


                        // student sms

                        if($student_detail->mobile != ""){

                            $result = $message = $client->messages->create($student_detail->mobile, array('from' => $from_phone_number, 'body' => $body));

                        }

                        // guardian sms
                        if($parent_detail->guardians_mobile != ""){

                            $result = $message = $client->messages->create($parent_detail->guardians_mobile, array('from' => $from_phone_number, 'body' => $body));
                        }

                    }
                    else if ($activeSmsGateway->gateway_name == 'Himalayasms') {
                          
                            if($student_detail->mobile != ""){ 
                                
                                $client = new HttpClient();
                                    $request = $client->get( "https://sms.techhimalaya.com/base/smsapi/index.php", [
                                    'query' => [
                                        'key' => $activeSmsGateway->himalayasms_key,
                                        'senderid' => $activeSmsGateway->himalayasms_senderId,
                                        'campaign' => $activeSmsGateway->himalayasms_campaign,
                                        'routeid' => $activeSmsGateway->himalayasms_routeId ,
                                        'contacts' => $student_detail->mobile,
                                        'msg' => $body,
                                        'type' => "text"
                                    ],
                                    'http_errors' => false
                                 ]);
                        
                                    $result = $request->getBody();
                        }
                        

                        if($parent_detail->fathers_mobile != ""){
                            
                            $client = new HttpClient();
                                $request = $client->get( "https://sms.techhimalaya.com/base/smsapi/index.php", [
                                'query' => [
                                    'key' => $activeSmsGateway->himalayasms_key,
                                    'senderid' => $activeSmsGateway->himalayasms_senderId,
                                    'campaign' => $activeSmsGateway->himalayasms_campaign,
                                    'routeid' => $activeSmsGateway->himalayasms_routeId ,
                                    'contacts' => $parent_detail->fathers_mobile,
                                    'msg' => $body,
                                    'type' => "text"
                                ],
                                'http_errors' => false
                            ]);
                    
                            $result = $request->getBody();
                        }

                       
                    
                        }

                    elseif ($activeSmsGateway->gateway_name == 'Msg91') {
                       
                        $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
                        $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
                        $msg91_route = $activeSmsGateway->msg91_route;
                        $msg91_country_code = $activeSmsGateway->msg91_country_code;

                         if($student_detail->mobile != ""){

                            $curl = curl_init();

                            $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" . $student_detail->mobile . "&authkey=" . $msg91_authentication_key_sid . "&route=" . $msg91_route . "&sender=" . $msg91_sender_id . "&message=" . $body . "&country=91";

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
                            ));
                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            curl_close($curl);

                        }

                       if($parent_detail->guardians_mobile != ""){

                            $curl = curl_init();

                            $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" . $parent_detail->guardians_mobile . "&authkey=" . $msg91_authentication_key_sid . "&route=" . $msg91_route . "&sender=" . $msg91_sender_id . "&message=" . $body . "&country=91";

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
                            ));
                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            curl_close($curl);

                        }
                    }

                }

            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();


        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }



    }
    public function feesStatemnt(Request $request)
    {
        try {
            $classes = SmClass::get();                  
            return view('backEnd.feesCollection.fees_statment', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesStatementSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'student' => 'required',
            'class' => 'required',
            'section' => 'required',
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
            $classes = SmClass::get();
            $fees_masters = SmFeesMaster::select('fees_group_id')->distinct('fees_group_id')->get();
            $student = SmStudent::find($request->student);
            $fees_assigneds = SmFeesAssign::where('student_id', $request->student)->where('school_id',Auth::user()->school_id)->get();
            if ($fees_assigneds->count() <= 0) {
                Toastr::error('Fees assigned not yet!');
                return redirect()->back();
            }
            else
            $fees_discounts = SmFeesAssignDiscount::where('student_id', $request->student)->where('school_id',Auth::user()->school_id)->get();
            $applied_discount = [];
            foreach ($fees_discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::where('active_status',1)->select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->where('school_id',Auth::user()->school_id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            $class_id = $request->class;
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['fees_masters'] = $fees_masters->toArray();
                $data['fees_assigneds'] = $fees_assigneds->toArray();
                $data['fees_discounts'] = $fees_discounts->toArray();
                $data['applied_discount'] = $applied_discount;
                $data['student'] = $student;
                $data['class_id'] = $class_id;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.feesCollection.fees_statment', compact('classes', 'fees_masters', 'fees_assigneds', 'fees_discounts', 'applied_discount', 'student', 'class_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    public function feesInvoice($sid, $pid, $faid)
    {
        try {
            return view('backEnd.feesCollection.fees_collect_invoice');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesGroupPrint($id)
    {
        $fees_assigned = SmFeesAssign::find($id);
        $student = SmStudent::find($fees_assigned->student_id);
    }

    public function feesPaymentPrint($id, $group)
    {
        try {
            // $payment = SmFeesPayment::find($id);
             if (checkAdmin()) {
                    $payment = SmFeesPayment::find($id);
                }else{
                    $payment = SmFeesPayment::where('active_status',1)->where('id',$id)->where('school_id',Auth::user()->school_id)->first();
                }
            $group = $group;
            $student = SmStudent::find($payment->student_id);
            $pdf = PDF::loadView('backEnd.feesCollection.fees_payment_print', ['payment' => $payment, 'group' => $group, 'student' => $student]);
            return $pdf->stream(date('d-m-Y') . '-' . $student->full_name . '-fees-payment-details.pdf');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function feesPaymentInvoicePrint($id, $s_id)
    {
        try {
            set_time_limit(2700);
            $groups = explode("-", $id);
            $student = SmStudent::find($s_id);
            foreach ($groups as $group) {
                $fees_assigneds[] = SmFeesAssign::find($group);
            }
            $parent = DB::table('sm_parents')->where('id', $student->parent_id)->where('school_id',Auth::user()->school_id)->first();

            $unapplied_discount_amount = SmFeesAssignDiscount::where('student_id',$s_id)->sum('unapplied_amount');
            return view('backEnd.feesCollection.fees_payment_invoice_print')->with(['fees_assigneds' => $fees_assigneds, 'student' => $student,'unapplied_discount_amount'=>$unapplied_discount_amount, 'parent' => $parent]);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function feesGroupsPrint($id, $s_id)
    {
        try {
            $groups = explode("-", $id);
            $student = SmStudent::find($s_id);
            foreach ($groups as $group) {
                $fees_assigneds[] = SmFeesAssign::find($group);
            }
            $pdf = PDF::loadView('backEnd.feesCollection.fees_groups_print', ['fees_assigneds' => $fees_assigneds, 'student' => $student]);
            return $pdf->stream(date('d-m-Y') . '-' . $student->full_name . '-fees-groups-details.pdf');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

 

    public function studentFineReport(Request $request)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, null);
            }
            return view('backEnd.reports.student_fine_report');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentFineReportSearch(Request $request)
    {
        try {
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            $fees_payments = SmFeesPayment::where('active_status',1)->where('payment_date', '>=', $date_from)->where('payment_date', '<=', $date_to)->where('fine', '!=', 0)->where('school_id',Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($fees_payments, null);
            }
            return view('backEnd.reports.student_fine_report', compact('fees_payments'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //


    public function fineReport(){
        $classes = SmClass::get();            
        return view('backEnd.accounts.fine_report',compact('classes'));
    }

    public function fineReportSearch(SmFineReportSearchRequest $request){

        $rangeArr = $request->date_range ? explode('-', $request->date_range) : "".date('m/d/Y')." - ".date('m/d/Y')."";

        try {
            $classes = SmClass::get();

            if($request->date_range){
                $date_from = new \DateTime(trim($rangeArr[0]));
                $date_to =  new \DateTime(trim($rangeArr[1]));
            }

            if($request->date_range ){
                $fine_info = SmFeesPayment::where('active_status',1)->where('payment_date', '>=', $date_from)
                                ->where('payment_date', '<=', $date_to)
                                ->where('school_id',Auth::user()->school_id)
                                ->get();

                $fine_info = $fine_info->groupBy('student_id');
            }

            if($request->class){
                $students=SmStudent::where('class_id',$request->class)                        
                        ->get();

                $fine_info = SmFeesPayment::where('active_status',1)->where('payment_date', '>=', $date_from)
                                ->where('payment_date', '<=', $date_to)
                                ->where('school_id',Auth::user()->school_id)
                                ->whereIn('student_id', $students)
                                ->get();
                $fine_info = $fine_info->groupBy('student_id');

            }

            if($request->class && $request->section){

                $students=SmStudent::where('class_id',$request->class)
                        ->where('section_id',$request->section)                      
                        ->get();

                $fine_info = SmFeesPayment::where('active_status',1)->where('payment_date', '>=', $date_from)
                                ->where('payment_date', '<=', $date_to)
                                ->where('school_id',Auth::user()->school_id)
                                ->whereIn('student_id', $students)
                                ->get();

                $fine_info = $fine_info->groupBy('student_id');
            }
            return view('backEnd.accounts.fine_report',compact('classes','fine_info'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }




}