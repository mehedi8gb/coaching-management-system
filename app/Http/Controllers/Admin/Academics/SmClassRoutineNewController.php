<?php

namespace App\Http\Controllers\Admin\Academics;

use App\ApiBaseMethod;
use App\Http\Controllers\Controller;
use App\SmAcademicYear;
use App\SmAssignSubject;
use App\SmClass;
use App\SmClassRoom;
use App\SmClassRoutineUpdate;
use App\SmClassTime;
use App\SmSection;
use App\SmStaff;
use App\SmStudent;
use App\SmWeekend;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SmClassRoutineNewController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }

    public function classRoutine(Request $request)
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $class_routines = SmClassRoutineUpdate::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            Session::put('session_day_id', null); /* for update class routine->abu nayem */
            return view('backEnd.academics.class_routine_new', compact('classes', 'class_routines'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function classRoutinePrint($class, $section)
    {

        // try {
        $print = request()->print;
        $class_times = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
        $class_id = $class;
        $section_id = $section;
        $academic_year = SmAcademicYear::find(getAcademicId());

        $sm_weekends = SmWeekend::with('classRoutine', 'classRoutine.subject')->where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();

        $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

        $customPaper = array(0, 0, 700.00, 1500.80);
        return view('backEnd.academics.class_routine_print',
            [
                'classes' => $classes,
                'class_times' => $class_times,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'academic_year' => $academic_year,
                'sm_weekends' => $sm_weekends,
                'section' => SmSection::find($section_id),
                'class' => SmClass::find($class_id),
                'print' => $print
            ]);

//        $pdf = \PDF::loadView(
//            'backEnd.academics.class_routine_print',
//            [
//                'classes' => $classes,
//                'class_times' => $class_times,
//                'class_id' => $class_id,
//                'section_id' => $section_id,
//                'academic_year' => $academic_year,
//                'sm_weekends' => $sm_weekends,
//                'section' => SmSection::find($section_id),
//                'class' => SmClass::find($class_id),
//            ]
//        )->setPaper('A4', 'landscape');
//        return $pdf->stream('class_routine.pdf');
    }

    public function printTeacherRoutine($teacher_id)
    {
        try {
            $sm_weekends = SmWeekend::with('classRoutine', 'classRoutine.subject')->where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
            $teacher = SmStaff::find($teacher_id)->full_name;
            $pdf = \PDF::loadView(
                'backEnd.academics.teacher_class_routine_print',
                [
                    'sm_weekends' => $sm_weekends,
                    'teacher' => $teacher,
                    'teacher_id' => $teacher_id,
                ]
            )->setPaper('A4', 'landscape');
            return $pdf->stream('teacher_class_routine.pdf');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function classRoutineSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $class_id = $request->class;
            $section_id = $request->section;

            $sm_weekends = SmWeekend::with('classRoutine')->where('school_id', Auth::user()->school_id)
                ->orderBy('order', 'ASC')
                ->where('active_status', 1)
                ->get();
            // return $sm_weekends;
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $subjects = SmAssignSubject::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('school_id', Auth::user()->school_id)
                ->groupBy(['class_id', 'section_id', 'subject_id'])
                ->get();

            // remove code unnecessary -abunayem

            $rooms = SmClassRoom::where('active_status', 1) /* ->where('capacity','>=',$stds) */
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $teachers = SmStaff::where('role_id', 4)->where('school_id', Auth::user()->school_id)->get(['id', 'full_name', 'user_id']);

            if (!$class_id) {
                Session::put('session_day_id', null);
            }

            return view('backEnd.academics.class_routine_new', compact('classes', 'teachers', 'rooms', 'subjects', 'class_id', 'section_id', 'sm_weekends'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addNewClassRoutine($day, $class_id, $section_id)
    {
        try {
            $assinged_subjects = SmClassRoutineUpdate::select('subject_id')->where('class_id', $class_id)
                ->where('section_id', $section_id)->where('day', $day)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $assinged_subject = [];
            foreach ($assinged_subjects as $value) {
                $assinged_subject[] = $value->subject_id;
            }

            $assinged_rooms = SmClassRoutineUpdate::select('room_id')
                ->where('day', $day)
                ->where('school_id', Auth::user()->school_id)->get();

            $assinged_room = [];
            foreach ($assinged_rooms as $value) {
                $assinged_room[] = $value->room_id;
            }
            $stds = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->count();
            $rooms = SmClassRoom::where('active_status', 1)->where('capacity', '>=', $stds)
                ->where('school_id', Auth::user()->school_id)
                ->get();
            $subjects = SmAssignSubject::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('school_id', Auth::user()->school_id)
                ->groupby(['class_id', 'section_id', 'subject_id'])
                ->get();

            return view('backEnd.academics.add_new_class_routine_form', compact('rooms', 'subjects', 'day', 'class_id', 'section_id', 'assinged_subject', 'assinged_room'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addNewClassRoutineEdit($class_time_id, $day, $class_id, $section_id, $subject_id, $room_id, $assigned_id, $teacher_id)
    {
        try {
            $assinged_subjects = SmClassRoutineUpdate::select('subject_id')->where('class_id', $class_id)->where('section_id', $section_id)->where('day', $day)->where('subject_id', '!=', $subject_id)->where('school_id', Auth::user()->school_id)->get();

            $assinged_subject = [];
            foreach ($assinged_subjects as $value) {
                $assinged_subject[] = $value->subject_id;
            }

            $assinged_rooms = SmClassRoutineUpdate::select('room_id')->where('room_id', '!=', $room_id)
                ->where('class_period_id', $class_time_id)
                ->where('day', $day)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $assinged_room = [];
            foreach ($assinged_rooms as $value) {
                $assinged_room[] = $value->room_id;
            }
            $stds = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)
                ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->count();
            $rooms = SmClassRoom::where('active_status', 1)->where('capacity', '>=', $stds)
                ->where('school_id', Auth::user()->school_id)
                ->get();
            $teacher_detail = SmStaff::select('id', 'full_name')->where('id', $teacher_id)->first();

            $already_assigned = SmClassRoutineUpdate::select('teacher_id')->where('class_period_id', $class_time_id)
                ->where('day', $day)
                ->where('teacher_id', '!=', $teacher_id)
                ->get();

            $subject_teachers = SmAssignSubject::select('teacher_id')
                ->where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('subject_id', $subject_id)
                ->whereNotIn('teacher_id', $already_assigned)
                ->get();
            $teachers = SmStaff::select('id', 'full_name')->whereIN('id', $subject_teachers)->get();

            $subjects = SmAssignSubject::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('school_id', Auth::user()->school_id)
                ->groupBy(['class_id', 'section_id', 'subject_id'])
                ->get();
            return view('backEnd.academics.add_new_class_routine_form', compact('rooms', 'subjects', 'day', 'class_time_id', 'class_id', 'section_id', 'assinged_subject', 'assinged_room', 'subject_id', 'room_id', 'assigned_id', 'teacher_detail', 'teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function dayWiseClassRoutine(Request $request)
    {

        $day_id = $request->day_id;
        $class_id = $request->class_id;
        $section_id = $request->section_id;

        $class_routines = SmClassRoutineUpdate::where('day', $day_id)->where('class_id', $class_id)->where('section_id', $section_id)
            ->orderBy('start_time', 'ASC')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

        $subjects = SmAssignSubject::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('school_id', Auth::user()->school_id)
            ->groupBy(['class_id', 'section_id', 'subject_id'])
            ->get();

        $stds = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)
            ->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->count();
        $rooms = SmClassRoom::where('active_status', 1)->where('capacity', '>=', $stds)
            ->where('school_id', Auth::user()->school_id)
            ->get();
        $teachers = SmStaff::where('role_id', 4)->where('school_id', Auth::user()->school_id)->get(['id', 'full_name', 'user_id']);
        $sm_weekends = SmWeekend::where('school_id', Auth::user()->school_id)
            ->orderBy('order', 'ASC')
            ->where('active_status', 1)
            ->get();
        return view('backEnd.academics.classRoutine.form', compact('day_id', 'class_routines', 'sm_weekends', 'subjects', 'rooms', 'teachers', 'section_id', 'class_id'));
    }

    public function addNewClassRoutineStore(Request $request)
    {
        try {
            //  return  date("H:i", strtotime("04:25 PM"));
  
            // change this method code for update class routine ->abu Nayem
            $request->validate([
                'class_id' => 'required',
                'section_id' => 'required',
                'day' => 'required',
            ]);

            SmClassRoutineUpdate::where('day', $request->day)->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->delete();

            foreach ($request->routine as $key => $routine_data) {
                if (!gv($routine_data, 'subject') || !gv($routine_data, 'start_time') || !gv($routine_data, 'end_time')) {
                    continue;
                }
                $days = gv($routine_data, 'day_ids') == null ? array($request->day) : gv($routine_data, 'day_ids', []);

                foreach ($days as $day) {
                    $exist_class_routine = SmClassRoutineUpdate::where('day', $day)
                        ->where('class_id', $request->class_id)
                        ->where('section_id', $request->section_id)
                        ->where('start_time', date('H:i:s', strtotime(gv($routine_data, 'start_time'))))
                        ->where('end_time', date('H:i:s', strtotime(gv($routine_data, 'end_time'))))
                        ->where('subject_id', gv($routine_data, 'subject'))
                        ->where('teacher_id', gv($routine_data, 'teacher_id'))
                        ->where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->first();
                  
                    if ($exist_class_routine) {
                        continue;
                    }

                    $class_routine_time = SmClassRoutineUpdate::where('day', $day)
                                            ->where('class_id', $request->class_id)
                                            ->where('section_id', $request->section_id)
                                            ->where('academic_id', getAcademicId())
                                            ->where('school_id', Auth::user()->school_id)
                                            ->first();
                    $timeInterval=[];
                    $startTimeToInteger=null;
                    if ($class_routine_time) {
                        $start_time = $class_routine_time->start_time;
                        $end_time = $class_routine_time->end_time;
                        $startTimeToInteger = str_replace(':', '', $start_time);
                        $endTimeToInteger = str_replace(':', '', $end_time);
                        $timeInterval = range($startTimeToInteger, $endTimeToInteger);
                    }
                    $requestStartTime = date('H:i:s', strtotime(gv($routine_data, 'start_time')));
                    if (in_array($requestStartTime, $timeInterval)) {
                        Toastr::error('This Time Has another Class', 'Failed');
                        return redirect()->back();
                    }

                    $class_routine = new SmClassRoutineUpdate();
                    $class_routine->class_id = $request->class_id;
                    $class_routine->section_id = $request->section_id;
                    $class_routine->subject_id = gv($routine_data, 'subject');
                    $class_routine->teacher_id = gv($routine_data, 'teacher_id');
                    $class_routine->room_id = gv($routine_data, 'room');
                    $class_routine->start_time = date('H:i:s', strtotime(gv($routine_data, 'start_time')));
                    $class_routine->end_time = date('H:i:s', strtotime(gv($routine_data, 'end_time')));
                    $class_routine->is_break = gv($routine_data, 'is_break');
                    $class_routine->day = $day;
                    $class_routine->school_id = Auth::user()->school_id;
                    $class_routine->academic_id = getAcademicId();
                    $class_routine->save();
                }
            }

            Session::put('session_day_id', $request->day);
            Toastr::success('Class routine has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function classRoutineRedirect($class_id, $section_id)
    {
        try {
            $sm_weekends = SmWeekend::where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
            $class_times = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            return view('backEnd.academics.class_routine_new', compact('classes', 'class_times', 'class_id', 'section_id', 'sm_weekends'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getClassTeacherAjax(Request $request)
    {

        try {

            $already_assigned = SmClassRoutineUpdate::select('teacher_id')
                ->where('day', $request->day)
                ->get();

            $subject_teacher = SmAssignSubject::where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->where('subject_id', $request->subject)
                ->whereNotIn('teacher_id', $already_assigned)
                ->get('teacher_id', 'id');

            $teachers = [];
            foreach ($subject_teacher as $teacherId) {

                $teachers[] = SmStaff::find($teacherId->teacher_id);

            }

            return response()->json([$teachers]);

        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }
    public function getOtherDaysAjax(Request $request)
    {

        try {

            $assgin_days = SmClassRoutineUpdate::query();
            $assgin_days->where('class_period_id', $request->class_time_id)
                ->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id);
            $assgin_day_ids = $assgin_days->select('day')->get();

            $days = SmWeekend::query();
            $days->whereNotIn('id', $assgin_day_ids);
            $days = $days->where('is_weekend', 0)->orderBy('order', 'ASC')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->where('active_status', 1)->get();
            return response()->json([$days]);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function classRoutineReport(Request $request)
    {

        try {
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.reports.class_routine_report', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function classRoutineReportSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $class_times = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $class_id = $request->class;
            $section_id = $request->section;
            $sm_weekends = SmWeekend::with('classRoutine', 'classRoutine.subject')->where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $sm_routine_updates = $classes->where('id', $request->class)->first()->routineUpdates->where('section_id', $section_id);
  
            return view('backEnd.reports.class_routine_report', compact('classes', 'class_times', 'class_id', 'section_id', 'sm_routine_updates', 'sm_weekends'));
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Failed');
//            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function teacherClassRoutineReport(Request $request)
    {

        try {
            $teachers = SmStaff::select('id', 'full_name')->where('active_status', 1)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($teachers, null);
            }
            return view('backEnd.reports.teacher_class_routine_report', compact('teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function teacherClassRoutineReportSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'teacher' => 'required',
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $class_times = SmClassTime::where('type', 'class')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            $teacher_id = $request->teacher;
            $sm_weekends = SmWeekend::where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
            $teachers = SmStaff::where('role_id', 4)->select('id', 'full_name')->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['class_times'] = $class_times->toArray();
                $data['teacher_id'] = $teacher_id;
                $data['sm_weekends'] = $sm_weekends->toArray();
                $data['teachers'] = $teachers->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.reports.teacher_class_routine_report', compact('class_times', 'teacher_id', 'sm_weekends', 'teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteClassRoutineModal($id)
    {

        try {
            return view('backEnd.academics.delete_class_routine', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteClassRoutine($id)
    {

        try {

            // $class_routine = SmClassRoutineUpdate::find($id);
            if (checkAdmin()) {
                $class_routine = SmClassRoutineUpdate::find($id);
            } else {
                $class_routine = SmClassRoutineUpdate::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
            }
            $class_id = $class_routine->class_id;
            $section_id = $class_routine->section_id;
            $result = $class_routine->delete();
            if ($result) {
                Toastr::success('Class routine has been deleted successfully', 'Success');
            } else {
                Toastr::error('Operation Failed', 'Failed');
            }
            return redirect('class-routine-new/' . $class_id . '/' . $section_id);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //new update class routine delete abu nayem

    public function destroyClassRoutine(Request $request)
    {
        try {

            $id = $request->id;

            if (checkAdmin()) {
                $class_routine = SmClassRoutineUpdate::find($id);
            } else {
                $class_routine = SmClassRoutineUpdate::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
            }
            $class_routine->delete();

            return response(["done"]);

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}
