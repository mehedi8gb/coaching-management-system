<?php

namespace App\Http\Controllers\Admin\Report;
use App\SmExam;
use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\YearCheck;
use App\SmExamType;
use App\SmExamSetup;
use App\SmMarkStore;
use App\SmMarksGrade;
use App\ApiBaseMethod;
use App\SmResultStore;
use App\SmAssignSubject;
use App\CustomResultSetting;
use Illuminate\Http\Request;
use App\SmClassOptionalSubject;
use App\SmOptionalSubjectAssign;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmReportController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
        // User::checkAuth();
	}

    public function tabulationSheetReport(Request $request)
    {
        try{
            $exam_types = SmExamType::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['exam_types'] = $exam_types->toArray();
                $data['classes'] = $classes->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.reports.tabulation_sheet_report', compact('exam_types', 'classes'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function tabulationSheetReportSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'exam' => 'required',
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
        try{
            if(!$request->student){
                $allClass = 0;
                $exam_term_id   = $request->exam;
                $class_id       = $request->class;
                $section_id     = $request->section;

                $exam_types = SmExamType::where('active_status', 1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();

                $classes = SmClass::where('active_status', 1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();


                $marks = SmMarkStore::where([
                    ['exam_term_id', $exam_term_id],
                    ['class_id', $class_id],
                    ['section_id', $section_id],
                ])->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->get();

                $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                    ->where('active_status', 1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->orderBy('gpa','desc')
                    ->get()
                    ->toArray();
                    $single_exam_term = SmExamType::find($request->exam);
                    $className = SmClass::find($request->class);
                    $sectionName = SmSection::find($request->section);

                    $tabulation_details['exam_term'] = $single_exam_term->title;
                    $tabulation_details['class'] = $className->class_name;
                    $tabulation_details['section'] = $sectionName->section_name;
                    $tabulation_details['grade_chart'] = $grade_chart;
                    $year=YearCheck::getYear();

                    $examSubjects = SmExam::where([['exam_type_id', $exam_term_id], ['section_id', $section_id], ['class_id', $class_id]])
                                ->where('school_id',Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();

                    $examSubjectIds = [];
                    foreach($examSubjects as $examSubject){
                        $examSubjectIds[] = $examSubject->subject_id;
                    }
                    
                    $subjects = SmAssignSubject::where([
                        ['class_id', $request->class],
                        ['section_id', $request->section]
                    ])->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->whereIn('subject_id',$examSubjectIds)
                    ->get();

                    $optional_subject_setup = SmClassOptionalSubject::where('class_id','=',$request->class)->first();

                    $students   = SmStudent::where('class_id', $request->class)
                                ->where('section_id',$section_id)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->get();

                    $max_grade = SmMarksGrade::where('active_status',1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->max('gpa');

                    $fail_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->min('gpa');

                    $fail_grade_name = SmMarksGrade::where('active_status',1)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->where('gpa',$fail_grade)
                                ->first();


                    
                return view('backEnd.reports.tabulation_sheet_report',compact('allClass',
                'exam_types',
                'classes',
                'marks',
                'tabulation_details',
                'year',
                'subjects',
                'optional_subject_setup',
                'exam_term_id',
                'class_id',
                'section_id',
                'students',
                'max_grade',
                'fail_grade_name'
            ));

            }else{
                $exam_term_id   = $request->exam;
                $class_id       = $request->class;
                $section_id     = $request->section;
                $student_id     = $request->student;

                $optional_subject_setup=SmClassOptionalSubject::where('class_id','=',$request->class)->first();

                $fail_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->min('gpa');

                $fail_grade_name = SmMarksGrade::where('active_status',1)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->where('gpa',$fail_grade)
                                ->first();

                $max_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->max('gpa');
                
                
                $examSubjects = SmExam::where([['exam_type_id', $exam_term_id], ['section_id', $section_id], ['class_id', $class_id]])
                                ->where('school_id',Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();

                $examSubjectIds = [];
                foreach($examSubjects as $examSubject){
                    $examSubjectIds[] = $examSubject->subject_id;
                }


                $student_detail = $studentDetails = SmStudent::find($request->student);

                $subjects = $studentDetails->class->subjects->whereIn('subject_id',$examSubjectIds)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id);
                            
                $year=YearCheck::getYear();

                $optional_subject_mark='';

                $get_optional_subject=SmOptionalSubjectAssign::where('student_id','=',$student_detail->id)
                                    ->where('session_id','=',$student_detail->session_id)
                                    ->first();

                if ($get_optional_subject!='') {
                    $optional_subject_mark=$get_optional_subject->subject_id;
                }

                $mark_sheet = SmResultStore::where([['class_id', $request->class], ['exam_type_id', $request->exam], ['section_id', $request->section], ['student_id', $request->student]])
                            ->whereIn('subject_id', $subjects->pluck('subject_id')
                            ->toArray())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();

                if ($request->student == "") {
                    $eligible_subjects       = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
                    $eligible_students       = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
                    foreach ($eligible_students as $SingleStudent) {
                        foreach ($eligible_subjects as $subject) {
                            $getMark            =  SmResultStore::where([
                                ['exam_type_id',   $exam_term_id],
                                ['class_id',       $class_id],
                                ['section_id',     $section_id],
                                ['student_id',     $SingleStudent->id],
                                ['subject_id',     $subject->subject_id]
                            ])->first();
                            if ($getMark == "") {
                                Toastr::error('Please register marks for all students.!', 'Failed');
                                return redirect()->back();
                                // return redirect()->back()->with('message-danger', 'Please register marks for all students.!');
                            }
                        }
                    }
                } else {
                    $eligible_subjects = SmAssignSubject::where('class_id', $class_id)
                                        ->where('section_id', $section_id)
                                        ->where('academic_id', getAcademicId())
                                        ->where('school_id',Auth::user()->school_id)
                                        ->get();

                    foreach ($eligible_subjects as $subject) {


                        $getMark            =  SmResultStore::where([
                            ['exam_type_id',   $exam_term_id],
                            ['class_id',       $class_id],
                            ['section_id',     $section_id],
                            ['student_id',     $request->student]
                        ])->first();


                        if ($getMark == "") {
                            Toastr::error('Please register marks for all students.!', 'Failed');
                            return redirect()->back();
                            // return redirect()->back()->with('message-danger', 'Please register marks for all students.!');
                        }
                    }
                }

                if ($request->student!='') {
                    $marks      = SmMarkStore::where([
                        ['exam_term_id', $request->exam],
                        ['class_id', $request->class],
                        ['section_id', $request->section],
                        ['student_id', $request->student]
                    ])->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

                    $students   = SmStudent::where([
                        ['class_id', $request->class],
                        ['section_id', $request->section],
                        ['id', $request->student]
                    ])
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

                    $subjects       = SmAssignSubject::where([
                        ['class_id', $request->class],
                        ['section_id', $request->section]
                    ])->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)
                    ->whereIn('subject_id', $examSubjectIds)
                    ->get();

                    

                    foreach ($subjects as $sub) {
                        $subject_list_name[] = $sub->subject->subject_name;
                    }

                    $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                    ->where('active_status', 1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->orderBy('gpa','desc')
                    ->get()
                    ->toArray();

                    $single_student = SmStudent::find($request->student);
                    $single_exam_term = SmExamType::find($request->exam);

                    $tabulation_details['student_name'] = $single_student->full_name;
                    $tabulation_details['student_roll'] = $single_student->roll_no;
                    $tabulation_details['student_admission_no'] = $single_student->admission_no;
                    $tabulation_details['student_class'] = $single_student->Class->class_name;
                    $tabulation_details['student_section'] = $single_student->section->section_name;
                    $tabulation_details['exam_term'] = $single_exam_term->title;
                    $tabulation_details['subject_list'] = $subject_list_name;
                    $tabulation_details['grade_chart'] = $grade_chart;
                } else {
                    $marks = SmMarkStore::where([
                        ['exam_term_id', $request->exam],
                        ['class_id', $request->class],
                        ['section_id', $request->section]
                    ])->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();
                    $students       = SmStudent::where([
                        ['class_id', $request->class],
                        ['section_id', $request->section]
                    ])->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();
                }


                $exam_types = SmExamType::where('active_status', 1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();

                $classes = SmClass::where('active_status', 1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();

                $single_class   = SmClass::find($request->class);
                $single_section   = SmSection::find($request->section);
                $subjects       = SmAssignSubject::where([
                    ['class_id', $request->class],
                    ['section_id', $request->section]
                ])
                ->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->whereIn('subject_id', $examSubjectIds)
                ->get();


                foreach ($subjects as $sub) {
                    $subject_list_name[] = $sub->subject->subject_name;
                }
                $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                                ->where('active_status', 1)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->orderBy('gpa','desc')
                                ->get()
                                ->toArray();

                $single_exam_term = SmExamType::find($request->exam);

                $tabulation_details['student_class'] = $single_class->class_name;
                $tabulation_details['student_section'] = $single_section->section_name;
                $tabulation_details['exam_term'] = $single_exam_term->title;
                $tabulation_details['subject_list'] = $subject_list_name;
                $tabulation_details['grade_chart'] = $grade_chart;

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['exam_types'] = $exam_types->toArray();
                    $data['classes'] = $classes->toArray();
                    $data['marks'] = $marks->toArray();
                    $data['subjects'] = $subjects->toArray();
                    $data['exam_term_id'] = $exam_term_id;
                    $data['class_id'] = $class_id;
                    $data['section_id'] = $section_id;
                    $data['students'] = $students->toArray();
                    return ApiBaseMethod::sendResponse($data, null);
                }
                $get_class = SmClass::where('active_status', 1)
                    ->where('id', $request->class)
                    ->first();

                $get_section = SmSection::where('active_status', 1)
                    ->where('id', $request->section)
                    ->first();
                $single = 0;

                $class_name = $get_class->class_name;
                $section_name = $get_section->section_name;
                return view('backEnd.reports.tabulation_sheet_report',
                compact('optional_subject_setup',
                'exam_types', 
                'classes', 
                'marks', 
                'subjects', 
                'exam_term_id', 
                'class_id', 
                'section_id', 
                'class_name', 
                'section_name', 
                'students', 
                'student_id', 
                'tabulation_details',
                'max_grade',
                'optional_subject_mark',
                'mark_sheet',
                'fail_grade_name',
                'year',
                'single'
                )
            );
            }
    }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
        }
    }

    //tabulationSheetReportPrint
    public function tabulationSheetReportPrint(Request $request)
    {
        try{

            if($request->allSection == "allSection"){
                $exam_term_id   = $request->exam_term_id;
                $class_id       = $request->class_id;
                $section_id     = $request->section_id;

                $fail_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->min('gpa');

                $fail_grade_name = SmMarksGrade::where('active_status',1)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->where('gpa',$fail_grade)
                                ->first();

                $max_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->max('gpa');

                $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                            ->where('active_status', 1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->orderBy('gpa','desc')
                            ->get()
                            ->toArray();
                
                $students   = SmStudent::where('class_id', $class_id)
                            ->where('section_id',$section_id)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();

                $examSubjects = SmExam::where([['exam_type_id', $exam_term_id], ['section_id', $section_id], ['class_id', $class_id]])
                                ->where('school_id',Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();

                $examSubjectIds = [];
                foreach($examSubjects as $examSubject){
                    $examSubjectIds[] = $examSubject->subject_id;
                }

                $subjects       = SmAssignSubject::where([
                    ['class_id', $request->class_id],
                    ['section_id', $request->section_id]
                ])->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->whereIn('subject_id', $examSubjectIds)
                ->get();

                $optional_subject_setup=SmClassOptionalSubject::where('class_id','=',$request->class_id)->first();

                $single_exam_term = SmExamType::find($exam_term_id);
                $className = SmClass::find($class_id);
                $sectionName = SmSection::find($section_id);
                $year=YearCheck::getYear();

                $tabulation_details['exam_term'] = $single_exam_term->title;
                $tabulation_details['class'] = $className->class_name;
                $tabulation_details['section'] = $sectionName->section_name;
                $tabulation_details['grade_chart'] = $grade_chart;

                $optional_subject_mark='';

                foreach($students as $student){
                    $get_optional_subject=SmOptionalSubjectAssign::where('student_id','=',$student->id)
                                    ->where('session_id','=',$student->session_id)
                                    ->first();
                }

                if ($get_optional_subject!='') {
                    $optional_subject_mark=$get_optional_subject->subject_id;
                }

                $mark_sheet = SmResultStore::where([['class_id', $request->class_id], ['exam_type_id', $request->exam_term_id], ['section_id', $request->section_id]])
                            ->whereIn('subject_id', $subjects->pluck('subject_id')
                            ->toArray())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();

                $allClass = 0;
                $year=YearCheck::getYear();


                return view('backEnd.reports.tabulation_sheet_report_print',
                compact('allClass',
                'exam_term_id',
                'class_id',
                'section_id',
                'fail_grade',
                'max_grade',
                'fail_grade_name',
                'tabulation_details',
                'year',
                'students',
                'subjects',
                'optional_subject_setup',
                'optional_subject_mark',
                'mark_sheet'
                ));
            }else{
                $exam_term_id   = $request->exam_term_id;
                $class_id       = $request->class_id;
                $section_id     = $request->section_id;
                $student_id     = $request->student_id;


                $examSubjects = SmExam::where([['exam_type_id', $exam_term_id], ['section_id', $section_id], ['class_id', $class_id]])
                                ->where('school_id',Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();

                $examSubjectIds = [];
                foreach($examSubjects as $examSubject){
                    $examSubjectIds[] = $examSubject->subject_id;
                }

                $subjects       = SmAssignSubject::where([
                    ['class_id', $request->class_id],
                    ['section_id', $request->section_id]
                ])->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->whereIn('subject_id', $examSubjectIds)
                ->get();

                $optional_subject_setup=SmClassOptionalSubject::where('class_id','=',$request->class_id)->first();

                $fail_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->min('gpa');

                $fail_grade_name = SmMarksGrade::where('active_status',1)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->where('gpa',$fail_grade)
                                ->first();

                $max_grade = SmMarksGrade::where('active_status',1)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id)
                            ->max('gpa');

                $student_detail = $studentDetails = SmStudent::find($request->student_id);

                $subjects_optional = $studentDetails->class->subjects->where('section_id', $request->section_id)
                            ->where('academic_id', getAcademicId())
                            ->where('school_id',Auth::user()->school_id);

                $optional_subject_mark='';

                $get_optional_subject=SmOptionalSubjectAssign::where('student_id','=',$student_detail->id)
                                    ->where('session_id','=',$student_detail->session_id)
                                    ->first();

                if ($get_optional_subject!='') {
                    $optional_subject_mark=$get_optional_subject->subject_id;
                }

                $mark_sheet = SmResultStore::where([['class_id', $request->class_id], ['exam_type_id', $request->exam_term_id], ['section_id', $request->section_id], ['student_id', $request->student_id]])
                            ->whereIn('subject_id', $subjects->pluck('subject_id')
                            ->toArray())
                            ->where('school_id',Auth::user()->school_id)
                            ->get();
                
                if (!empty($request->student_id)) {

                    $marks      = SmMarkStore::where([
                        ['exam_term_id',    $request->exam_term_id],
                        ['class_id',        $request->class_id],
                        ['section_id',      $request->section_id],
                        ['student_id',      $request->student_id]
                    ])
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

                    $students   = SmStudent::where([
                        ['class_id',    $request->class_id],
                        ['section_id',  $request->section_id],
                        ['id',          $request->student_id]
                    ])->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

                    $single_class   = SmClass::find($request->class_id);
                    $single_section   = SmSection::find($request->section_id);
                    $single_exam_term = SmExamType::find($request->exam_term_id);
                    $subject_list_name = [];

                    foreach ($subjects as $sub) {
                        $subject_list_name[] = $sub->subject->subject_name;
                    }

                    $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                                    ->where('active_status', 1)
                                    ->where('academic_id', getAcademicId())
                                    ->where('school_id',Auth::user()->school_id)
                                    ->orderBy('gpa','desc')
                                    ->get()
                                    ->toArray();
                                    
                    $single_student = SmStudent::find($request->student_id);
                    $single_exam_term = SmExamType::find($request->exam_term_id);
                    $tabulation_details['student_name'] = $single_student->full_name;
                    $tabulation_details['student_roll'] = $single_student->roll_no;
                    $tabulation_details['student_admission_no'] = $single_student->admission_no;
                    $tabulation_details['student_class'] = $single_student->Class->class_name;
                    $tabulation_details['student_section'] = $single_student->section->section_name;
                    $tabulation_details['exam_term'] = $single_exam_term->title;
                    $tabulation_details['subject_list'] = $subject_list_name;
                    $tabulation_details['grade_chart'] = $grade_chart;
                } else {
                    $marks = SmMarkStore::where([
                        ['exam_term_id', $request->exam_term_id],
                        ['class_id', $request->class_id],
                        ['section_id', $request->section_id]
                    ])
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

                    $students       = SmStudent::where([
                        ['class_id', $request->class_id],
                        ['section_id', $request->section_id]
                    ])->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();
                }

                $exam_types     = SmExamType::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
                $classes        = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

                foreach ($subjects as $sub) {
                    $subject_list_name[] = $sub->subject->subject_name;
                }
                $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                                ->where('active_status', 1)
                                ->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->orderBy('gpa','desc')
                                ->get()
                                ->toArray();

                $tabulation_details['student_class'] = $single_class->class_name;
                $tabulation_details['student_section'] = $single_section->section_name;
                $tabulation_details['exam_term'] = $single_exam_term->title;
                $tabulation_details['subject_list'] = $subject_list_name;
                $tabulation_details['grade_chart'] = $grade_chart;


                $get_class = SmClass::where('active_status', 1)
                    ->where('id', $request->class_id)
                    ->first();

                $get_section = SmSection::where('active_status', 1)
                    ->where('id', $request->section_id)
                    ->first();

                $class_name = $get_class->class_name;
                $section_name = $get_section->section_name;

                $customPaper = array(0, 0, 700.00, 1500.80);
                $single = 0;
                $year=YearCheck::getYear();

                return view('backEnd.reports.tabulation_sheet_report_print',
                compact('optional_subject_setup',
                'exam_types', 
                'classes', 
                'marks', 
                'subjects', 
                'exam_term_id', 
                'class_id', 
                'section_id', 
                'class_name', 
                'section_name', 
                'students', 
                'student_id', 
                'tabulation_details',
                'max_grade',
                'fail_grade_name',
                'optional_subject_mark',
                'mark_sheet',
                'subjects_optional',
                'single',
                'year'
                ));
            }
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function progressCardReport(Request $request)
    {
        try{
        $exams = SmExam::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();
        $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['routes'] = $exams->toArray();
            $data['assign_vehicles'] = $classes->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.reports.progress_card_report', compact('exams', 'classes'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    //student progress report search by Amit
    public function progressCardReportSearch(Request $request)
    {
        //input validations, 3 input must be required
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'student' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
        $exams = SmExam::where('active_status', 1)
                ->where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->get();

        $exam_types = SmExamType::where('active_status', 1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->pluck('id');
        
        

        $classes = SmClass::where('active_status', 1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

        $fail_grade = SmMarksGrade::where('active_status',1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->min('gpa');

        $fail_grade_name = SmMarksGrade::where('active_status',1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->where('gpa',$fail_grade)
                        ->first();

        $studentDetails = SmStudent::where('sm_students.id', $request->student)
                        ->join('sm_academic_years', 'sm_academic_years.id', '=', 'sm_students.session_id')
                        ->join('sm_classes', 'sm_classes.id', '=', 'sm_students.class_id') //canRelation
                        ->join('sm_sections', 'sm_sections.id', '=', 'sm_students.section_id') //relation
                        ->first();

        $marks_grade = SmMarksGrade::where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->orderBy('gpa','desc')
                    ->get();

        $maxGrade = SmMarksGrade::where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->max('gpa');

        $optional_subject_setup = SmClassOptionalSubject::where('class_id','=',$request->class)
                                    ->first();

        $student_optional_subject = SmOptionalSubjectAssign::where('student_id',$request->student)
                                    ->where('session_id','=',$studentDetails->session_id)
                                    ->first();

        $exam_setup = SmExamSetup::where([
                    ['class_id', $request->class], 
                    ['section_id', $request->section]])
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

        $class_id = $request->class;
        $section_id = $request->section;
        $student_id = $request->student;

        $examSubjects = SmExam::where([['section_id', $section_id], ['class_id', $class_id]])
                                ->where('school_id',Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();

        $examSubjectIds = [];
        foreach($examSubjects as $examSubject){
            $examSubjectIds[] = $examSubject->subject_id;
        }

        $subjects = SmAssignSubject::where([
                    ['class_id', $request->class], 
                    ['section_id', $request->section]])
                    ->where('school_id',Auth::user()->school_id)
                    ->whereIn('subject_id', $examSubjectIds)
                    ->get();

        $assinged_exam_types = [];
        foreach ($exams as $exam) {
            $assinged_exam_types[] = $exam->exam_type_id;
        }
        $assinged_exam_types = array_unique($assinged_exam_types);
        

        foreach ($assinged_exam_types as $assinged_exam_type) {
            $is_percentage = CustomResultSetting::where('exam_type_id',$assinged_exam_type)
            ->where('school_id',Auth::user()->school_id)
            ->where('academic_id',getAcademicId())
            ->first();

            if (is_null($is_percentage)) {
                Toastr::error('Please Complete Exam Result Settings .', 'Failed');
                return redirect('custom-result-setting');
              
            }

            foreach ($subjects as $subject) {
                $is_mark_available = SmResultStore::where([
                                    ['class_id', $request->class], 
                                    ['section_id', $request->section], 
                                    ['student_id', $request->student]
                                    // ['exam_type_id', $assinged_exam_type]]
                                    ])
                                    ->first();
                if ($is_mark_available == "") {
                    Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');
                    return redirect('progress-card-report');
                  
                }
            }
        }
        $is_result_available = SmResultStore::where([
            ['class_id', $request->class], 
            ['section_id', $request->section], 
            ['student_id', $request->student]])
            ->where('school_id',Auth::user()->school_id)
            ->get();

        if ($is_result_available->count() > 0) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['exams'] = $exams->toArray();
                $data['classes'] = $classes->toArray();
                $data['studentDetails'] = $studentDetails;
                $data['is_result_available'] = $is_result_available;
                $data['subjects'] = $subjects->toArray();
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                $data['student_id'] = $student_id;
                $data['exam_types'] = $exam_types;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.reports.progress_card_report', 
            compact(
            'exams',
            'optional_subject_setup',
            'student_optional_subject', 
            'classes', 'studentDetails',
            'is_result_available', 
            'subjects', 
            'class_id', 
            'section_id', 
            'student_id', 
            'exam_types', 
            'assinged_exam_types',
            'marks_grade',
            'fail_grade_name',
            'fail_grade',
            'maxGrade'
        ));
        } else {
            Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');
            return redirect('progress-card-report');
        }
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function progressCardPrint(Request $request)
    {
       try{
        $exams = SmExam::where('active_status', 1)
                ->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->get();

        $exam_types = SmExamType::where('active_status', 1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

        $classes = SmClass::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->get();

        $marks_grade = SmMarksGrade::where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->orderBy('gpa','desc')
                    ->get();

        $fail_grade = SmMarksGrade::where('active_status',1)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->min('gpa');

        $fail_grade_name = SmMarksGrade::where('active_status',1)
                        ->where('academic_id', getAcademicId())
                        ->where('school_id',Auth::user()->school_id)
                        ->where('gpa',$fail_grade)
                        ->first();

        $student_detail = SmStudent::find($request->student_id);

        $exam_setup = SmExamSetup::where([
                    ['class_id', $request->class_id], 
                    ['section_id', $request->section_id]])
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

        $studentDetails = SmStudent::where('sm_students.id', $request->student_id)
            ->join('sm_academic_years', 'sm_academic_years.id', '=', 'sm_students.session_id')
            ->join('sm_classes', 'sm_classes.id', '=', 'sm_students.class_id') //canRelation
            ->join('sm_sections', 'sm_sections.id', '=', 'sm_students.section_id') //relation
            ->first();


        $class_id = $request->class_id;
        $section_id = $request->section_id;
        $student_id = $request->student_id;


        $examSubjects = SmExam::where([ ['section_id', $section_id], ['class_id', $class_id]])
                                ->where('school_id',Auth::user()->school_id)
                                ->where('academic_id',getAcademicId())
                                ->get();

                $examSubjectIds = [];
                foreach($examSubjects as $examSubject){
                    $examSubjectIds[] = $examSubject->subject_id;
                }

        $subjects = SmAssignSubject::where([
                    ['class_id', $request->class_id], 
                    ['section_id', $request->section_id]])
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->whereIn('subject_id', $examSubjectIds)
                    ->get();

        $assinged_exam_types = [];
        foreach ($exams as $exam) {
            $assinged_exam_types[] = $exam->exam_type_id;
        }
        $assinged_exam_types = array_unique($assinged_exam_types);
        foreach ($assinged_exam_types as $assinged_exam_type) {
            foreach ($subjects as $subject) {
                $is_mark_available = SmResultStore::where([
                                ['class_id', $request->class_id], 
                                ['section_id', $request->section_id], 
                                ['student_id', $request->student_id], 
                                ['subject_id', $subject->subject_id], 
                                // ['exam_type_id', $assinged_exam_type]
                                ])
                                ->where('academic_id', getAcademicId())
                                ->first();
                if ($is_mark_available == "") {
                    Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');
                    return redirect('progress-card-report');
                    // return redirect('progress-card-report')->with('message-danger', 'Ops! Your result is not found! Please check mark register.');
                }
            }
        }
        
        $is_result_available = SmResultStore::where([['class_id', $request->class_id], ['section_id', $request->section_id], ['student_id', $request->student_id]])->where('academic_id', getAcademicId())->where('school_id',Auth::user()->school_id)->get();

        $optional_subject_setup=SmClassOptionalSubject::where('class_id','=',$request->class_id)->first();

        $student_optional_subject=SmOptionalSubjectAssign::where('student_id',$request->student_id)->where('academic_id','=',$student_detail->academic_id)->first();
        //    return $student_optional_subject;
        // $studentDetails = SmStudent::where('sm_students.id', $request->student)
        // $studentDetails = SmStudent::where('sm_students.id', $request->student)
         return view('backEnd.reports.progress_card_report_print', 
         compact(
             'optional_subject_setup',
             'student_optional_subject',
             'exams', 
             'classes', 
             'student_detail', 
             'is_result_available', 
             'subjects', 
             'class_id', 
             'section_id', 
             'student_id', 
             'exam_types', 
             'assinged_exam_types',
             'marks_grade',
             'studentDetails',
             'fail_grade_name'
            ));

        // $customPaper = array(0, 0, 700.00, 1500.80);

        // $pdf = PDF::loadView(
        //     'backEnd.reports.progress_card_report_print',
        //     [
        //         'optional_subject_setup'=> $optional_subject_setup ,
        //         'student_optional_subject'=> $student_optional_subject,
        //         'exams'    => $exams,
        //         'classes'       => $classes,
        //         'student_detail'         => $student_detail,
        //         'is_result_available'         => $is_result_available,
        //         'subjects'         => $subjects,
        //         'class_id'         => $class_id,
        //         'section_id'         => $section_id,
        //         'student_id'         => $student_id,
        //         'exam_types'         => $exam_types,
        //         'assinged_exam_types'         => $assinged_exam_types,
        //         'marks_grade'         => $marks_grade,
        //         'studentDetails'         => $studentDetails,
        //         'fail_grade_name' => $fail_grade_name,
        //     ]
        // )->setPaper('A4', 'portrait');
        // return $pdf->stream('progressCardReportPrint.pdf');
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}