<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\SmClass;
use App\SmStaff;
use App\SmSection;
use App\SmStudent;
use App\ApiBaseMethod;
use App\SmClassSection;
use App\SmStudentAttendance;
use Illuminate\Http\Request;
use App\StudentAttendanceBulk;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentAttendanceImport;
use App\Http\Requests\Admin\StudentInfo\SmStudentAttendanceSearchRequest;

class SmStudentAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
  
    }

    public function index(Request $request)
    {
        try {

          
            if (teacherAccess()) {
                 $teacher_info=SmStaff::where('user_id',auth()->user()->id)->first();
                 $classes = $teacher_info->classes;
            } else {
                 $classes = SmClass::get();
            }

            return view('backEnd.studentInformation.student_attendance', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

  
    public function studentSearch(SmStudentAttendanceSearchRequest $request)
    {
       

        try {
            $date = $request->attendance_date;
            if (teacherAccess()) {
                $teacher_info=SmStaff::where('user_id',auth()->user()->id)->first();
                $classes = $teacher_info->classes;
            } else {
                $classes = SmClass::get();
            }
            $students = SmStudent::with('DateWiseAttendances')->where('class_id', $request->class)->where('section_id', $request->section)
            ->get(['id','admission_no','first_name','last_name','roll_no']);

            if ($students->isEmpty()) {
                Toastr::error('No Result Found', 'Failed');
                return redirect('student-attendance');
            }

       
            $attendance_type= $students[0]['DateWiseAttendances'] != null  ? $students[0]['DateWiseAttendances']['attendance_type']:'';
        
            $class_id = $request->class;
            $section_id = $request->section;
            $search_info['class_name'] = SmClass::find($request->class)->class_name;
            $search_info['section_name'] =  SmSection::find($request->section)->section_name;
            $search_info['date'] = $request->attendance_date;
            $sections = SmClassSection::with('sectionName')->where('class_id', $request->class)->get();

            return view('backEnd.studentInformation.student_attendance', compact('classes','sections','class_id','section_id','date','students', 'attendance_type', 'search_info'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function studentAttendanceStore(Request $request)
    {

    
        try {
            foreach ($request->id as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student)->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                    ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->first();

                if ($attendance) {
                    $attendance->delete();
                }


                $attendance = new SmStudentAttendance();
                $attendance->student_id = $student;
                if (isset($request->mark_holiday)) {
                    $attendance->attendance_type = "H";
                } else {
                    $attendance->attendance_type = $request->attendance[$student];
                    $attendance->notes = $request->note[$student];
                }
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $attendance->school_id = Auth::user()->school_id;
                $attendance->academic_id = getAcademicId();
                $attendance->save();
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
            }
            Toastr::success('Operation successful', 'Success');
            return redirect('student-attendance');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function studentAttendanceHoliday(Request $request)
    {
        $students = SmStudent::where('class_id', $request->class_id)->where('section_id', $request->section_id)
               ->get();

                if ($students->isEmpty()) {
                    Toastr::error('No Result Found', 'Failed');
                    return redirect('student-attendance');
                }
                foreach ($students as $student) {

                    $attendance = SmStudentAttendance::where('student_id', $student->id)
                        ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))                        
                        ->first();
                        if (!empty($attendance)) {
                            $attendance->delete();
                        }
                        if($request->purpose == "mark"){
                            $attendance = new SmStudentAttendance();
                            $attendance->attendance_type= "H";
                            $attendance->notes= "Holiday";
                            $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                            $attendance->student_id = $student->id;
                            $attendance->academic_id = getAcademicId();
                            $attendance->school_id = Auth::user()->school_id;
                            $attendance->save();
                        }
                    
                }
      
            
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
    }

    public function studentAttendanceImport()
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            return view('backEnd.studentInformation.student_attendance_import', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function downloadStudentAtendanceFile()
    {

        try {
            $studentsArray = ['admission_no', 'class_id', 'section_id', 'attendance_date', 'in_time', 'out_time'];

            return Excel::create('student_attendance_sheet', function ($excel) use ($studentsArray) {
                $excel->sheet('student_attendance_sheet', function ($sheet) use ($studentsArray) {
                    $sheet->fromArray($studentsArray);
                });
            })->download('xlsx');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function studentAttendanceBulkStore(Request $request)
    {
        
      
            try {
           

                Excel::import(new StudentAttendanceImport($request->class,$request->section), $request->file('file'), 's3', \Maatwebsite\Excel\Excel::XLSX);
                $data = StudentAttendanceBulk::get();

                if (!empty($data)) {
                    $class_sections = [];
                    foreach ($data as $key => $value) {
                        if (date('d/m/Y', strtotime($request->attendance_date)) == date('d/m/Y', strtotime($value->attendance_date))) {
                            $class_sections[] = $value->class_id . '-' . $value->section_id;
                        }
                    }
                    DB::beginTransaction();


                    $all_student_ids = [];
                    $present_students = [];
                    foreach (array_unique($class_sections) as $value) {

                        $class_section = explode('-', $value);
                        $students = SmStudent::where('class_id', $class_section[0])->where('section_id', $class_section[1])->where('school_id', Auth::user()->school_id)->get();

                        foreach ($students as $student) {
                            StudentAttendanceBulk::where('student_id', $student->id)->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                                ->delete();
                            $all_student_ids[] = $student->id;
                        }

                    }


                    try {
                        foreach ($data as $key => $value) {
                            if ($value != "") {

                                if (date('d/m/Y', strtotime($request->attendance_date)) == date('d/m/Y', strtotime($value->attendance_date))) {
                                    $student = SmStudent::select('id')->where('id', $value->student_id)->where('school_id', Auth::user()->school_id)->first();


                                    // return $student;

                                    if ($student != "") {
                                        // SmStudentAttendance
                                        $attendance_check = SmStudentAttendance::where('student_id', $student->id)
                                            ->where('attendance_date', date('Y-m-d', strtotime($value->attendance_date)))->first();
                                        if ($attendance_check) {
                                            $attendance_check->delete();
                                        }
                                        $present_students[] = $student->id;
                                        $import = new SmStudentAttendance();
                                        $import->student_id = $student->id;
                                        $import->attendance_date = date('Y-m-d', strtotime($value->attendance_date));
                                        $import->attendance_type = $value->attendance_type;
                                        $import->notes = $value->note;
                                        $import->school_id = Auth::user()->school_id;
                                        $import->academic_id = getAcademicId();
                                        $import->save();
                                    }
                                } else {
                                    // Toastr::error('Attendance Date not Matched', 'Failed');
                                   StudentAttendanceBulk::where('student_id', $value->student_id)->delete();
                                }

                            }

                        }

                    } catch (\Exception $e) {
                        DB::rollback();
                        Toastr::error('Operation Failed', 'Failed');
                        return redirect()->back();
                    }
                    DB::commit();
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        
    }
}