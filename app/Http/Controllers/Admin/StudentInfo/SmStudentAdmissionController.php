<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\Scopes\StatusAcademicSchoolScope;
use App\User;
use App\SmClass;
use App\SmRoute;
use App\SmStaff;
use App\SmParent;
use App\SmStudent;
use App\SmVehicle;
use App\SmExamType;
use App\SmBaseSetup;
use App\SmMarksGrade;
use App\ApiBaseMethod;
use App\SmAcademicYear;
use App\SmEmailSetting;
use App\SmExamSchedule;
use App\SmStudentGroup;
use App\SmDormitoryList;
use App\SmStudentCategory;
use App\Traits\CustomFields;
use Illuminate\Http\Request;
use App\Models\SmCustomField;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\StudentInfo\SmStudentAdmissionRequest;

class SmStudentAdmissionController extends Controller
{
    use CustomFields;

    public function __construct()
    {
        $this->middleware('PM');

    }
    public function index()
    {

        try {
            if (moduleStatusCheck('SaasSubscription') == true && moduleStatusCheck('Saas') == true) {

                $active_student = SmStudent::where('school_id', Auth::user()->school_id)->where('active_status', 1)->count();

                if (\Modules\SaasSubscription\Entities\SmPackagePlan::student_limit() <= $active_student) {

                    Toastr::error('Your student limit has been crossed.', 'Failed');
                    return redirect()->back();

                }
            }

            $data = $this->loadData();
            $data['max_admission_id'] = SmStudent::max('admission_no');
            $data['max_roll_id'] = SmStudent::where('school_id', Auth::user()->school_id)->max('roll_no');

            return view('backEnd.studentInformation.student_admission', $data);

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(SmStudentAdmissionRequest $request)
    {
        

        $validator = Validator::make($request->all(), $this->generateValidateRules("student_registration"));
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                Toastr::error(str_replace('custom f.', '', $error), 'Failed');
            }
            return redirect()->back()->withInput();
        }

        $destination = 'public/uploads/student/document/';
        $student_file_destination = 'public/uploads/student/';

        if ($request->relation == 'Father') {
            $guardians_photo = fileUpload($request->file('fathers_photo'), $student_file_destination);
        } elseif ($request->relation == 'Mother') {
            $guardians_photo = fileUpload($request->file('mothers_photo'), $student_file_destination);
        } elseif ($request->relation == 'Other') {
            $guardians_photo = fileUpload($request->file('guardians_photo'), $student_file_destination);
        }

        DB::beginTransaction();

        try {
            $academic_year = SmAcademicYear::find($request->session);

            $user_stu = new User();
            $user_stu->role_id = 2;
            $user_stu->full_name = $request->first_name . ' ' . $request->last_name;
            $user_stu->username = $request->admission_number;
            $user_stu->email = $request->email_address;
            $user_stu->password = Hash::make(123456);
            $user_stu->school_id = Auth::user()->school_id;
            $user_stu->created_at = $academic_year->year . '-01-01 12:00:00';
            $user_stu->save();
            $user_stu->toArray();

            if ($request->parent_id == "") {
                $user_parent = new User();
                $user_parent->role_id = 3;
                $user_parent->full_name = $request->fathers_name;
                if (!empty($request->guardians_email)) {
                    $data_parent['email'] = $request->guardians_email;
                    $user_parent->username = $request->guardians_email;
                }
                $user_parent->email = $request->guardians_email;
                $user_parent->password = Hash::make(123456);
                $user_parent->school_id = Auth::user()->school_id;
                $user_parent->created_at = $academic_year->year . '-01-01 12:00:00';
                $user_parent->save();
                $user_parent->toArray();

                $parent = new SmParent();
                $parent->user_id = $user_parent->id;
                $parent->fathers_name = $request->fathers_name;
                $parent->fathers_mobile = $request->fathers_phone;
                $parent->fathers_occupation = $request->fathers_occupation;
                $parent->fathers_photo = fileUpload($request->file('fathers_photo'), $student_file_destination);
                $parent->mothers_name = $request->mothers_name;
                $parent->mothers_mobile = $request->mothers_phone;
                $parent->mothers_occupation = $request->mothers_occupation;
                $parent->mothers_photo = fileUpload($request->file('mothers_photo'), $student_file_destination);
                $parent->guardians_name = $request->guardians_name;
                $parent->guardians_mobile = $request->guardians_phone;
                $parent->guardians_email = $request->guardians_email;
                $parent->guardians_occupation = $request->guardians_occupation;
                $parent->guardians_relation = $request->relation;
                $parent->relation = $request->relationButton;
                $parent->guardians_photo = $guardians_photo;
                $parent->guardians_address = $request->guardians_address;
                $parent->is_guardian = $request->is_guardian;
                $parent->school_id = Auth::user()->school_id;
                $parent->academic_id = $request->session;
                $parent->created_at = $academic_year->year . '-01-01 12:00:00';
                $parent->save();
                $parent->toArray();
            } else{
                $parent = SmParent::find($request->parent_id);
            }
            $student = new SmStudent();
            $student->class_id = $request->class;
            $student->section_id = $request->section;
            $student->session_id = $request->session;
            $student->user_id = $user_stu->id;
            $student->parent_id = $request->parent_id == "" ? $parent->id : $request->parent_id;
            $student->role_id = 2;
            $student->admission_no = $request->admission_number;
            $student->roll_no = $request->roll_number;
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->full_name = $request->first_name . ' ' . $request->last_name;
            $student->gender_id = $request->gender;
            $student->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
            $student->caste = $request->caste;
            $student->email = $request->email_address;
            $student->mobile = $request->phone_number;
            $student->admission_date = date('Y-m-d', strtotime($request->admission_date));
            $student->student_photo = fileUpload($request->photo, $student_file_destination);
            $student->bloodgroup_id = $request->blood_group;
            $student->religion_id = $request->religion;
            $student->height = $request->height;
            $student->weight = $request->weight;
            $student->current_address = $request->current_address;
            $student->permanent_address = $request->permanent_address;
            $student->route_list_id = $request->route;
            $student->dormitory_id = $request->dormitory_name;
            $student->room_id = $request->room_number;

            if (!empty($request->vehicle)) {
                $driver = SmVehicle::where('id', '=', $request->vehicle)
                    ->select('driver_id')
                    ->first();
                if (!empty($driver)) {
                    $student->vechile_id = $request->vehicle;
                    $student->driver_id = $driver->driver_id;
                }
            }

            $student->national_id_no = $request->national_id_number;
            $student->local_id_no = $request->local_id_number;
            $student->bank_account_no = $request->bank_account_number;
            $student->bank_name = $request->bank_name;
            $student->previous_school_details = $request->previous_school_details;
            $student->aditional_notes = $request->additional_notes;
            $student->ifsc_code = $request->ifsc_code;
            $student->document_title_1 = $request->document_title_1;
            $student->document_file_1 = fileUpload($request->file('document_file_1'), $destination);
            $student->document_title_2 = $request->document_title_2;
            $student->document_file_2 = fileUpload($request->file('document_file_2'), $destination);
            $student->document_title_3 = $request->document_title_3;
            $student->document_file_3 = fileUpload($request->file('document_file_3'), $destination);
            $student->document_title_4 = $request->document_title_4;
            $student->document_file_4 = fileUpload($request->file('document_file_4'), $destination);
            $student->school_id = Auth::user()->school_id;
            $student->academic_id = $request->session;
            $student->student_category_id = $request->student_category_id;
            $student->student_group_id = $request->student_group_id;
            $student->created_at = $academic_year->year . '-01-01 12:00:00';

            if ($request->customF) {
                $dataImage = $request->customF;
                foreach ($dataImage as $label => $field) {
                    if (is_object($field) && $field != "") {
                        $dataImage[$label] = fileUpload($field, 'public/uploads/customFields/');
                    }
                }

                //Custom Field Start
                $student->custom_field_form_name = "student_registration";
                $student->custom_field = json_encode($dataImage, true);
                //Custom Field End
            }
            //add by abu nayem for lead convert to student
            if (moduleStatusCheck('Lead')==true) {
                $student->lead_id = $request->lead_id;
            }
            //end lead convert to student

            $student->save();
            $student->toArray();

            if ($student) {
                $compact['user_email'] = $request->email_address;
                $compact['slug'] = 'student';
                $compact['id'] = $student->id;
                @send_mail($request->email_address, $request->first_name . ' ' . $request->last_name, "student_login_credentials", $compact);
                @send_sms($request->phone_number, 'student_admission', $compact);
            }

            if ($parent) {
                $compact['user_email'] = $parent->guardians_email;
                $compact['slug'] = 'parent';
                $compact['id'] = $parent->id;
                @send_mail($parent->guardians_email, $request->fathers_name, "parent_login_credentials", $compact);
                @send_sms($request->guardians_phone , 'student_admission_for_parent', $compact);
            }

            //add by abu nayem for lead convert to student
            if (moduleStatusCheck('Lead')==true && $request->lead_id) {
                $lead =\Modules\Lead\Entities\Lead::find($request->lead_id);
                $lead->class_id = $request->class;
                $lead->section_id = $request->section;
                $lead->save();
            }
            //end lead convert to student
            DB::commit();
            if (moduleStatusCheck('Lead')==true && $request->lead_id) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->route('lead.index');
            }else {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $data = $this->loadData();
            $data['student'] = SmStudent::with('sections')->select('sm_students.*')->find($id);
            $data['siblings'] = SmStudent::where('parent_id', $data['student']->parent_id)->get();
            $data['custom_filed_values'] = json_decode($data['student']->custom_field);

            return view('backEnd.studentInformation.student_edit', $data);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(SmStudentAdmissionRequest $request)
    {
        
        $student_detail = SmStudent::find($request->id);
        // custom field validation start
        $validator = Validator::make($request->all(), $this->generateValidateRules("student_registration", $student_detail));
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                Toastr::error(str_replace('custom f.', '', $error), 'Failed');
            }
            return redirect()->back()->withInput();
        }
        // custom field validation End


        $destination = 'public/uploads/student/document/';
        $student_file_destination = 'public/uploads/student/';
        $student = SmStudent::find($request->id);
        if ($request->relation == 'Father') {
            $guardians_photo = fileUpdate($student->parents->guardians_photo, $request->fathers_photo, $student_file_destination);

        } elseif ($request->relation == 'Mother') {
            $guardians_photo = fileUpdate($student->parents->guardians_photo, $request->mothers_photo, $student_file_destination);

        } elseif ($request->relation == 'Other') {
            $guardians_photo = fileUpdate($student->parents->guardians_photo, $request->guardians_photo, $student_file_destination);

        }

        DB::beginTransaction();
        try {
            $academic_year = SmAcademicYear::find($request->session);
            $user_stu = $this->add_user($student_detail->user_id, 2, $request->admission_number, $request->email_address);

            try {
                if (($request->sibling_id == 0 || $request->sibling_id == 1) && $request->parent_id == "") {

                    $user_parent = $this->add_user($student_detail->parents->user_id, 3, $request->guardians_email, $request->guardians_email);

                    $user_parent->toArray();

                } elseif ($request->sibling_id == 0 && $request->parent_id != "") {
                    User::destroy($student_detail->parents->user_id);
                } elseif (($request->sibling_id == 2 || $request->sibling_id == 1) && $request->parent_id != "") {
                } elseif ($request->sibling_id == 2 && $request->parent_id == "") {

                    $user_parent = $this->add_user(null, 3, $request->guardians_email, $request->guardians_email);
                    $user_parent->toArray();
                }
                try {
                    if ($request->sibling_id == 0 && $request->parent_id != "") {
                        SmParent::destroy($student_detail->parent_id);
                    } elseif (($request->sibling_id == 2 || $request->sibling_id == 1) && $request->parent_id != "") {
                    } else {

                        if (($request->sibling_id == 0 || $request->sibling_id == 1) && $request->parent_id == "") {
                            $parent = SmParent::find($student_detail->parent_id);
                        } elseif ($request->sibling_id == 2 && $request->parent_id == "") {
                            $parent = new SmParent();
                        }

                        $parent->user_id = $user_parent->id;
                        $parent->fathers_name = $request->fathers_name;
                        $parent->fathers_mobile = $request->fathers_phone;
                        $parent->fathers_occupation = $request->fathers_occupation;
                        $parent->fathers_photo = fileUpdate($parent->fathers_photo, $request->fathers_photo, $student_file_destination);
                        $parent->mothers_name = $request->mothers_name;
                        $parent->mothers_mobile = $request->mothers_phone;
                        $parent->mothers_occupation = $request->mothers_occupation;
                        $parent->mothers_photo = fileUpdate($parent->mothers_photo, $request->mothers_photo, $student_file_destination);
                        $parent->guardians_name = $request->guardians_name;
                        $parent->guardians_mobile = $request->guardians_phone;
                        $parent->guardians_email = $request->guardians_email;
                        $parent->guardians_occupation = $request->guardians_occupation;
                        $parent->guardians_relation = $request->relation;
                        $parent->relation = $request->relationButton;
                        $parent->guardians_photo = $guardians_photo;
                        $parent->guardians_address = $request->guardians_address;
                        $parent->is_guardian = $request->is_guardian;
                        $parent->created_at = $academic_year->year . '-01-01 12:00:00';
                        $parent->save();
                        $parent->toArray();
                    }

                    try {

                        $student = SmStudent::find($request->id);

                        if (($request->sibling_id == 0 || $request->sibling_id == 1) && $request->parent_id == "") {
                            $student->parent_id = $parent->id;
                        } elseif ($request->sibling_id == 0 && $request->parent_id != "") {
                            $student->parent_id = $request->parent_id;
                        } elseif (($request->sibling_id == 2 || $request->sibling_id == 1) && $request->parent_id != "") {
                            $student->parent_id = $request->parent_id;
                        } elseif ($request->sibling_id == 2 && $request->parent_id == "") {
                            $student->parent_id = $parent->id;
                        }
                        $student->class_id = $request->class;
                        $student->section_id = $request->section;
                        $student->session_id = $request->session;
                        $student->user_id = $user_stu->id;
                        $student->admission_no = $request->admission_number;
                        $student->roll_no = $request->roll_number;
                        $student->first_name = $request->first_name;
                        $student->last_name = $request->last_name;
                        $student->full_name = $request->first_name . ' ' . $request->last_name;
                        $student->gender_id = $request->gender;
                        $student->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));

                        $student->age = $request->age;

                        $student->caste = $request->caste;
                        $student->email = $request->email_address;
                        $student->mobile = $request->phone_number;
                        $student->admission_date = date('Y-m-d', strtotime($request->admission_date));
                        $student->student_photo = fileUpdate($parent->student_photo, $request->photo, $student_file_destination);
                        $student->bloodgroup_id = $request->blood_group;
                        $student->religion_id = $request->religion;
                        $student->height = $request->height;
                        $student->weight = $request->weight;
                        $student->current_address = $request->current_address;
                        $student->permanent_address = $request->permanent_address;
                        $student->student_category_id = $request->student_category_id;
                        $student->student_group_id = $request->student_group_id;
                        $student->route_list_id = $request->route;
                        $student->dormitory_id = $request->dormitory_name;
                        $student->room_id = $request->room_number;
                        if (!empty($request->vehicle)) {
                            $driver = SmVehicle::where('id', '=', $request->vehicle)
                                ->select('driver_id')
                                ->first();
                            $student->vechile_id = $request->vehicle;
                            $student->driver_id = $driver->driver_id;
                        }

                        $student->national_id_no = $request->national_id_number;
                        $student->local_id_no = $request->local_id_number;
                        $student->bank_account_no = $request->bank_account_number;
                        $student->bank_name = $request->bank_name;
                        $student->previous_school_details = $request->previous_school_details;
                        $student->aditional_notes = $request->additional_notes;
                        $student->ifsc_code = $request->ifsc_code;
                        $student->document_title_1 = $request->document_title_1;
                        $student->document_file_1 = fileUpdate($student->document_file_1, $request->file('document_file_1'), $destination);
                        $student->document_title_2 = $request->document_title_2;
                        $student->document_file_2 = fileUpdate($student->document_file_2, $request->file('document_file_2'), $destination);
                        $student->document_title_3 = $request->document_title_3;
                        $student->document_file_3 = fileUpdate($student->document_file_3, $request->file('document_file_3'), $destination);
                        $student->document_title_4 = $request->document_title_4;
                        $student->document_file_4 = fileUpdate($student->document_file_4, $request->file('document_file_4'), $destination);
                        $student->created_at = $academic_year->year . '-01-01 12:00:00';
                        $student->academic_id = $academic_year->id;

                        if ($request->customF) {
                            $dataImage = $request->customF;
                            foreach ($dataImage as $label => $field) {
                                if (is_object($field) && $field != "") {
                                    $key = "";

                                    $maxFileSize = generalSetting()->file_size;
                                    $file = $field;
                                    $fileSize = filesize($file);
                                    $fileSizeKb = ($fileSize / 1000000);
                                    if ($fileSizeKb >= $maxFileSize) {
                                        Toastr::error('Max upload file size ' . $maxFileSize . ' Mb is set in system', 'Failed');
                                        return redirect()->back();
                                    }
                                    $file = $field;
                                    $key = $file->getClientOriginalName();
                                    $file->move('public/uploads/customFields/', $key);
                                    $dataImage[$label] = 'public/uploads/customFields/' . $key;

                                }
                            }

                            //Custom Field Start
                            $student->custom_field_form_name = "student_registration";
                            $student->custom_field = json_encode($dataImage, true);
                            //Custom Field End

                        }

                        $student->save();
                        DB::commit();

                        Toastr::success('Operation successful', 'Success');
                        return redirect('student-list');
                    } catch (\Exception $e) {
                        DB::rollback();
                        Toastr::error('Operation Failed', 'Failed');
                        return redirect()->back();
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            } catch (\Exception $e) {
                DB::rollback();
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    private function add_user($user_id, $role_id, $username, $email)
    {
        try {

            $user = $user_id == null ? new User() : User::find($user_id);
            $user->role_id = $role_id;
            $user->username = $username;
            $user->email = $email;
            $user->save();
            return $user;

        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function view(Request $request, $id)
    {

        try {

            $student_detail = SmStudent::withoutGlobalScope(StatusAcademicSchoolScope::class)->find($id);

            $siblings = SmStudent::where('parent_id', $student_detail->parent_id)->where('id', '!=', $id)->status()->withoutGlobalScope(StatusAcademicSchoolScope::class)->get();

            $exams = SmExamSchedule::where('class_id', $student_detail->class_id)
                ->where('section_id', $student_detail->section_id)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $academic_year = SmAcademicYear::where('id', $student_detail->session_id)
                ->first();

            $grades = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $max_gpa = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->max('gpa');

            $fail_gpa = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->min('gpa');

            $fail_gpa_name = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->where('gpa', $fail_gpa)
                ->first();

            if (!empty($student_detail->vechile_id)) {
                $driver_id = SmVehicle::where('id', '=', $student_detail->vechile_id)->first();
                $driver_info = SmStaff::where('id', '=', $driver_id->driver_id)->first();
            } else {
                $driver_id = '';
                $driver_info = '';
            }

            $exam_terms = SmExamType::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $custom_field_data = $student_detail->custom_field;

            if (!is_null($custom_field_data)) {
                $custom_field_values = json_decode($custom_field_data);
            } else {
                $custom_field_values = null;
            }

           
            return view('backEnd.studentInformation.student_view', compact('student_detail', 'driver_info', 'exams', 'siblings', 'grades', 'academic_year', 'exam_terms', 'max_gpa', 'fail_gpa_name', 'custom_field_values'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentDetails(Request $request)
    {
        try {
            $classes = SmClass::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $students = SmStudent::where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $sessions = SmAcademicYear::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.studentInformation.student_details', compact('classes', 'sessions'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public static function loadData()
    {
        $data['classes'] = SmClass::get(['id', 'class_name']);
        $data['religions'] = SmBaseSetup::where('base_group_id', '=', '2')->get(['id', 'base_setup_name']);
        $data['blood_groups'] = SmBaseSetup::where('base_group_id', '=', '3')->get(['id', 'base_setup_name']);
        $data['genders'] = SmBaseSetup::where('base_group_id', '=', '1')->get(['id', 'base_setup_name']);
        $data['route_lists'] = SmRoute::get(['id', 'title']);
        $data['dormitory_lists'] = SmDormitoryList::get(['id', 'dormitory_name']);
        $data['categories'] = SmStudentCategory::get(['id', 'category_name']);
        $data['groups'] = SmStudentGroup::get(['id', 'group']);
        $data['sessions'] = SmAcademicYear::get(['id', 'year', 'title']);
        $data['driver_lists'] = SmStaff::where([['active_status', '=', '1'], ['role_id', 9]])->where('school_id', Auth::user()->school_id)->get();
        $data['custom_fields'] = SmCustomField::where('form_name', 'student_registration')->where('school_id', Auth::user()->school_id)->get();
        $data['vehicles'] = SmVehicle::get();

        return $data;
    }
}
