<?php

namespace App\Http\Controllers\Admin\Hr;

use App\ApiBaseMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Hr\staffRequest;
use App\InfixModuleManager;
use App\Models\SmCustomField;
use App\Scopes\ActiveStatusSchoolScope;
use App\SmBaseSetup;
use App\SmDesignation;
use App\SmGeneralSettings;
use App\SmHrPayrollGenerate;
use App\SmHumanDepartment;
use App\SmLeaveRequest;
use App\SmStaff;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmUserLog;
use App\Traits\CustomFields;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\MultiBranch\Entities\Branch;
use Modules\RolePermission\Entities\InfixRole;

class SmStaffController extends Controller
{
    use CustomFields;

    public function __construct()
    {
        $this->middleware('PM');

        $this->User = json_encode(User::find(1));
        $this->SmGeneralSettings = json_encode(generalSetting());
        $this->SmUserLog = json_encode(SmUserLog::find(1));
        $this->InfixModuleManager = json_encode(InfixModuleManager::find(1));
        $this->URL = url('/');
    }

    public function staffList(Request $request)
    {

        try {

            $all_staffs = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class);

            if (Auth::user()->role_id != 1) {
                $all_staffs->whereNotIn('role_id', [1, 5]);
            }
            $all_staffs = $all_staffs->with('departments', 'designations', 'roles')->where('school_id', Auth::user()->school_id)->get();

            $roles = InfixRole::query();
            if (Auth::user()->role_id != 1) {
                $roles->whereNotIn('id', [1, 2, 3, 5]);
            }

            $roles = $roles->where('is_saas', 0)
                ->where('active_status', '=', '1')
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->orderBy('name', 'asc')
                ->get();

            return view('backEnd.humanResource.staff_list', compact('all_staffs', 'roles'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function roleStaffList(Request $request, $role_id)
    {

        try {
            $staffs_api = DB::table('sm_staffs')
                ->where('is_saas', 0)
                ->where('sm_staffs.active_status', 1)
                ->where('role_id', '=', $role_id)
                ->join('roles', 'sm_staffs.role_id', '=', 'roles.id')
                ->join('sm_human_departments', 'sm_staffs.department_id', '=', 'sm_human_departments.id')
                ->join('sm_designations', 'sm_staffs.designation_id', '=', 'sm_designations.id')
                ->join('sm_base_setups', 'sm_staffs.gender_id', '=', 'sm_base_setups.id')
                ->where('sm_staffs.school_id', Auth::user()->school_id)
                ->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                return ApiBaseMethod::sendResponse($staffs_api, null);
            }
            if (moduleStatusCheck('MultiBranch')) {
                $branches = Branch::where('active_status', 1)->get();
                return view('backEnd.humanResource.staff_list', compact('staffs', 'roles', 'branches'));
            } else {
                return view('backEnd.humanResource.staff_list', compact('staffs', 'roles'));
            }

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addStaff()
    {

        try {
            $max_staff_no = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->where('is_saas', 0)
                ->where('school_id', Auth::user()->school_id)
                ->max('staff_no');

            $roles = InfixRole::where('is_saas', 0)->where('active_status', '=', 1)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->whereNotIn('id', [1, 2, 3])
                ->orderBy('name', 'asc')
                ->get();

            $departments = SmHumanDepartment::where('is_saas', 0)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            $designations = SmDesignation::where('is_saas', 0)
                ->orderBy('title', 'asc')
                ->get(['id', 'title']);

            $marital_ststus = SmBaseSetup::where('base_group_id', '=', '4')
                ->orderBy('base_setup_name', 'asc')
                ->where('school_id', auth()->user()->school_id)
                ->get(['id', 'base_setup_name']);

            $genders = SmBaseSetup::where('base_group_id', '=', '1')
                ->orderBy('base_setup_name', 'asc')
                ->where('school_id', auth()->user()->school_id)
                ->get(['id', 'base_setup_name']);

            $custom_fields = SmCustomField::where('form_name', 'staff_registration')->get();

            return view('backEnd.humanResource.addStaff', compact('roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders', 'custom_fields'));
        } catch (\Exception $e) {
            return $e->getMessage();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function staffPicStore(Request $r)
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

    public function staffStore(staffRequest $request)
    {
        // custom field validation start
        $validator = Validator::make($request->all(), $this->generateValidateRules("staff_registration"));
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                Toastr::error(str_replace('custom f.', '', $error), 'Failed');
            }
            return redirect()->back()->withInput();
        }

        try {
            DB::beginTransaction();
            try {
                $designation = 'public/uploads/resume/';

                $user = new User();
                $user->role_id = $request->role_id;
                $user->username = $request->email;
                $user->email = $request->email;
                $user->full_name = $request->first_name . ' ' . $request->last_name;
                $user->password = Hash::make(123456);
                $user->school_id = Auth::user()->school_id;
                $user->save();
                $user->toArray();

                $basic_salary = !empty($request->basic_salary) ? $request->basic_salary : 0;

                $staff = new SmStaff();
                $staff->staff_no = $request->staff_no;
                $staff->role_id = $request->role_id;
                $staff->department_id = $request->department_id;
                $staff->designation_id = $request->designation_id;

                if (moduleStatusCheck('MultiBranch')) {
                    if (Auth::user()->is_administrator == 'yes') {
                        $staff->branch_id = $request->branch_id;
                    } else {
                        $staff->branch_id = Auth::user()->branch_id;
                    }
                }

                $staff->first_name = $request->first_name;
                $staff->last_name = $request->last_name;
                $staff->full_name = $request->first_name . ' ' . $request->last_name;
                $staff->fathers_name = $request->fathers_name;
                $staff->mothers_name = $request->mothers_name;
                $staff->email = $request->email;
                $staff->school_id = Auth::user()->school_id;
                $staff->staff_photo = fileUpload($request->staff_photo, $designation);
                $staff->gender_id = $request->gender_id;
                $staff->marital_status = $request->marital_status;
                $staff->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
                $staff->date_of_joining = date('Y-m-d', strtotime($request->date_of_joining));
                $staff->mobile = $request->mobile ?? null;
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
                $staff->user_id = $user->id;
                $staff->resume = fileUpload($request->resume, $designation);
                $staff->joining_letter = fileUpload($request->joining_letter, $designation);
                $staff->other_document = fileUpload($request->other_document, $designation);
                $staff->driving_license = $request->driving_license;

                //Custom Field Start
                if ($request->customF) {
                    $dataImage = $request->customF;
                    foreach ($dataImage as $label => $field) {
                        if (is_object($field) && $field != "") {
                            $dataImage[$label] = fileUpload($field, 'public/uploads/customFields/');
                        }
                    }
                    $staff->custom_field_form_name = "staff_registration";
                    $staff->custom_field = json_encode($dataImage, true);
                }
                //Custom Field End
                $results = $staff->save();
                $staff->toArray();
                DB::commit();
                $user_info = [];
                if ($request->email != "") {
                    $user_info[] = array('email' => $request->email, 'id' => $staff->id, 'slug' => 'staff');
                }
                try {
                    if (count($user_info) != 0) {
                        $compact['user_email'] = $request->email;
                        $compact['id'] = $staff->id;
                        $compact['slug'] = 'staff';
                        @send_mail($request->email, $staff->full_name, "staff_login_credentials", $compact);
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

            Toastr::success('Operation successful', 'Success');
            return redirect('staff-directory');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function editStaff($id)
    {

        try {
            $editData = SmStaff::find($id);
            $max_staff_no = SmStaff::where('is_saas', 0)->where('school_id', Auth::user()->school_id)->max('staff_no');

            $roles = InfixRole::where('active_status', '=', 1)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->whereNotIn('id', [1, 2, 3])
                ->orderBy('id', 'desc')
                ->get();

            $departments = SmHumanDepartment::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $designations = SmDesignation::where('active_status', '=', '1')->where('school_id', Auth::user()->school_id)->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->where('school_id', auth()->user()->school_id)->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->where('school_id', auth()->user()->school_id)->get();

            // Custom Field Start
            $custom_fields = SmCustomField::where('form_name', 'staff_registration')->where('school_id', Auth::user()->school_id)->get();
            $custom_filed_values = json_decode($editData->custom_field);
            $student = $editData;
            // Custom Field End
            return view('backEnd.humanResource.editStaff', compact('editData', 'roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders', 'custom_fields', 'custom_filed_values', 'student'));
        } catch (\Exception $e) {

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
                'field_name' => "required",
                'staff_photo' => "sometimes|nullable|mimes:jpg,jpeg,png",
            ]);
        }
        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
        }

        try {
            if (!empty($request->field_name)) {
                $request_string = $request->field_name;
                $request_id = $request->id;
                $data = SmStaff::find($request_id);
                $data->$request_string = $request->$request_string;
                if ($request_string == "first_name") {
                    $full_name = $request->$request_string . ' ' . $data->last_name;
                    $data->full_name = $full_name;
                } else if ($request_string == "last_name") {
                    $full_name = $data->first_name . ' ' . $request->$request_string;
                    $data->full_name = $full_name;
                } else if ($request_string == "staff_photo") {
                    $maxFileSize = SmGeneralSettings::first('file_size')->file_size;
                    $file = $request->file('staff_photo');
                    $fileSize = filesize($file);
                    $fileSizeKb = ($fileSize / 1000000);
                    if ($fileSizeKb >= $maxFileSize) {
                        Toastr::error('Max upload file size ' . $maxFileSize . ' Mb is set in system', 'Failed');
                        return redirect()->back();
                    }
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
                    $data['flag'] = true;
                    return ApiBaseMethod::sendResponse($data, null);
                }
            } else {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['message'] = 'Invalid Input';
                    $data['flag'] = false;
                    return ApiBaseMethod::sendError($data, null);
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function staffProfileUpdate(Request $r, $id)
    {

        $validator = Validator::make($r->all(), [
            'logo_pic' => 'sometimes|required|mimes:jpg,png|max:40000',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'error'], 201);
        }
        try {
            // $data = SmStaff::findOrFail($id);
            if (checkAdmin()) {
                $data = SmStaff::find($id);
            } else {
                $data = SmStaff::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
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
                    $data->staff_photo = $imageName;
                } else {
                    $name = md5($file->getClientOriginalName() . time()) . "." . "png";
                    if (file_exists($data->staff_photo)) {
                        File::delete($data->staff_photo);
                    }
                    $images->save('public/uploads/staff/' . $name);
                    $imageName = 'public/uploads/staff/' . $name;
                    $data->staff_photo = $imageName;
                }
                $data->save();
            }

            return response()->json('success', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'error'], 201);
        }
    }

    public function staffUpdate(StaffRequest $request)
    {

        // custom field validation start
        $validator = Validator::make($request->all(), $this->generateValidateRules("staff_registration"));
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                Toastr::error(str_replace('custom f.', '', $error), 'Failed');
            }
            return redirect()->back()->withInput();
        }
        // custom field validation End

        // custom field validation start
        $validator = Validator::make($request->all(), $this->generateValidateRules("staff_registration"));
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                Toastr::error(str_replace('custom f.', '', $error), 'Failed');
            }
            return redirect()->back()->withInput();
        }
        // custom field validation End

        try {
            $designation = 'public/uploads/resume/';

            $staff = SmStaff::find($request->staff_id);
            $basic_salary = !empty($request->basic_salary) ? $request->basic_salary : 0;
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
            $staff->staff_photo = fileUpdate($staff->staff_photo, $request->staff_photo, $designation);
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
            $staff->user_id = $staff->user_id;
            $staff->resume = fileUpdate($staff->resume, $request->resume, $designation);
            $staff->joining_letter = fileUpdate($staff->joining_letter, $request->joining_letter, $designation);
            $staff->other_document = fileUpdate($staff->other_document, $request->other_document, $designation);
            $staff->driving_license = $request->driving_license;

            //Custom Field Start
            if ($request->customF) {
                $dataImage = $request->customF;
                foreach ($dataImage as $label => $field) {
                    if (is_object($field) && $field != "") {
                        $dataImage[$label] = fileUpload($field, 'public/uploads/customFields/');
                    }
                }
                $staff->custom_field_form_name = "staff_registration";
                $staff->custom_field = json_encode($dataImage, true);
            }
            //Custom Field End

            $result = $staff->update();

            if ($result) {
                $user = User::find($staff->user_id);
                $user->username = $request->email;
                $user->email = $request->email;
                $user->role_id = $request->role_id;
                $user->full_name = $request->first_name . ' ' . $request->last_name;
                $user->update();

            }
            Toastr::success('Operation successful', 'Success');
            return redirect('staff-directory');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function staffRoles(Request $request)
    {

        try {
            $roles = InfixRole::where('is_saas', 0)
                ->where('active_status', '=', '1')
                ->select('id', 'name', 'type')
                ->where('id', '!=', 2)
                ->where('id', '!=', 3)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                return ApiBaseMethod::sendResponse($roles, null);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function viewStaff($id)
    {

        try {

            if (checkAdmin()) {
                $staffDetails = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->find($id);
            } else {
                $staffDetails = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->where('id', $id)->where('school_id', Auth::user()->school_id)->first();
            }
            if (Auth::user()->role_id != 1 && Auth::user()->staff->id != $id) {
                Toastr::error('You are not authorized to view this page', 'Failed');
                return redirect()->back();
            }

            if (!empty($staffDetails)) {
                $staffPayrollDetails = SmHrPayrollGenerate::where('staff_id', $id)->where('payroll_status', '!=', 'NG')->where('school_id', Auth::user()->school_id)->get();
                $staffLeaveDetails = SmLeaveRequest::where('staff_id', $staffDetails->user_id)->where('school_id', Auth::user()->school_id)->get();
                $staffDocumentsDetails = SmStudentDocument::where('student_staff_id', $id)->where('type', '=', 'stf')->where('school_id', Auth::user()->school_id)->get();
                $timelines = SmStudentTimeline::where('staff_student_id', $id)->where('type', '=', 'stf')->where('school_id', Auth::user()->school_id)->get();

                $custom_field_data = $staffDetails->custom_field;

                if (!is_null($custom_field_data)) {
                    $custom_field_values = json_decode($custom_field_data);
                } else {
                    $custom_field_values = null;
                }
                return view('backEnd.humanResource.viewStaff', compact('staffDetails', 'staffPayrollDetails', 'staffLeaveDetails', 'staffDocumentsDetails', 'timelines', 'custom_field_values'));
            } else {
                Toastr::error('Something went wrong, please try again', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchStaff(Request $request)
    {

        try {
            $staff = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class);
            $staff->where('is_saas', 0)->where('active_status', 1);
            if ($request->role_id != "") {
                $staff->where('role_id', $request->role_id);
            }
            if ($request->staff_no != "") {
                $staff->where('staff_no', $request->staff_no);
            }

            if ($request->staff_name != "") {
                $staff->where('full_name', 'like', '%' . $request->staff_name . '%');
            }

            if (Auth::user()->role_id != 1) {
                $staff->where('role_id', '!=', 1);
            }

            $all_staffs = $staff->where('school_id', Auth::user()->school_id)->get();

            if (Auth::user()->role_id != 1) {
                $roles = InfixRole::where('is_saas', 0)->where('active_status', '=', '1')->where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 3)->where('id', '!=', 5)->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })->get();
            } else {
                $roles = InfixRole::where('is_saas', 0)->where('active_status', '=', '1')->where('id', '!=', 2)->where('id', '!=', 3)->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })->get();
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['staffs'] = $all_staffs->toArray();
                $data['roles'] = $roles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.staff_list', compact('all_staffs', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function uploadStaffDocuments($staff_id)
    {

        try {
            return view('backEnd.humanResource.uploadStaffDocuments', compact('staff_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function saveUploadDocument(Request $request)
    {
        $request->validate([
            'staff_upload_document' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt",
        ]);
        try {
            if ($request->file('staff_upload_document') != "" && $request->title != "") {
                $document_photo = "";
                if ($request->file('staff_upload_document') != "") {
                    $maxFileSize = SmGeneralSettings::first('file_size')->file_size;
                    $file = $request->file('staff_upload_document');
                    $fileSize = filesize($file);
                    $fileSizeKb = ($fileSize / 1000000);
                    if ($fileSizeKb >= $maxFileSize) {
                        Toastr::error('Max upload file size ' . $maxFileSize . ' Mb is set in system', 'Failed');
                        return redirect()->back()->with(['staffDocuments' => 'active']);
                    }
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
                $document->academic_id = getAcademicId();
                $results = $document->save();
            }

            if ($results) {
                Toastr::success('Document uploaded successfully', 'Success');
                return redirect()->back()->with(['staffDocuments' => 'active']);
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back()->with(['staffDocuments' => 'active']);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->with(['staffDocuments' => 'active']);
        }
    }

    public function deleteStaffDocumentView(Request $request, $id)
    {

        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($id, null);
            }
            return view('backEnd.humanResource.deleteStaffDocumentView', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffDocument($id)
    {
        try {
            $result = SmStudentDocument::where('student_staff_id', $id)->first();
            if ($result) {

                if (file_exists($result->file)) {
                    File::delete($result->file);
                }
                $result->delete();
                Toastr::success('Operation successful', 'Success');
                return redirect()->back()->with(['staffDocuments' => 'active']);
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back()->with(['staffDocuments' => 'active']);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->with(['staffDocuments' => 'active']);
        }
    }

    public function addStaffTimeline($id)
    {
        try {
            return view('backEnd.humanResource.addStaffTimeline', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function storeStaffTimeline(Request $request)
    {

        $request->validate([
            'document_file_4' => "sometimes|nullable|mimes:pdf,doc,docx,jpg,jpeg,png,txt",
        ]);
        try {
            if ($request->title != "") {

                $document_photo = "";
                if ($request->file('document_file_4') != "") {
                    $maxFileSize = SmGeneralSettings::first('file_size')->file_size;
                    $file = $request->file('document_file_4');
                    $fileSize = filesize($file);
                    $fileSizeKb = ($fileSize / 1000000);
                    if ($fileSizeKb >= $maxFileSize) {
                        Toastr::error('Max upload file size ' . $maxFileSize . ' Mb is set in system', 'Failed');
                        return redirect()->back();
                    }
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
                $timeline->academic_id = getAcademicId();
                $timeline->save();
            }
            Toastr::success('Document uploaded successfully', 'Success');
            return redirect()->back()->with(['staffTimeline' => 'active']);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->with(['staffTimeline' => 'active']);
        }
    }

    public function deleteStaffTimelineView($id)
    {

        try {
            return view('backEnd.humanResource.deleteStaffTimelineView', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffTimeline($id)
    {

        try {
            $result = SmStudentTimeline::destroy($id);
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back()->with(['staffTimeline' => 'active']);
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back()->with(['staffTimeline' => 'active']);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->with(['staffTimeline' => 'active']);
        }
    }

    public function deleteStaffView($id)
    {

        try {
            return view('backEnd.humanResource.deleteStaffView', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaff($id)
    {

        try {
            $tables = \App\tableList::getTableList('staff_id', $id);
            $tables1 = \App\tableList::getTableList('driver_id', $id);

            if ($tables == null) {
                if (checkAdmin()) {
                    $staffs = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->find($id);
                } else {
                    $staffs = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->where('id', $id)->where('school_id', Auth::user()->school_id)->first();
                }
                $user_id = $staffs->user_id;
                $result = $staffs->delete();
                User::destroy($user_id);
                Toastr::success('Operation successful', 'Success');
                return redirect('staff-directory');
            } else {
                $msg = 'This data already used in  : ' . $tables . $tables1 . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function staffDisableEnable(Request $request)
    {
        // return $request;
        try {
            if ($request->status == 'on') {
                $status = 1;
            } else {
                $status = 0;
            }

            // $staff = SmStaff::find($request->id);
            if (checkAdmin()) {
                $staff = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->find($request->id);
            } else {
                $staff = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->where('id', $request->id)->where('school_id', Auth::user()->school_id)->first();
            }
            $staff->active_status = $status;
            $staff_id = $staff->user_id;
            $staff->save();

            $user = User::find($staff_id);

            $user->active_status = $status;

            $user->save();

            return response()->json($staff);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffDoc(Request $request)
    {

        try {
            $staff_detail = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)->where('id', $request->staff_id)->first();

            if ($request->doc_id == 1) {
                if ($staff_detail->joining_letter != "") {
                    unlink($staff_detail->joining_letter);
                }
                $staff_detail->joining_letter = null;
            } else if ($request->doc_id == 2) {
                if ($staff_detail->resume != "") {
                    unlink($staff_detail->resume);
                }
                $staff_detail->resume = null;
            } else if ($request->doc_id == 3) {
                if ($staff_detail->other_document != "") {
                    unlink($staff_detail->other_document);
                }
                $staff_detail->other_document = null;
            }
            $staff_detail->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back()->with(['staffDocuments' => 'active']);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->with(['staffDocuments' => 'active']);
        }
    }
}
