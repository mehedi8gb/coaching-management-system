<?php

namespace App\Http\Controllers\Admin\Examination;

use App\SmClass;
use App\SmStaff;
use App\SmSection;
use App\SmStudent;
use App\SmSubject;
use App\YearCheck;
use App\SmExamType;
use App\SmExamSchedule;
use App\SmAssignSubject;
use App\SmExamAttendance;
use Illuminate\Http\Request;
use App\SmExamAttendanceChild;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Examination\SmExamAttendanceSearchRequest;

class SmExamAttendanceController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
	}
    
    public function examAttendanceCreate()
    {
        try{
            $exams = SmExamType::get();

            if (teacherAccess()) {
                $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                $classes=$teacher_info->classes;
            } else {
                $classes = SmClass::get();
            }
            $subjects = SmSubject::get();
            return view('backEnd.examination.exam_attendance_create', compact('exams', 'classes', 'subjects'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function examAttendanceSearch(SmExamAttendanceSearchRequest $request)
    {
      try{
        $exam_schedules = SmExamSchedule::query();
        if($request->class !=null){
                $exam_schedules-> where('class_id', $request->class);
        }
        
        if($request->section !=null){
                $exam_schedules->where('section_id', $request->section);
        }
                            
        $exam_schedules=$exam_schedules->where('exam_term_id', $request->exam)
                        ->where('subject_id', $request->subject)
                        ->count();

        if ($exam_schedules == 0) {
            Toastr::error('You have to create exam schedule first', 'Failed');
            return redirect('exam-attendance-create');
        }
        $students = SmStudent::query()->with('class','section');

        if($request->class !=null){
            $students ->where('class_id', $request->class);
        }

        if($request->section !=null){
            $students->where('section_id', $request->section);
        }
            
        $students = $students->get();
        
        if ($students->count() == 0) {
            Toastr::error('No Record Found', 'Failed');
            return redirect('exam-attendance-create');
        }

        $exam_attendance = SmExamAttendance::query();
        if($request->class !=null){ 
            $exam_attendance->where('class_id', $request->class);
        }

        if($request->section !=null){ 
            $exam_attendance->where('section_id', $request->section);
        }

        if($request->subject !=null){ 
            $exam_attendance->where('subject_id', $request->subject);
        }
        $exam_attendance =  $exam_attendance->where('exam_id', $request->exam)->first();
        $exam_attendance_childs =$exam_attendance != "" ? $exam_attendance->examAttendanceChild: [];

        if (teacherAccess()) {
            $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
            $classes=$teacher_info->classes;
        } else {
            $classes = SmClass::get();
        }

        $exams    = SmExamType::get();
        $subjects = SmSubject::get();
        $exam_id  = $request->exam;
        $subject_id = $request->subject;
        $class_id = $request->class;
        $section_id =$request->section !=null ? $request->section : null;
        
        $subject_info = SmSubject::find($request->subject);            
        $search_info['class_name'] = SmClass::find($request->class)->class_name;
        $search_info['section_name'] =  $section_id==null ? 'All Sections' : SmSection::find($request->section)->section_name;
        $search_info['subject_name'] =  SmSubject::find($request->subject)->subject_name;
        return view('backEnd.examination.exam_attendance_create', compact('exams', 'classes', 'subjects', 'students', 'exam_id', 'subject_id', 'class_id', 'section_id', 'exam_attendance_childs','search_info'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function examAttendanceStore(Request $request)
    {
        try {
            $alreday_assigned  = SmExamAttendance::query();
            if($request->class_id !=null){ 
                $alreday_assigned ->where('class_id', $request->class_id);
            }
            if($request->section_id !=''){ 
                $alreday_assigned->where('section_id', $request->section_id);
            }
            if($request->subject_id !=null){ 
                $alreday_assigned->where('subject_id', $request->subject_id);
            }
            $alreday_assigned=$alreday_assigned->where('exam_id', $request->exam_id)->first();

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            if($request->section_id !=''){
                if ($alreday_assigned == "") {
                    $exam_attendance = new SmExamAttendance();
                } else {
                    $exam_attendance = SmExamAttendance::where('class_id', $request->class_id)
                                        ->where('section_id', $request->section_id)
                                        ->where('subject_id', $request->subject_id)
                                        ->where('exam_id', $request->exam_id)
                                        ->first();
                }

                $exam_attendance->exam_id = $request->exam_id;
                $exam_attendance->subject_id = $request->subject_id;
                $exam_attendance->class_id = $request->class_id;
                $exam_attendance->section_id = $request->section_id;
                $exam_attendance->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                $exam_attendance->school_id = Auth::user()->school_id;
                $exam_attendance->academic_id = getAcademicId();
                $exam_attendance->save();
                $exam_attendance->toArray();

                if ($alreday_assigned != "") {
                    SmExamAttendanceChild::where('exam_attendance_id', $exam_attendance->id)->delete();
                }

                foreach ($request->id as $student) {    
                    $exam_attendance_child = new SmExamAttendanceChild();
                    $exam_attendance_child->exam_attendance_id = $exam_attendance->id;
                    $exam_attendance_child->student_id = $student;
                    $exam_attendance_child->attendance_type = $request->attendance[$student];
                    $exam_attendance_child->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                    $exam_attendance_child->school_id = Auth::user()->school_id;
                    $exam_attendance_child->academic_id = getAcademicId();
                    $exam_attendance_child->save();
                }
            }else{
                $classSections= SmAssignSubject::where('class_id', $request->class_id)
                                ->where('subject_id', $request->subject_id)
                                ->groupBy(['section_id','subject_id'])
                                ->get();

                foreach($classSections as $section){        
                    if ($alreday_assigned == "") {                      
                        $exam_attendance = new SmExamAttendance();
                    } else {
                        $exam_attendance = SmExamAttendance::where('class_id', $request->class_id)
                                        ->where('section_id', $section->section_id)
                                        ->where('subject_id', $request->subject_id)
                                        ->where('exam_id', $request->exam_id)
                                        ->first();
                    }

                    $exam_attendance->exam_id = $request->exam_id;
                    $exam_attendance->subject_id = $request->subject_id;
                    $exam_attendance->class_id = $request->class_id;
                    $exam_attendance->section_id = $section->section_id;
                    $exam_attendance->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                    $exam_attendance->school_id = Auth::user()->school_id;
                    $exam_attendance->academic_id = getAcademicId();
                    $exam_attendance->save();
                    $exam_attendance->toArray();

                    if ($alreday_assigned != "") {
                        SmExamAttendanceChild::where('exam_attendance_id', $exam_attendance->id)->delete();
                    }

                    foreach ($request->id as $student) {    
                        $exam_attendance_child = new SmExamAttendanceChild();
                        $exam_attendance_child->exam_attendance_id = $exam_attendance->id;
                        $exam_attendance_child->student_id = $student;
                        $exam_attendance_child->attendance_type = $request->attendance[$student];
                        $exam_attendance_child->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                        $exam_attendance_child->school_id = Auth::user()->school_id;
                        $exam_attendance_child->academic_id = getAcademicId();
                        $exam_attendance_child->save();
                    }
                }
            }
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('exam-attendance-create');
            } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
