<?php

namespace App\Http\Controllers;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\YearCheck;
use App\ApiBaseMethod;
use App\SmStudentAttendance;
use Illuminate\Http\Request;
use App\SmStudentAttendanceImport;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SmStudentAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        
        try{
            $classes = SmClass::where('active_status', 1)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function studentSearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'attendance_date' => 'required'
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
            $date = $request->attendance_date;
            $classes = SmClass::where('active_status', 1)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
    
            $students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->where('active_status', 1)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
    
            if ($students->isEmpty()) {
                Toastr::error('No Result Found', 'Failed');
                return redirect('student-attendance');
            }
    
            $already_assigned_students = [];
            $new_students = [];
            $attendance_type = "";
            foreach ($students as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->first();
    
                if ($attendance != "") {
                    $already_assigned_students[] = $attendance;
                    $attendance_type =  $attendance->attendance_type;
                } else {
                    $new_students[] =  $student;
                }
            }
    
            $class_id = $request->class;
            $class_info = SmClass::find($request->class);
            $section_info = SmSection::find($request->section);
    
            $search_info['class_name'] = $class_info->class_name;
            $search_info['section_name'] = $section_info->section_name;
            $search_info['date'] = $request->attendance_date;
    
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['date'] = $date;
                $data['class_id'] = $class_id;
                $data['already_assigned_students'] = $already_assigned_students;
                $data['new_students'] = $new_students;
                $data['attendance_type'] = $attendance_type;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.studentInformation.student_attendance', compact('classes', 'date', 'class_id', 'date', 'already_assigned_students', 'new_students', 'attendance_type', 'search_info'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function studentAttendanceStore(Request $request)
    {

        try{
            foreach ($request->id as $student) {
                $attendance = SmStudentAttendance::where('student_id', $student)->where('attendance_date', date('Y-m-d', strtotime($request->date)))->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->first();
    
                if ($attendance != "") {
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
                $attendance->save();
            }
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Student attendance been submitted successfully');
            }
            Toastr::success('Operation successful', 'Success');
            return redirect('student-attendance');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function studentAttendanceHoliday(Request $request)
    {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
    }

    public function studentAttendanceImport(){

        
        try{
            $classes = SmClass::where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
            return view('backEnd.studentInformation.student_attendance_import', compact('classes'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    public function downloadStudentAtendanceFile(){        
        
        try{
            $studentsArray = ['admission_no', 'class_id', 'section_id', 'attendance_date', 'in_time', 'out_time'];

            return Excel::create('student_attendance_sheet', function ($excel) use ($studentsArray) {
                $excel->sheet('student_attendance_sheet', function ($sheet) use ($studentsArray) {
                    $sheet->fromArray($studentsArray);
                });
            })->download('xlsx');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }

    public function studentAttendanceBulkStore(Request $request){


        $request->validate([
            'attendance_date' => 'required',
            'file' => 'required'
        ]);
        $file_type=strtolower($request->file->getClientOriginalExtension());
        if ($file_type<>'csv' && $file_type<>'xlsx' && $file_type<>'xls') {
            Toastr::warning('The file must be a file of type: xlsx, csv or xls', 'Warning');
            return redirect()->back();
        }else{
        try{
        $max_admission_id = SmStudent::where('school_id',Auth::user()->school_id)->max('admission_no');
        $path = $request->file('file')->getRealPath();
        $data = Excel::load($path)->get();
        // return $request;
        // if ($data->count()) {
        if (!empty($data)) {
            $class_sections = [];
            foreach ($data as $key => $value) {
                if(date('d/m/Y', strtotime($request->attendance_date)) == date('d/m/Y', strtotime($value->attendance_date))){
                    $class_sections[] = $value->class_id.'-'.$value->section_id;
                }
            }
            // return $request;
            DB::beginTransaction();


            $all_student_ids = [];
            $present_students = [];
            foreach(array_unique($class_sections) as $value){

                $class_section = explode('-', $value);
                $students = SmStudent::where('class_id', $class_section[0])->where('section_id', $class_section[1])->where('school_id',Auth::user()->school_id)->get();

                foreach($students as $student){
                    SmStudentAttendanceImport::where('student_id', $student->id)->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))->delete();
                    $all_student_ids[] = $student->id;
                }

            }

            try {
                foreach ($data as $key => $value) {

                    if ($value->filter()->isNotEmpty()) {
                        if(date('d/m/Y', strtotime($request->attendance_date)) == date('d/m/Y', strtotime($value->attendance_date))){
                            $student = SmStudent::select('id')->where('admission_no', $value->admission_no)->where('school_id',Auth::user()->school_id)->first();
                            if($student != ""){
                                $present_students[] = $student->id;
                                $import = new SmStudentAttendanceImport();
                                $import->student_id = $student->id;
                                $import->attendance_date = date('Y-m-d', strtotime($value->attendance_date));
                                $import->attendance_type = 'P';
                                $import->in_time = $value->in_time;
                                $import->out_time = $value->out_time;
                                $import->school_id = Auth::user()->school_id;
                                $import->save();
                            }
                        }

                    }

                }


                foreach ($all_student_ids as $all_student_id) {
                    if(!in_array($all_student_id, $present_students)){
                        $import = new SmStudentAttendanceImport();
                        $import->student_id = $all_student_id;
                        $import->attendance_type = 'A';
                        $import->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                        $import->school_id = Auth::user()->school_id;
                        $import->save();
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
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    }
}
