<?php

namespace App\Http\Controllers;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\SmExamType;
use App\SmAcademicYear;
use App\SmStudentPromotion;
use App\CustomResultSetting;
use Illuminate\Http\Request;
use App\SmTemporaryMeritlist;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Scopes\StatusAcademicSchoolScope;
use App\SmClassSection;
use Illuminate\Validation\ValidationException;

class SmStudentPromoteController extends Controller
{
    //

    public function index()
    {
        try {
            $generalSetting = generalSetting();
            $sessions = SmAcademicYear::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)->get();
            $exams = SmExamType::where('active_status', 1)->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)->get();
          
            
            if ($generalSetting->promotionSetting == 0) {
                return view('backEnd.studentInformation.student_promote_new', compact('sessions', 'classes'));
            } else {
                return view('backEnd.studentInformation.student_promote_with_exam', compact('sessions', 'classes', 'exams'));

            }

        } catch (\Throwable $th) {
            //throw $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentCurrentSearch(Request $request)
    {

        //  return $request->all();
        $request->validate([
            'current_session' => 'required',
            'promote_session' => 'required',
            'current_class' => 'required',
            'current_section' => 'sometimes',
        ]);

        try {

            $students = SmStudent::query()->with('class', 'section');

            if ($request->current_session) {
                $students->where('session_id', '=', $request->current_session);
            }
            if ($request->current_class) {
                $students->where('class_id', '=', $request->current_class);
            }
            if ($request->current_section) {
                $students->Where('section_id', $request->current_section);
            }
            $students = $students->where('active_status', 1)
                ->orderBy('roll_no', 'ASC')
                ->where('academic_id', $request->current_session)
                ->where('school_id', Auth::user()->school_id)
                ->withOutGlobalScope(StatusAcademicSchoolScope::class)
                ->get();

            $current_session = $request->current_session;
            $current_class = $request->current_class;
            $current_section = $request->current_section;
            $promote_session = $request->promote_session;
            $sessions = SmAcademicYear::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();
            $currrent_academic_class = SmClass::where('active_status', 1)
                ->where('academic_id', $request->current_session)
                ->withOutGlobalScope(StatusAcademicSchoolScope::class)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $classes = SmClass::with('classSection')->where('active_status', 1)
                ->where('academic_id', $request->promote_session)
                ->withOutGlobalScope(StatusAcademicSchoolScope::class)
                ->where('school_id', Auth::user()->school_id)
                ->get();


            // return $classes;
            if (empty($classes)) {
                Toastr::error('No Class found For Next Academic Year', 'Failed');
                return redirect('student-promote');
            }

            $next_class = $classes->except($current_class)->first();

            $next_sections = SmClassSection::with('sectionWithoutGlobal')->where('class_id', '=', $next_class->id)->where('academic_id', $request->promote_session)
                ->where('school_id', Auth::user()->school_id)->withoutGlobalScope(StatusAcademicSchoolScope::class)->get();

            $search_current_class = SmClass::withoutGlobalScope(StatusAcademicSchoolScope::class)->findOrFail($request->current_class);
            $search_current_section = SmSection::withoutGlobalScope(StatusAcademicSchoolScope::class)->find($request->current_section);
            $search_current_academic_year = SmAcademicYear::find($request->current_session);
            $search_promote_academic_year = SmAcademicYear::find($request->promote_session);
            $sections = $search_current_class ? $search_current_class->classSection : [];

            // return $search_info;
            if (empty($students)) {
                Toastr::error('No result found', 'Failed');
                return redirect('student-promote');
            }

            return view('backEnd.studentInformation.student_promote_new', compact('currrent_academic_class','next_class', 'sessions', 'classes', 'students', 'current_session', 'current_class', 'current_section', 'promote_session', 'search_current_class', 'search_current_section', 'search_current_academic_year', 'search_promote_academic_year', 'sections', 'next_sections'));

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function rollCheck(Request $request)
    {

        $exist_roll_number = SmStudent::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('roll_no', $request->promote_roll_number)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->count();

        return response()->json($exist_roll_number);
    }

    public function promote(Request $request)
    {

        // return $request->all();
        $request->validate([
            'promote_session' => 'required',
            'promote.*.class' => 'required',
            'promote.*.section' => 'required_with:promote.*.student',
            'promote.*.roll_number' => 'sometimes|nullable|integer',

        ]);

        // $validator=Validator::make()

        try {
            //code...

            $promote_year = SmAcademicYear::find($request->promote_session);
            if (empty($request->promote)) {
                Toastr::error('Please Select Student', 'Failed');
                return back();
            }
            foreach ($request->promote as $student_id => $student_data) {
                if (!gv($student_data, 'student') || !gv($student_data, 'class') || !gv($student_data, 'section')) {
                    continue;
                }
                $roll_number = gv($student_data, 'roll_number');

                if ($roll_number) {
                    $exist_roll_number = SmStudent::withoutGlobalScope(StatusAcademicSchoolScope::class)->where('class_id', gv($student_data, 'class'))->where('section_id', gv($student_data, 'section'))->where('roll_no', $roll_number)->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)->count();

                    if ($exist_roll_number) {
                        throw ValidationException::withMessages(['promote.' . $student_id . '.roll_number' => 'Roll no already exist']);
                    }
                } else {
                    $roll_number = SmStudent::withoutGlobalScope(StatusAcademicSchoolScope::class)->where('class_id', (int) gv($student_data, 'class'))
                            ->where('section_id', (int) gv($student_data, 'section'))->where('academic_id', getAcademicId())
                            ->where('school_id', Auth::user()->school_id)->max('roll_no') + 1;
                }

                $student_details = SmStudent::withoutGlobalScope(StatusAcademicSchoolScope::class)->findOrFail($student_id);

                $student_promote = new SmStudentPromotion();
                $student_promote->student_id = $student_id;

                $student_promote->previous_class_id = $student_details->class_id;
                $student_promote->current_class_id = gv($student_data, 'class');

                $student_promote->previous_session_id = $request->current_session;
                $student_promote->current_session_id = $request->promote_session;

                $student_promote->previous_section_id = $student_details->section_id;
                $student_promote->current_section_id = gv($student_data, 'section');

                $student_promote->admission_number = $student_details->admission_no;
                $student_promote->student_info = $student_details->toJson();
                $student_promote->merit_student_info = $student_details->toJson();
                $student_promote->previous_roll_number = $student_details->roll_no;
                $student_promote->current_roll_number = $roll_number;
                $student_promote->academic_id = $request->promote_session;
                $student_promote->result_status = gv($student_data, 'result') ? gv($student_data, 'result') : 'F';
                $student_promote->save();

                $student_details->session_id = $request->promote_session;
                $student_details->class_id = gv($student_data, 'class');
                $student_details->academic_id = $request->promote_session;
                $student_details->section_id = gv($student_data, 'section');
                $student_details->roll_no = $roll_number;
                $student_details->created_at = $promote_year->starting_date . ' 12:00:00';
                $student_details->save();
            }

            Toastr::success('Operation successful', 'Success');
            return back();

        } catch (\Throwable $th) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function studentSearchWithExam(Request $request)
    {

        // return $request->all();
        $request->validate([
            'current_session' => 'required',
            'promote_session' => 'required',
            'current_class' => 'required',
            'current_section' => 'sometimes',
            'exam' => 'required',
        ]);

        try {

            $meritListSettings = CustomResultSetting::first('merit_list_setting')->merit_list_setting;

            $merit_list = $this->meritList($request);

            $student_ids = SmStudent::query()->with('class', 'section');

            if ($request->current_session) {
                $student_ids->where('session_id', '=', $request->current_session);
            }
            if ($request->current_class) {
                $student_ids->where('class_id', '=', $request->current_class);
            }
            if ($request->current_section) {
                $student_ids->Where('section_id', $request->current_section);
            }
            $student_ids = $student_ids->where('active_status', 1)
                ->orderBy('roll_no', 'ASC')
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get()->pluck('id')->toArray();

            $students = SmTemporaryMeritlist::query()->with('class', 'studentinfo', 'section');

            if ($request->current_session) {
                $students->where('academic_id', '=', $request->current_session);
            }
            if ($request->current_class) {
                $students->where('class_id', '=', $request->current_class);
            }
            if ($request->current_section) {
                $students->Where('section_id', $request->current_section);
            }
            if ($meritListSettings == "total_grade") {
                $students->orderBy('gpa_point', 'DESC');
            } else {
                $students->orderBy('total_marks', 'DESC');
            }

            $students = $students->whereIn('student_id', $student_ids)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            if (count($students) == 0) {
                Toastr::error('Please Check Your Merit List First', 'Failed');
                return back();
            }

            $current_session = $request->current_session;
            $current_class = $request->current_class;
            $current_section = $request->current_section;
            $promote_session = $request->promote_session;
            $exam_id = $request->exam;
            $sessions = SmAcademicYear::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $classes = SmClass::with('classSection')->where('active_status', 1)
                ->where('academic_id', $request->promote_session)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            // return $classes;
            if (empty($classes)) {
                Toastr::error('No Class found For Next Academic Year', 'Failed');
                return redirect('student-promote');
            }

            $next_class = $classes->except($current_class)->first();
            $search_current_class = SmClass::findOrFail($request->current_class);
            $search_current_section = SmSection::find($request->current_section);
            $search_current_academic_year = SmAcademicYear::find($request->current_session);
            $search_promote_academic_year = SmAcademicYear::find($request->promote_session);
            $search_exams = SmExamType::find($request->exam)->title;
            $sections = $search_current_class ? $search_current_class->classSection : [];
            $exams = SmExamType::where('active_status', 1)->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)->get();

            // return $search_info;
            if (empty($students)) {
                Toastr::error('No result found', 'Failed');
                return redirect('student-promote');
            }
            return view('backEnd.studentInformation.student_promote_with_exam', compact('next_class', 'sessions', 'classes', 'students', 'current_session', 'current_class', 'current_section', 'promote_session', 'search_current_class', 'search_current_section', 'search_current_academic_year', 'search_promote_academic_year', 'sections', 'exams', 'exam_id', 'search_exams'));

        } catch (\Throwable $th) {
           
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}
