<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\ApiBaseMethod;
use App\SmPhoneCallLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\AdminSection\SmPhoneCallRequest;

class SmPhoneCallLogController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function index(Request $request)
    {

        try{
            $phone_call_logs = SmPhoneCallLog::get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($phone_call_logs, null);
            }
            return view('backEnd.admin.phone_call', compact('phone_call_logs'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function store(SmPhoneCallRequest $request)
    {



        try{
            $phone_call_log = new SmPhoneCallLog();
            $phone_call_log->name = $request->name;
            $phone_call_log->phone = $request->phone;
            $phone_call_log->date = date('Y-m-d', strtotime($request->date));
            $phone_call_log->description = $request->description;
            $phone_call_log->next_follow_up_date = date('Y-m-d', strtotime($request->follow_up_date));
            $phone_call_log->call_duration = $request->call_duration;
            $phone_call_log->call_type = $request->call_type;
            $phone_call_log->school_id = Auth::user()->school_id;
            $phone_call_log->academic_id = getAcademicId();
            $result = $phone_call_log->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Vehicle has been created successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } 

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }


    public function show(Request $request, $id)
    {

        try{
            $phone_call_logs = SmPhoneCallLog::get();
          
             
            $phone_call_log = SmPhoneCallLog::find($id);
           

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['phone_call_logs'] = $phone_call_logs->toArray();
                $data['phone_call_log'] = $phone_call_log->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.admin.phone_call', compact('phone_call_logs', 'phone_call_log'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function update(SmPhoneCallRequest $request, $id)
    {
    
        try{
             
            $phone_call_log = SmPhoneCallLog::find($request->id);
           
            $phone_call_log->name = $request->name;
            $phone_call_log->phone = $request->phone;
            $phone_call_log->date = date('Y-m-d', strtotime($request->date));
            $phone_call_log->description = $request->description;
            $phone_call_log->next_follow_up_date = date('Y-m-d', strtotime($request->follow_up_date));
            $phone_call_log->call_duration = $request->call_duration;
            $phone_call_log->call_type = $request->call_type;
            $result = $phone_call_log->save();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Call Log has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } 
            Toastr::success('Operation successful', 'Success');
            return redirect('phone-call');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }


    public function destroy(Request $request, $id)
    {

        try{

          
            $phone_call_log = SmPhoneCallLog::find($id);
            
            $result = $phone_call_log->delete();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Call Log has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } 
            Toastr::success('Operation successful', 'Success');
            return redirect('phone-call');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
}