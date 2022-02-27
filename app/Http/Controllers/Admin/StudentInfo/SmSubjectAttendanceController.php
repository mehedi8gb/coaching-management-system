<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\SmSubject;
use App\SmBaseSetup;
use App\ApiBaseMethod;
use App\SmClassSection;
use App\SmAssignSubject;
use App\SmStudentCategory;
use App\SmStudentAttendance;
use App\SmSubjectAttendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendanceStoreRequest;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendancSearchRequest;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendanceSearchRequest;
use App\Http\Requests\Admin\StudentInfo\subjectAttendanceAverageReportSearchRequest;

class SmSubjectAttendanceController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function index(Request $request)
    {
        try{
              
                $classes = SmClass::get();            
                return view('backEnd.studentInformation.subject_attendance', compact('classes')); 
           
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(StudentSubjectWiseAttendancSearchRequest $request)
    {

        try{

            $input['attendance_date']= $request->attendance_date;
            $input['class']= $request->class;
            $input['subject']= $request->subject;
            $input['section']= $request->section;

            $classes = SmClass::get();
            $sections = SmClassSection::with('sectionName')->where('class_id', $input['class'])->get();
            $subjects = SmAssignSubject::with('subject')->where('class_id', $input['class'])->where('section_id', $input['section'])
                ->groupBy('subject_id')->get();

            $students = SmStudent::with('DateSubjectWiseAttendances')->where('class_id', $input['class'])->where('section_id', $input['section'])->get();

            if ($students->isEmpty()) {
                Toastr::error('No Result Found', 'Failed');
                return redirect('subject-wise-attendance');
            }

            $attendance_type= $students[0]['DateSubjectWiseAttendances'] != null  ? $students[0]['DateSubjectWiseAttendances']['attendance_type']:'';

            


            $search_info['class_name'] = SmClass::find($request->class)->class_name;
            $search_info['section_name'] = SmSection::find($request->section)->section_name;
            $search_info['subject_name'] = SmSubject::find($request->subject)->subject_name;
         

 
            if (generalSetting()->attendance_layout==1) {
                return view('backEnd.studentInformation.subject_attendance_list', compact('classes','subjects','sections','students', 'attendance_type', 'search_info', 'input'));
            } else {
                return view('backEnd.studentInformation.subject_attendance_list2', compact('classes','subjects','sections','students', 'attendance_type', 'search_info', 'input'));
            }

            
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function storeAttendance(StudentSubjectWiseAttendanceStoreRequest $request)
    {
       
        try {
            foreach ($request->id as $student) {
                $attendance = SmSubjectAttendance::where('student_id', $student)
                            ->where('subject_id', $request->subject)
                            ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                            ->where('academic_id', getAcademicId())
                            ->first();

                if ($attendance != "") 
                {
                    $attendance->delete(); 
                }

                $attendance = new SmSubjectAttendance();
                $attendance->attendance_type = $request->attendance[$student];
                $attendance->student_id = $student;
                $attendance->subject_id = $request->subject;
                $attendance->school_id = Auth::user()->school_id;
                $attendance->academic_id = getAcademicId();
                $attendance->notes = $request->note[$student];
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $r= $attendance->save();
            }
        if($r) {
            Toastr::success('Operation successful', 'Success');
            return redirect('subject-wise-attendance');
        }else{

                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
        }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function storeAttendanceSecond(Request $request)
    {
       
  
 
        try {

            $present_list=[];
            foreach ($request->status as $key => $std) {
                $present_list[]=$key;
            }

            foreach ($request->id as $key => $student_id) {
                # code...
                $attendance = SmSubjectAttendance::where('student_id', $student_id)
                ->where('subject_id', $request->subject_id)
                ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                ->where('academic_id', getAcademicId())
                ->first();
                if ($attendance != "")
                 { 
                    $attendance->delete(); 
                 }
                $attendance = new SmSubjectAttendance();
                $attendance->student_id = $student_id;
                $attendance->subject_id = $request->subject_id;
                $attendance->school_id = Auth::user()->school_id;
                $attendance->academic_id = getAcademicId();
                if (in_array($student_id,$present_list)) {
                    $attendance->attendance_type = 'P';
                 } else {
                    $attendance->attendance_type = 'A';
                 }

                $attendance->notes = $request->note[$student_id];
                $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                $r= $attendance->save();
            }
            return response()->json('success');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function subjectHolidayStore(Request $request)
    {     
        $students = SmStudent::where('class_id', $request->class_id)
        ->where('section_id', $request->section_id)
        ->where('active_status', 1)
        ->where('academic_id', getAcademicId())
        ->where('school_id', Auth::user()->school_id)
        ->get();

        if ($students->isEmpty()) {
            Toastr::error('No Result Found', 'Failed');
            return redirect('subject-wise-attendance');
        }
        if ($request->purpose == "mark") {
            foreach ($students as $student) {
                $attendance = SmSubjectAttendance::where('student_id', $student->id)
                    ->where('subject_id', $request->subject_id)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                    ->where('academic_id', getAcademicId())
                    ->first();
                if (!empty($attendance)) {
                    $attendance->delete();

                    $attendance = new SmSubjectAttendance();
                    $attendance->attendance_type= "H";
                    $attendance->notes= "Holiday";
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                    $attendance->student_id = $student->id;
                    $attendance->subject_id = $request->subject_id;
                    $attendance->academic_id = getAcademicId();
                    $attendance->school_id = Auth::user()->school_id;
                    $attendance->save();

                }else{
                    $attendance = new SmSubjectAttendance();
                    $attendance->attendance_type= "H";
                    $attendance->notes= "Holiday";
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                    $attendance->student_id = $student->id;
                    $attendance->subject_id = $request->subject_id;
                    $attendance->academic_id = getAcademicId();
                    $attendance->school_id = Auth::user()->school_id;
                    $attendance->save();
                }
            }
        }elseif($request->purpose == "unmark"){
            foreach ($students as $student) {
                $attendance = SmSubjectAttendance::where('student_id', $student->id)
                    ->where('subject_id', $request->subject_id)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                    ->where('academic_id', getAcademicId())
                    ->first();
                if (!empty($attendance)) {
                    $attendance->delete();
                }
            }
        } 
        Toastr::success('Operation successful', 'Success');
        return redirect('subject-wise-attendance');
    }

    public function subjectAttendanceReport(Request $request)
    {
        try{
            $classes = SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id',Auth::user()->school_id)
            ->get();

            $types = SmStudentCategory::where('school_id',Auth::user()->school_id)->get();

            $genders = SmBaseSetup::where('active_status', '=', '1')
            ->where('base_group_id', '=', '1')
            ->where('school_id',Auth::user()->school_id)
            ->get();

            return view('backEnd.studentInformation.subject_attendance_report_view', compact('classes', 'types', 'genders'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function subjectAttendanceReportSearch(StudentSubjectWiseAttendanceSearchRequest $request)
    {
      

        // if ($validator->fails()) {
        //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //             return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //         }
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        try{
            $year = $request->year;
            $month = $request->month;
            $class_id = $request->class;
            $section_id = $request->section;
            $assign_subjects = SmAssignSubject::where('class_id',$class_id)
                                ->where('section_id',$section_id)
                                ->first();

            $subject_id = $assign_subjects->subject_id;
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
            $classes = SmClass::where('active_status', 1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->get();

            $students = SmStudent::where('class_id', $request->class)
                        ->where('section_id', $request->section)
                        ->where('active_status', 1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->get();

            $attendances = [];

            foreach ($students as $student) {
                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $student->id)
                ->join('sm_students','sm_students.id','=','sm_subject_attendances.student_id')
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)
                ->get();

                if ($attendance) {
                    $attendances[] = $attendance;
                }
            }

            return view('backEnd.studentInformation.subject_attendance_report_view', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id','subject_id'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
            }
    }
    public function subjectAttendanceAverageReport(Request $request)

    {

        try{

            $classes = SmClass::get();

            $types = SmStudentCategory::withoutGlobalScope(AcademicSchoolScope::class)->where('school_id',Auth::user()->school_id)->get();

            $genders = SmBaseSetup::where('base_group_id', '=', '1')->get();

            return view('backEnd.studentInformation.subject_attendance_report_average_view', compact('classes', 'types', 'genders'));

        }catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }

    }
    public function subjectAttendanceAverageReportSearch(subjectAttendanceAverageReportSearchRequest $request)

    {



        // if ($validator->fails()) {

        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {

        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());

        //     }

        //     return redirect()->back()

        //         ->withErrors($validator)

        //         ->withInput();

        // }

        try{

            $year = $request->year;

            $month = $request->month;

            $class_id = $request->class;

            $section_id = $request->section;

            $assign_subjects=SmAssignSubject::where('class_id',$class_id)->where('section_id',$section_id)->first();

            if(!$assign_subjects){

                Toastr::error('No Subject Assign ', 'Failed');

                return redirect()->back();
            }
            $subject_id = $assign_subjects->subject_id;

            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

            $classes = SmClass::get();

            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

            $attendances = [];

            foreach ($students as $student) {

                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $student->id)

                ->join('sm_students','sm_students.id','=','sm_subject_attendances.student_id')

                // ->where('subject_id', $subject_id)

                ->where('attendance_date', 'like', $year . '-' . $month . '%')

                ->where('sm_subject_attendances.academic_id', getAcademicId())

                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)

                ->get();

                if ($attendance) {

                    $attendances[] = $attendance;

                }

            }

            // return $attendances;
            return view('backEnd.studentInformation.subject_attendance_report_average_view', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id','subject_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }

    }


    public function studentAttendanceReportPrint($class_id, $section_id, $month, $year)
    {
        try{
            $current_day = date('d');
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $classes = SmClass::where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            $students = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)->where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();

            $attendances = [];
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', 'like', $year . '-' . $month . '%')->where('school_id',Auth::user()->school_id)->get();
                if (count($attendance) != 0) {
                    $attendances[] = $attendance;
                }
            }

            return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function subjectAttendanceReportAveragePrint($class_id, $section_id, $month, $year){
            set_time_limit(2700);
        try{
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $students = SmStudent::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id',Auth::user()->school_id)
            ->get();

            $attendances = [];

            foreach ($students as $student) {
                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $student->id)
                ->join('sm_students','sm_students.id','=','sm_subject_attendances.student_id')
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)
                ->get();

                if ($attendance) {
                    $attendances[] = $attendance;
                }
            }

        return view('backEnd.studentInformation.student_subject_attendance',compact('attendances','days' , 'year'  , 'month','class_id'  ,'section_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function subjectAttendanceReportPrint($class_id, $section_id, $month, $year){
             set_time_limit(2700);
        try{
            $current_day = date('d');

            $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $students = SmStudent::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id',Auth::user()->school_id)
            ->get();

            $attendances = [];

            foreach ($students as $student) {
                $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $student->id)
                ->join('sm_students','sm_students.id','=','sm_subject_attendances.student_id')
                ->where('attendance_date', 'like', $year . '-' . $month . '%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id',Auth::user()->school_id)
                ->get();

                if ($attendance) {
                    $attendances[] = $attendance;
                }
            }

        return view('backEnd.studentInformation.student_subject_attendance',compact('attendances','days' , 'year'  , 'month','class_id'  ,'section_id'));

        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}