<?php

namespace App\Http\Controllers\Admin\Leave;

use App\Role;
use App\User;
use App\SmStaff;
use App\SmParent;
use App\YearCheck;
use App\SmLeaveType;
use App\ApiBaseMethod;
use App\SmLeaveDefine;
use App\SmClassTeacher;
use App\SmLeaveRequest;
use App\SmNotification;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use App\SmAssignClassTeacher;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Modules\RolePermission\Entities\InfixRole;
use App\Notifications\LeaveApprovedNotification;
use App\Http\Requests\Admin\Leave\SmApproveLeaveRequest;

class SmApproveLeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {
            $user = Auth::user();
            $staff = SmStaff::where('user_id', Auth::user()->id)->first();
            if (Auth::user()->role_id == 1) {
                $apply_leaves = SmLeaveRequest::with('leaveDefine','staffs','student')->where([['active_status', 1], ['approve_status', '!=', 'P']])->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            } else {
                $apply_leaves = SmLeaveRequest::with('leaveDefine','staffs','student')->where([['active_status', 1], ['approve_status', '!=', 'P'], ['staff_id', '=', $staff->id]])->where('academic_id', getAcademicId())->get();
            }
            $leave_types = SmLeaveType::where('active_status', 1)->get();
            $roles = InfixRole::where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 3)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['apply_leaves'] = $apply_leaves->toArray();
                $data['apply_leaves'] = $leave_types->toArray();
                $data['roles'] = $roles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.humanResource.approveLeaveRequest', compact('apply_leaves', 'leave_types', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function pendingLeave(Request $request)
    {
        try {
            $user = Auth::user();
            $staff = SmStaff::where('user_id', Auth::user()->id)->first();
           
            
            if (checkAdmin()) {
             
                $apply_leaves = SmLeaveRequest::with('leaveDefine','staffs','student')->where([['active_status', 1], ['approve_status', '!=', 'A']])
                                ->where('school_id', Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();
            }elseif($staff->role_id == 4){
                $class_teacher = SmClassTeacher::where('teacher_id', $staff->id)
                                    ->where('school_id', Auth::user()->school_id)
                                    ->where('academic_id',getAcademicId())
                                    ->first();
                                  
                if($class_teacher){
                    $leaves = SmLeaveRequest::where([
                        ['active_status', 1], 
                        ['approve_status', '!=', 'A'],
                        ['role_id', '=', 2]
                        ])
                        ->where('school_id', Auth::user()->school_id)
                        ->where('academic_id',getAcademicId())
                        ->first();
                        $smAssignClassTeacher = SmAssignClassTeacher::find($class_teacher->assign_class_teacher_id);  
                        if($leaves){
                            $apply_leaves = SmLeaveRequest::with(array('student' => function($query)use($smAssignClassTeacher) {
                                $query->where('class_id', $smAssignClassTeacher->class_id)->where('section_id',  $smAssignClassTeacher->section_id);
                            }))->where([
                                ['active_status', 1], 
                                ['approve_status', '!=', 'A'],
                                ['role_id', '=', 2]
                                ])->where('school_id', Auth::user()->school_id)
                            ->where('academic_id',getAcademicId())
                            ->get();
                        }
                }else{
                    $apply_leaves = SmLeaveRequest::with('leaveDefine')->where([
                        ['active_status', 1], 
                        ['approve_status', '!=', 'A'],
                        ['staff_id', '=', $staff->id],
                        ['role_id', '!=', 2]
                        ])
                        ->where('school_id', Auth::user()->school_id)
                        ->where('academic_id',getAcademicId())
                        ->get();
                }
            }elseif(auth()->user()->role_id==1){
                $apply_leaves = SmLeaveRequest::with('leaveDefine','staffs','student')->where([['active_status', 1], ['approve_status', '!=', 'A']])
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id',getAcademicId())
                ->get();
            }
            $leave_types = SmLeaveType::where('active_status', 1)->get();
            $roles = InfixRole::where('id', '!=', 1)->where('id', '!=', 3)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();

            
            return view('backEnd.humanResource.approveLeaveRequest', compact('apply_leaves', 'leave_types', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(SmApproveLeaveRequest $request)
    {

        try {


            $path='public/uploads/leave_request/';
            $fileName =fileUpload($request->attach_file,$path);
            $user = Auth()->user();

            if ($user) {
                $login_id = $user->id;
                $role_id = $user->role_id;
            } else {
                $login_id = $request->login_id;
                $role_id = $request->role_id;
            }
            $leave_request_data = new SmLeaveRequest();
            $leave_request_data->staff_id = $login_id;
            $leave_request_data->role_id =  $role_id;
            $leave_request_data->apply_date = date('Y-m-d', strtotime($request->apply_date));
            $leave_request_data->type_id = $request->leave_type;
            $leave_request_data->leave_from = date('Y-m-d', strtotime($request->leave_from));
            $leave_request_data->leave_to = date('Y-m-d', strtotime($request->leave_to));
            $leave_request_data->approve_status = $request->approve_status;
            $leave_request_data->reason = $request->reason;
            $leave_request_data->file = $fileName;
            $leave_request_data->school_id = Auth::user()->school_id;
            $leave_request_data->save();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id)
    {


        try {
            // $editData = SmLeaveRequest::find($id);
            if (checkAdmin()) {
                $editData = SmLeaveRequest::find($id);
            }else{
                $editData = SmLeaveRequest::where('id',$id)->where('school_id',Auth::user()->school_id)->first();
            }
            $staffsByRole = SmStaff::where('role_id', '=', $editData->role_id)->where('school_id', Auth::user()->school_id)->get();
            $roles = InfixRole::whereOr(['school_id', Auth::user()->school_id], ['school_id', 1])->get();
            $apply_leaves = SmLeaveRequest::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            $leave_types = SmLeaveType::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['editData'] = $editData->toArray();
                $data['staffsByRole'] = $staffsByRole->toArray();
                $data['apply_leaves'] = $apply_leaves->toArray();
                $data['leave_types'] = $leave_types->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.humanResource.approveLeaveRequest', compact('editData', 'staffsByRole', 'apply_leaves', 'leave_types', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
    }


    public function staffNameByRole(Request $request)
    {
        try {
            if ($request->id != 3) {
                $allStaffs = SmStaff::where('role_id', '=', $request->id)->where('school_id', Auth::user()->school_id)->get(['id','full_name','user_id']);
                $staffs = [];
                foreach ($allStaffs as $staffsvalue) {
                    $staffs[] = SmStaff::where('id',$staffsvalue->id)->first(['id','full_name','user_id']);
                }
            } else {
                $staffs = SmParent::where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get(['id','fathers_name','user_id']);
            }
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($staffs, null);
            }
            return response()->json([$staffs]);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function updateApproveLeave(Request $request)
    {

        try {
            if (checkAdmin()) {
                $leave_request_data = SmLeaveRequest::find($request->id);
            }else{
                $leave_request_data = SmLeaveRequest::where('id',$request->id)->where('school_id',Auth::user()->school_id)->first();
            }

            $staff= User::find($leave_request_data->staff_id);
            $role_id = $leave_request_data->role_id;
            $leave_request_data->approve_status = $request->approve_status;
            $leave_request_data->academic_id = getAcademicId();
            $result = $leave_request_data->save();


            $notification = new SmNotification;
            // $notification->user_id = $leave_request_data->id;
            $notification->user_id = $staff->id;
            $notification->role_id = $role_id;
            $notification->date = date('Y-m-d');
            $notification->message = app('translator')->get('leave.leave_status_updated');
            $notification->school_id = Auth::user()->school_id;
            $notification->academic_id = getAcademicId();
            $notification->save();

            try{
                $user=User::find($notification->user_id);
                Notification::send($user, new LeaveApprovedNotification($notification));
            }
            catch (\Exception $e) {
                Log::info($e->getMessage());
            }


                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('approve-leave');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function viewLeaveDetails(Request $request, $id)
    {
        try {


            if (checkAdmin()) {
                $leaveDetails = SmLeaveRequest::find($id);
            }else{
                $leaveDetails = SmLeaveRequest::where('id',$id)->where('school_id',Auth::user()->school_id)->first();
            }
            $staff_leaves = SmLeaveDefine::where('user_id',$leaveDetails->staff_id)->where('role_id', $leaveDetails->role_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['leaveDetails'] = $leaveDetails->toArray();
                $data['staff_leaves'] = $staff_leaves->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.humanResource.viewLeaveDetails', compact('leaveDetails', 'staff_leaves'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}