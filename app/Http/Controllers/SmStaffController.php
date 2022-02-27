<?php

namespace App\Http\Controllers;

use Modules\RolePermission\Entities\InfixRole;
use App\Role;
use App\User;
use App\SmStaff;
use App\tableList;
use App\SmBaseSetup;

use App\SmGeneralSettings;
use App\SmEmailSetting;

use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmLeaveRequest;
use App\SmHumanDepartment;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmHrPayrollGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SmStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function staffList(Request $request)
    {
        
        try{
            $staffs = SmStaff::where('school_id',Auth::user()->school_id)->get();

            $roles = InfixRole::where('active_status', '=', 1)
            ->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })
            ->where('id', '!=', 1)
            ->where('id', '!=', 2)
            ->orderBy('id', 'desc')
            ->get();
            $staffs_api = DB::table('sm_staffs')
    
                ->where('sm_staffs.active_status', 1)
                ->join('roles', 'sm_staffs.role_id', '=', 'roles.id')
                ->join('sm_human_departments', 'sm_staffs.department_id', '=', 'sm_human_departments.id')
                ->join('sm_designations', 'sm_staffs.designation_id', '=', 'sm_designations.id')
                ->join('sm_base_setups', 'sm_staffs.gender_id', '=', 'sm_base_setups.id')
                ->where('sm_staffs.school_id',Auth::user()->school_id)
                ->get();
    
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    
                return ApiBaseMethod::sendResponse($staffs_api, null);
            }
            return view('backEnd.humanResource.staff_list', compact('staffs', 'roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function roleStaffList(Request $request, $role_id)
    {

        
        try{
            $staffs_api = DB::table('sm_staffs')

            ->where('sm_staffs.active_status', 1)
            ->where('role_id', '=', $role_id)
            ->join('roles', 'sm_staffs.role_id', '=', 'roles.id')
            ->join('sm_human_departments', 'sm_staffs.department_id', '=', 'sm_human_departments.id')
            ->join('sm_designations', 'sm_staffs.designation_id', '=', 'sm_designations.id')
            ->join('sm_base_setups', 'sm_staffs.gender_id', '=', 'sm_base_setups.id')
            ->where('sm_staffs.school_id',Auth::user()->school_id)
            ->get();


        if (ApiBaseMethod::checkUrl($request->fullUrl())) {

            return ApiBaseMethod::sendResponse($staffs_api, null);
        }
        return view('backEnd.humanResource.staff_list', compact('staffs', 'roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function addStaff()
    {
        
        try{
            $max_staff_no = SmStaff::where('school_id',Auth::user()->school_id)->max('staff_no');
            // $roles = InfixRole::where('active_status', '=', '1')
            // ->where('id', '!=', 1)->where('id', '!=', 2)
            // ->where('id', '!=', 3)->orderBy('name', 'asc')
            // ->where('school_id',Auth::user()->school_id)
            // ->get();

            $roles = InfixRole::where('active_status', '=', 1)
            ->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })
            ->where('id', '!=', 1)
            ->where('id', '!=', 2)
            ->orderBy('id', 'desc')
            ->get();


            $departments = SmHumanDepartment::where('active_status', '=', '1')->where('school_id',Auth::user()->school_id)->get();
            $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->where('school_id',Auth::user()->school_id)->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
    
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
    
            return view('backEnd.humanResource.addStaff', compact('roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    function staffPicStore(Request $r)
    {
        try {
            $validator = Validator::make($r->all(), [
                'logo_pic' => 'sometimes|required|mimes:jpg,png|max:40000',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => 'valid image upload'], 201);
            }
            if ($r->hasFile('logo_pic')) {
                $file = $r->file('logo_pic');
                $images = Image::make($file)->insert($file);
                $pathImage = 'public/uploads/staff/';
                if (!file_exists($pathImage)) {
                    mkdir($pathImage, 0777, true);
                    $name = md5($file->getClientOriginalName() . time()) . "." . "png";
                    $images->save('public/uploads/staff/' . $name);
                    $imageName = 'public/uploads/staff/' . $name;
                    // $data->staff_photo =  $imageName;
                    Session::put('staff_photo', $imageName);
                } else {
                    $name = md5($file->getClientOriginalName() . time()) . "." . "png";
                    if (file_exists(Session::get('staff_photo'))) {
                        File::delete(Session::get('staff_photo'));
                    }
                    $images->save('public/uploads/staff/' . $name);
                    $imageName = 'public/uploads/staff/' . $name;
                    // $data->staff_photo =  $imageName;
                    Session::put('staff_photo', $imageName);
                }
            }

            return response()->json(['success' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'error'], 201);
        }
    }


    public function staffStore(Request $request)
    {
        $request->validate([
            'staff_no' => "required",
            'role_id' => "required",
            'staff_photo' => "required|image|mimes:jpeg,png,jpg",
            'department_id' => "required",
            'designation_id' => "required",
            'first_name' => "required|max:200",
            'last_name' => "required|max:200",
            'email' => 'required|unique:sm_staffs,email',
            'date_of_joining' => "required",
            'mobile' => "required|max:30",
            'current_address' => "required|max:255",
            'gender_id' => "required",
            'basic_salary' => "required|max:100",
            'resume' => "sometimes|nullable|mimes:pdf,doc,docx|max:40000",
            'joining_letter' => "sometimes|nullable|mimes:pdf,doc,docx|max:40000",
            'other_document' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png|max:10000",
        ]);

        
        try{
            DB::beginTransaction();
            try {
                $user = new User();
                $user->role_id = $request->role_id;
                $user->username = $request->email;
                $user->email = $request->email;
                $user->full_name = $request->first_name . ' ' . $request->last_name;
                $user->password = Hash::make(123456);
                $user->school_id = Auth::user()->school_id;
                $user->save();
                $user->toArray();
    
                // for upload staff photo
                // $staff_photo = "";
                // if ($request->file('staff_photo') != "") {
                //     $file = $request->file('staff_photo');
                //     $staff_photo = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                //     $file->move('public/uploads/staff/', $staff_photo);
                //     $staff_photo = 'public/uploads/staff/' . $staff_photo;
                // }
    
                // for upload resume
                $resume = "";
                if ($request->file('resume') != "") {
                    $file = $request->file('resume');
                    $resume = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/resume/', $resume);
                    $resume = 'public/uploads/resume/' . $resume;
                }
    
                // for upload Staff Joining Letter
                $joining_letter = "";
                if ($request->file('joining_letter') != "") {
                    $file = $request->file('joining_letter');
                    $joining_letter = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/staff_joining_letter/', $joining_letter);
                    $joining_letter = 'public/uploads/staff_joining_letter/' . $joining_letter;
                }
    
                // for upload Staff Other Documents
                $other_document = "";
                if ($request->file('other_document') != "") {
                    $file = $request->file('other_document');
                    $other_document = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/others_documents/', $other_document);
                    $other_document = 'public/uploads/others_documents/' . $other_document;
                }
    
                $basic_salary = '';
                if (!empty($request->basic_salary)) {
                    $basic_salary = $request->basic_salary;
                } else {
                    $basic_salary = 0;
                }
    
                $staff = new SmStaff();
                $staff->staff_no = $request->staff_no;
                $staff->role_id = $request->role_id;
                $staff->department_id = $request->department_id;
                $staff->designation_id = $request->designation_id;
                $staff->first_name = $request->first_name;
                $staff->last_name = $request->last_name;
                $staff->full_name = $request->first_name . ' ' . $request->last_name;
                $staff->fathers_name = $request->fathers_name;
                $staff->mothers_name = $request->mothers_name;
                $staff->email = $request->email;
                $staff->school_id = Auth::user()->school_id;
                if (@Session::get('staff_photo')) {
                    $staff->staff_photo = Session::get('staff_photo');
                }
                $staff->gender_id = $request->gender_id;
                $staff->marital_status = $request->marital_status;
                $staff->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
                $staff->date_of_joining = date('Y-m-d', strtotime($request->date_of_joining));
                $staff->mobile = $request->mobile;
                $staff->emergency_mobile = $request->emergency_mobile;
                $staff->current_address = $request->current_address;
                $staff->permanent_address = $request->permanent_address;
                $staff->qualification = $request->qualification;
                $staff->experience = $request->experience;
                $staff->epf_no = $request->epf_no;
                $staff->basic_salary = $basic_salary;
                $staff->contract_type = $request->contract_type;
                $staff->location = $request->location;
                $staff->bank_account_name = $request->bank_account_name;
                $staff->bank_account_no = $request->bank_account_no;
                $staff->bank_name = $request->bank_name;
                $staff->bank_brach = $request->bank_brach;
                $staff->facebook_url = $request->facebook_url;
                $staff->twiteer_url = $request->twiteer_url;
                $staff->linkedin_url = $request->linkedin_url;
                $staff->instragram_url = $request->instragram_url;
                // $staff->school_id = 1;
                $staff->user_id = $user->id;
                $staff->resume = $resume;
                $staff->joining_letter = $joining_letter;
                $staff->other_document = $other_document;
                $staff->driving_license = $request->driving_license;
                $staff->save();
                $staff->toArray();


                DB::commit();

                $user_info = [];


                if($request->email != ""){
                    $user_info[] =  array('email' => $request->email, 'id' => $staff->id, 'slug' => 'staff');
                }


                 try{


                    if(count($user_info) != 0){
                        $systemSetting = SmGeneralSettings::select('school_name', 'email')->find(1);

                        $systemEmail = SmEmailSetting::find(1);

                        $system_email = $systemEmail->from_email;
                        $school_name = $systemSetting->school_name;

                        $sender['system_email'] = $system_email;
                        $sender['school_name'] = $school_name;

                        dispatch(new \App\Jobs\SendUserMailJob($user_info, $sender));

                    }

                } catch (\Exception $e) {

                    Toastr::success('Operation successful', 'Success');
                    return redirect('staff-directory');

                }



                Toastr::success('Operation successful', 'Success');
                return redirect('staff-directory');
            } catch (\Exception $e) {
                DB::rollback();
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
    
            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect('staff-directory');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function editStaff($id)
    {
        
        try{
            $editData = SmStaff::find($id);
            $max_staff_no = SmStaff::where('school_id',Auth::user()->school_id)->max('staff_no');

            $roles = InfixRole::where('active_status', '=', 1)
            ->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })
            ->where('id', '!=', 1)
            ->where('id', '!=', 2)
            ->orderBy('id', 'desc')
            ->get();

            $departments = SmHumanDepartment::where('active_status', '=', '1')->where('school_id',Auth::user()->school_id)->get();
            $designations = SmDesignation::where('active_status', '=', '1')->where('school_id',Auth::user()->school_id)->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
    
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
    
            return view('backEnd.humanResource.editStaff', compact('editData', 'roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function UpdateStaffApi(Request $request)
    {

        // $request->validate([
        //     'field_name' => "required"
        // ]);
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'field_name' => "required"

            ]);
        }
        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
        }
        
        try{
            if (!empty($request->field_name)) {
                $request_string = $request->field_name;
                $request_id = $request->id;
                $data = SmStaff::find($request_id);
                $data->$request_string = $request->$request_string;
                if ($request_string == "first_name") {
                    $full_name = $request->$request_string . ' ' . $data->last_name;
                    $data->full_name = $full_name;
                } else if ($request_string == "last_name") {
                    $full_name = $data->first_name . ' ' .  $request->$request_string;
                    $data->full_name = $full_name;
                } else if ($request_string == "staff_photo") {
                    $file = $request->file('staff_photo');
                    $images = Image::make($file)->resize(100, 100)->insert($file, 'center');
                    $staff_photos = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $images->save('public/uploads/staff/' . $staff_photos);
                    $staff_photo = 'public/uploads/staff/' . $staff_photos;
                    $data->staff_photo = $staff_photo;
                }
                $data->save();
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['message'] = 'Updated';
                    $data['flag'] = TRUE;
                    return ApiBaseMethod::sendResponse($data, null);
                }
            } else {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['message'] = 'Invalid Input';
                    $data['flag'] = FALSE;
                    return ApiBaseMethod::sendError($data, null);
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function staffProfileUpdate(Request $r, $id)
    {
        
            $validator = Validator::make($r->all(), [
                'logo_pic' => 'sometimes|required|mimes:jpg,png|max:40000',

            ]);
            if ($validator->fails()) {
                return response()->json(['error' => 'error'], 201);
            }
            try {
                $data = SmStaff::findOrFail($id);
                if ($r->hasFile('logo_pic')) {
                    $file = $r->file('logo_pic');
                    $images = Image::make($file)->insert($file);
                    $pathImage = 'public/uploads/staff/';
                    if (!file_exists($pathImage)) {
                        mkdir($pathImage, 0777, true);
                        $name = md5($file->getClientOriginalName() . time()) . "." . "png";
                        $images->save('public/uploads/staff/' . $name);
                        $imageName = 'public/uploads/staff/' . $name;
                        $data->staff_photo =  $imageName;
                    } else {
                        $name = md5($file->getClientOriginalName() . time()) . "." . "png";
                        if (file_exists($data->staff_photo)) {
                            File::delete($data->staff_photo);
                        }
                        $images->save('public/uploads/staff/' . $name);
                        $imageName = 'public/uploads/staff/' . $name;
                        $data->staff_photo =  $imageName;
                    }
                    $data->save();
                }

                return response()->json('success', 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'error'], 201);
            }
    }

    public function staffUpdate(Request $request)
    {

        $request->validate([
            'staff_no' => "required",
            'role_id' => "required",
            'department_id' => "required",
            'designation_id' => "required",
            'first_name' => "required|max:150",
            'email' => 'required|unique:sm_staffs,email,'.$request->staff_id,
            'date_of_joining' => "required",
            'mobile' => "required|max:30",
            'gender_id' => "required",
            'resume' => "sometimes|nullable|mimes:pdf,doc,docx|max:40000",
            'joining_letter' => "sometimes|nullable|mimes:pdf,doc,docx|max:40000",
            'other_document' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png|max:10000",

        ]);

        // for update staff photo
        /*     $staff_photos = "";
        if ($request->file('staff_photo') != "") {
            $photos = SmStaff::find($request->staff_id);
            if ($photos->staff_photo != '' && file_exists($photos->staff_photo)) {
                unlink($photos->staff_photo);
            }
            $file = $request->file('staff_photo');
            $images = Image::make($file)->resize(100, 100)->insert($file, 'center');
            $staff_photos = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $images->save('public/uploads/staff/' . $staff_photos);
            $staff_photo = 'public/uploads/staff/' . $staff_photos;
        } else {
            $photos = SmStaff::find($request->staff_id);
            $staff_photo = $photos->staff_photo;
        }
 */
        // for update resume
       
        try{
            $resume = "";
            if ($request->file('resume') != "") {
                $resumeExistFile = SmStaff::find($request->staff_id);
                if ($resumeExistFile->resume != '' && file_exists($resumeExistFile->resume)) {
                    unlink($resumeExistFile->resume);
                }
                $file = $request->file('resume');
                $resume = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/resume/', $resume);
                $resume = 'public/uploads/resume/' . $resume;
            } else {
                $resumeExistFile = SmStaff::find($request->staff_id);
                $resume = $resumeExistFile->resume;
            }
    
            // for update Staff Joining Letter
            $joining_letter = "";
            if ($request->file('joining_letter') != "") {
                $joiningLetterExistFile = SmStaff::find($request->staff_id);
                if ($joiningLetterExistFile->joining_letter != '' && file_exists($joiningLetterExistFile->joining_letter)) {
                    unlink($joiningLetterExistFile->joining_letter);
                }
                $file = $request->file('joining_letter');
                $joining_letter = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff_joining_letter/', $joining_letter);
                $joining_letter = 'public/uploads/staff_joining_letter/' . $joining_letter;
            } else {
                $joiningLetterExistFile = SmStaff::find($request->staff_id);
                $joining_letter = $joiningLetterExistFile->joining_letter;
            }
    
            // for update Staff Other Documents
            $other_document = "";
            if ($request->file('other_document') != "") {
                $otherDocumentExistFile = SmStaff::find($request->staff_id);
                if ($otherDocumentExistFile->other_document != '' && file_exists($otherDocumentExistFile->other_document)) {
                    unlink($otherDocumentExistFile->other_document);
                }
                $file = $request->file('other_document');
                $other_document = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/others_documents/', $other_document);
                $other_document = 'public/uploads/others_documents/' . $other_document;
            } else {
                $otherDocumentExistFile = SmStaff::find($request->staff_id);
                $other_document = $otherDocumentExistFile->other_document;
            }
    
            $basic_salary = '';
            if (!empty($request->basic_salary)) {
                $basic_salary = $request->basic_salary;
            } else {
                $basic_salary = 0;
            }
    
            $staff = SmStaff::find($request->staff_id);
            $staff->staff_no = $request->staff_no;
            $staff->role_id = $request->role_id;
            $staff->department_id = $request->department_id;
            $staff->designation_id = $request->designation_id;
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->full_name = $request->first_name . ' ' . $request->last_name;
            $staff->fathers_name = $request->fathers_name;
            $staff->mothers_name = $request->mothers_name;
            $staff->email = $request->email;
            // $staff->staff_photo = $staff_photo;
            $staff->gender_id = $request->gender_id;
            $staff->marital_status = $request->marital_status;
            $staff->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
            $staff->date_of_joining = date('Y-m-d', strtotime($request->date_of_joining));
            $staff->mobile = $request->mobile;
            $staff->emergency_mobile = $request->emergency_mobile;
            $staff->current_address = $request->current_address;
            $staff->permanent_address = $request->permanent_address;
            $staff->qualification = $request->qualification;
            $staff->experience = $request->experience;
            $staff->epf_no = $request->epf_no;
            $staff->basic_salary = $basic_salary;
            $staff->contract_type = $request->contract_type;
            $staff->location = $request->location;
            $staff->bank_account_name = $request->bank_account_name;
            $staff->bank_account_no = $request->bank_account_no;
            $staff->bank_name = $request->bank_name;
            $staff->bank_brach = $request->bank_brach;
            $staff->facebook_url = $request->facebook_url;
            $staff->twiteer_url = $request->twiteer_url;
            $staff->linkedin_url = $request->linkedin_url;
            $staff->instragram_url = $request->instragram_url;
            $staff->school_id = 1;
            $staff->user_id = $staff->user_id;
            $staff->resume = $resume;
            $staff->joining_letter = $joining_letter;
            $staff->other_document = $other_document;
            $result = $staff->update();
    
            if ($result) {
                $user = User::find($staff->user_id);
                $user->username = $request->email;
                $user->email = $request->email;
                $user->role_id = $request->role_id;
                $user->full_name = $request->first_name . ' ' . $request->last_name;
                $user->update();

                // return $user;
                Toastr::success('Operation successful', 'Success');
                return redirect('staff-directory');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function staffRoles(Request $request)
    {
        
        try{
            $roles = InfixRole::where('active_status', '=', '1')
            ->select('id', 'name', 'type')
            ->where('id', '!=', 2)
            ->where('id', '!=', 3)
            ->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {


            return ApiBaseMethod::sendResponse($roles, null);
        }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function viewStaff($id)
    {
        
        try{
            $staffDetails = SmStaff::find($id);
             if(Auth::user()->role_id!=1 && Auth::user()->staff->id!=$id){
                Toastr::error('You are not authorized to view this page', 'Failed');
                return redirect()->back(); 
            }
            if (!empty($staffDetails)) {
                $staffPayrollDetails = SmHrPayrollGenerate::where('staff_id', $id)->where('payroll_status', '!=', 'NG')->where('school_id',Auth::user()->school_id)->get();
                $staffLeaveDetails = SmLeaveRequest::where('staff_id', $id)->where('school_id',Auth::user()->school_id)->get();
                $staffDocumentsDetails = SmStudentDocument::where('student_staff_id', $id)->where('type', '=', 'stf')->where('school_id',Auth::user()->school_id)->get();
                $timelines = SmStudentTimeline::where('staff_student_id', $id)->where('type', '=', 'stf')->where('school_id',Auth::user()->school_id)->get();
                return view('backEnd.humanResource.viewStaff', compact('staffDetails', 'staffPayrollDetails', 'staffLeaveDetails', 'staffDocumentsDetails', 'timelines'));
            } else {
                Toastr::error('Something went wrong, please try again', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function staffView(Request $request, $id)
    {
        
        try{
            $staffDetails = SmStaff::find($id);
            if (!empty($staffDetails)) {
                $staffPayrollDetails = SmHrPayrollGenerate::where('staff_id', $id)->where('payroll_status', '!=', 'NG')->where('school_id',Auth::user()->school_id)->get();
                $staffLeaveDetails = SmLeaveRequest::where('staff_id', $id)->where('school_id',Auth::user()->school_id)->get();
                $staffDocumentsDetails = SmStudentDocument::where('student_staff_id', $id)->where('type', '=', 'stf')->where('school_id',Auth::user()->school_id)->get();
                $timelines = SmStudentTimeline::where('staff_student_id', $id)->where('type', '=', 'stf')->where('school_id',Auth::user()->school_id)->get();
    
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['staffDetails'] = $staffDetails->toArray();
                    $data['staffPayrollDetails'] = $staffPayrollDetails->toArray();
                    $data['staffLeaveDetails'] = $staffLeaveDetails->toArray();
                    $data['staffDocumentsDetails'] = $staffDocumentsDetails->toArray();
                    $data['timelines'] = $timelines->toArray();
    
                    return ApiBaseMethod::sendError($data, null);
                }
            } else {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['staffDetails'] = $staffDetails->toArray();
    
                    return ApiBaseMethod::sendError($data, null);
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function searchStaff(Request $request)
    {
        
        try{
            $staff = SmStaff::query();
            $staff->where('active_status', 1);
            if ($request->role_id != "") {
                $staff->where('role_id', $request->role_id);
            }
            if ($request->staff_no != "") {
                $staff->where('staff_no', $request->staff_no);
            }
    
            if ($request->staff_name != "") {
                $staff->where('full_name', 'like', '%' . $request->staff_name . '%');
            }
            $staffs = $staff->where('school_id',Auth::user()->school_id)->get();
            $roles = InfixRole::where('active_status', '=', '1')->where('id', '!=', 2)->where('id', '!=', 3)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['staffs'] = $staffs->toArray();
                $data['roles'] = $roles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.staff_list', compact('staffs', 'roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function uploadStaffDocuments($staff_id)
    {
        
        try{
            return view('backEnd.humanResource.uploadStaffDocuments', compact('staff_id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function saveUploadDocument(Request $request)
    {
        
        try{
            if ($request->file('staff_upload_document') != "" && $request->title != "") {
                $document_photo = "";
                if ($request->file('staff_upload_document') != "") {
                    $file = $request->file('staff_upload_document');
                    $document_photo = 'staff-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/staff/document/', $document_photo);
                    $document_photo = 'public/uploads/staff/document/' . $document_photo;
                }
    
                $document = new SmStudentDocument();
                $document->title = $request->title;
                $document->student_staff_id = $request->staff_id;
                $document->type = 'stf';
                $document->file = $document_photo;
                $document->created_by = Auth()->user()->id;
                $document->school_id = Auth::user()->school_id;
                $results = $document->save();
            }
    
            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteStaffDocumentView(Request $request, $id)
    {
       
        try{
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($id, null);
            }
            return view('backEnd.humanResource.deleteStaffDocumentView', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteStaffDocument($id)
    {
        
        try{
            $result = SmStudentDocument::where('student_staff_id', $id)->first();
            if ($result) {
    
                if (file_exists($result->file)) {
                    File::delete($result->file);
                }
                $result->delete();
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function addStaffTimeline($id)
    {
        
        try{
            return view('backEnd.humanResource.addStaffTimeline', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function storeStaffTimeline(Request $request)
    {
        
        try{
            if ($request->title != "") {

                $document_photo = "";
                if ($request->file('document_file_4') != "") {
                    $file = $request->file('document_file_4');
                    $document_photo = 'stu-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/staff/timeline/', $document_photo);
                    $document_photo = 'public/uploads/staff/timeline/' . $document_photo;
                }
    
                $timeline = new SmStudentTimeline();
                $timeline->staff_student_id = $request->staff_student_id;
                $timeline->title = $request->title;
                $timeline->type = 'stf';
                $timeline->date = date('Y-m-d', strtotime($request->date));
                $timeline->description = $request->description;
                if (isset($request->visible_to_student)) {
                    $timeline->visible_to_student = $request->visible_to_student;
                }
                $timeline->file = $document_photo;
                $timeline->school_id = Auth::user()->school_id;
                $timeline->save();
            }
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteStaffTimelineView($id)
    {
        
        try{
            return view('backEnd.humanResource.deleteStaffTimelineView', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteStaffTimeline($id)
    {
        
        try{
            $result = SmStudentTimeline::destroy($id);
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteStaffView($id)
    {
        
        try{
            return view('backEnd.humanResource.deleteStaffView', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }



    public function deleteStaff($id)
    {
      
            try {
                    $staffs = SmStaff::find($id);
                    $user_id = $staffs->user_id;
                    $result = $staffs->delete();
                    User::destroy($user_id);
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
            } catch (\Illuminate\Database\QueryException $e) {
    
                $msg = 'This data already used, Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        
    
    }


    public function staffDisableEnable(Request $request)
    {
        
        try{
            if ($request->status == 'on') {
                $status = 1;
            } else {
                $status = 0;
            }


            $staff = SmStaff::find($request->id);
            $staff->active_status = $status;
            $staff_id = $staff->user_id;
            $staff->save();


            $user = User::find($staff_id);

            $user->active_status = $status;

            $user->save();
    
            return response()->json($staff);
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}