<?php

namespace App\Http\Controllers\Admin\Leave;

use App\ApiBaseMethod;
use App\Http\Requests\Admin\Leave\SmLeaveRequest as FormRequest;
use App\SmLeaveDefine;
use App\SmLeaveRequest;
use App\tableList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmLeaveRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }


    public function index(Request $request)
    {

        try {
            $user = Auth::user();


            if ($user) {
                $my_leaves = SmLeaveDefine::with('leaveType')->where('user_id', $user->id)->where('role_id', $user->role_id)->where('school_id', Auth::user()->school_id)->get();
                $apply_leaves = SmLeaveRequest::with('leaveDefine')->where('role_id', $user->role_id)->where('active_status', 1)
                    ->where('school_id', Auth::user()->school_id)->has('leaveDefine')->where('staff_id', Auth::user()->id)->get();
                $leave_types = $my_leaves->where('active_status', 1);
            }
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['my_leaves'] = $my_leaves->toArray();
                $data['apply_leaves'] = $apply_leaves->toArray();
                $data['leave_types'] = $leave_types->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.apply_leave', compact('apply_leaves', 'leave_types', 'my_leaves'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(FormRequest $request)
    {

        try {
            // return $maxFileSize;
            $path = 'public/uploads/leave_request/';
            $apply_leave = new SmLeaveRequest();
            $apply_leave->staff_id = Auth::user()->id;
            $apply_leave->role_id = Auth::user()->role_id;
            $apply_leave->apply_date = date('Y-m-d', strtotime($request->apply_date));
            $apply_leave->leave_define_id = $request->leave_type;
            $apply_leave->type_id = $request->leave_type;
            $apply_leave->leave_from = date('Y-m-d', strtotime($request->leave_from));
            $apply_leave->leave_to = date('Y-m-d', strtotime($request->leave_to));
            $apply_leave->approve_status = 'P';
            $apply_leave->reason = $request->reason;
            if ($request->file('attach_file') != "") {
                $apply_leave->file = fileUpload($request->attach_file, $path);
            }
            $apply_leave->school_id = Auth::user()->school_id;
            $apply_leave->academic_id = getAcademicId();
            $apply_leave->save();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (Exception $e) {
            Toastr::error($e->getMessage(), 'Failed');
            return redirect()->back();
        }
    }


    public function show(FormRequest $request, $id)
    {


        try {
            $user = Auth::user();
            if ($user) {
                $my_leaves = SmLeaveDefine::where('user_id', $user->id)->where('role_id', $user->role_id)->where('school_id', Auth::user()->school_id)->get();
                $apply_leaves = SmLeaveRequest::where('role_id', $user->role_id)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
                $leave_types = SmLeaveDefine::where('role_id', $user->role_id)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            }

            $apply_leave = SmLeaveRequest::find($id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['my_leaves'] = $my_leaves->toArray();
                $data['apply_leaves'] = $apply_leaves->toArray();
                $data['leave_types'] = $leave_types->toArray();
                $data['apply_leave'] = $apply_leave->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.humanResource.apply_leave', compact('apply_leave', 'apply_leaves', 'leave_types', 'my_leaves'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(FormRequest $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');


        try {

            $path = 'public/uploads/leave_request/';

            $apply_leave = SmLeaveRequest::find($request->id);
            $apply_leave->staff_id = auth()->user()->id;
            $apply_leave->role_id = auth()->user()->role_id;
            $apply_leave->apply_date = date('Y-m-d', strtotime($request->apply_date));
            $apply_leave->leave_define_id = $request->leave_type;
            $apply_leave->leave_from = date('Y-m-d', strtotime($request->leave_from));
            $apply_leave->leave_to = date('Y-m-d', strtotime($request->leave_to));
            $apply_leave->approve_status = 'P';
            $apply_leave->reason = $request->reason;
            if ($request->file != "") {
                $apply_leave->file = fileUpdate($apply_leave->file, $request->file, $path);
            }
            $apply_leave->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('apply-leave');
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function viewLeaveDetails(Request $request, $id)
    {

        try {
            $leaveDetails = SmLeaveRequest::find($id);

            $apply = "";


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['leaveDetails'] = $leaveDetails->toArray();
                $data['apply'] = $apply;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.viewLeaveDetails', compact('leaveDetails', 'apply'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function destroy(Request $request, $id)
    {

        $tables = tableList::getTableList('leave_request_id', $id);

        try {
            if ($tables == null) {
                $apply_leave = SmLeaveRequest::find($id);

                if ($apply_leave->file != "" && file_exists($apply_leave->file)) {
                    unlink($apply_leave->file);
                }

                $apply_leave->delete();

                Toastr::success('Operation successful', 'Success');
                if (Auth::user()->role_id == 1) {
                    return redirect('pending-leave');
                } else {
                    return redirect('apply-leave');
                }
            } else {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } catch (Exception $e) {
            $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
            Toastr::error($msg, 'Failed');
            return redirect()->back();
        }
    }
}