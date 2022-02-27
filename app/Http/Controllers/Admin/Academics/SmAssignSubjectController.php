<?php

namespace App\Http\Controllers\Admin\Academics;

use App\SmClass;
use App\SmStaff;
use App\SmSubject;
use App\YearCheck;
use App\ApiBaseMethod;
use App\SmClassSection;
use App\SmAssignSubject;
use Illuminate\Http\Request;
use App\Events\CreateClassGroupChat;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SmAssignSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
     
    }
    public function index(Request $request)
    {

        try {
            $classes = SmClass::get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.academics.assign_subject', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function create(Request $request)
    {

        try {
            $classes = SmClass::get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($classes, null);
            }
            return view('backEnd.academics.assign_subject_create', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function ajaxSubjectDropdown(Request $request)
    {
        try {
            $staff_info = SmStaff::where('user_id', Auth::user()->id)->first();
            if (teacherAccess()) {
                $class_id = $request->class;
                $allSubjects = SmAssignSubject::where([['section_id', '=', $request->id], ['class_id', $class_id], ['teacher_id', $staff_info->id]])->where('school_id', Auth::user()->school_id)->get();
                $subjectsName = [];
                foreach ($allSubjects as $allSubject) {
                    $subjectsName[] = SmSubject::find($allSubject->subject_id);
                }
            } else {
                $class_id = $request->class;
                $allSubjects = SmAssignSubject::where([['section_id', '=', $request->id], ['class_id', $class_id]])->where('school_id', Auth::user()->school_id)->get();

                $subjectsName = [];
                foreach ($allSubjects as $allSubject) {
                    $subjectsName[] = SmSubject::find($allSubject->subject_id);
                }
            }
            return response()->json([$subjectsName]);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Error msg'], 404);
        }
    }

    public function search(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
             'section' => 'required'
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

             $assign_subjects=SmAssignSubject::query();
            $assign_subjects= $assign_subjects->where('class_id',$request->class);

            if($request->section !=null){
                $assign_subjects= $assign_subjects->where('section_id',$request->section);
            }

             $assign_subjects=$assign_subjects->where('school_id',Auth::user()->school_id)->get();
           

            $subjects = SmSubject::where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get();
            $teachers = SmStaff::where('active_status', 1)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->get();
         
            $class_id = $request->class;
            $section_id = $request->section;

            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['assign_subjects'] = $assign_subjects->toArray();
                $data['teachers'] = $teachers->toArray();
                $data['subjects'] = $subjects->toArray();
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.academics.assign_subject_create', compact('classes', 'assign_subjects', 'teachers', 'subjects', 'class_id', 'section_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function assignSubjectAjax(Request $request)
    {

        try {
            $subjects = SmSubject::get();
            $teachers = SmStaff::status()->where('role_id', 4)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['subjects'] = $subjects->toArray();
                $data['teachers'] = $teachers->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return response()->json([$subjects, $teachers]);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Error msg'], 404);
        }
    }

    public function assignSubjectStore(Request $request)
    {
        try {
            if ($request->update == 0) {
                $i = 0;
                //  $k = 0;
                if (isset($request->subjects)) {
                    foreach ($request->subjects as $key=>$subject) {
                        if ($subject != "") {                            
                            if($request->section_id==null){
                                $k = 0;
                                $all_section=SmClassSection::where('class_id',$request->class_id)->get();
                               $t_teacher=count($request->teachers);
                                foreach($all_section as $section){                                        
                                    $assign_subject = new SmAssignSubject();
                                    $assign_subject->class_id = $request->class_id;
                                    $assign_subject->school_id = Auth::user()->school_id;
                                    $assign_subject->section_id = $section->section_id;
                                    $assign_subject->subject_id = $subject;                            
                                    $assign_subject->teacher_id = $request->teachers[$key];                                    
                                    $assign_subject->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                                    $assign_subject->academic_id = getAcademicId();
                                    $assign_subject->save();
                                    event(new CreateClassGroupChat($assign_subject));
                                    $k++;
                                }

                            }else{
                            $assign_subject = new SmAssignSubject();
                            $assign_subject->class_id = $request->class_id;
                            $assign_subject->school_id = Auth::user()->school_id;
                            $assign_subject->section_id = $request->section_id;
                            $assign_subject->subject_id = $subject;
                            $assign_subject->teacher_id = $request->teachers[$i];
                            $assign_subject->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                            $assign_subject->academic_id = getAcademicId();
                            $assign_subject->save();
                            event(new CreateClassGroupChat($assign_subject));
                            $i++;
                            }
                        }
                    }
                }
            } elseif ($request->update == 1) {
                if($request->section_id ==null){
                    $assign_subjects = SmAssignSubject::where('class_id', $request->class_id)->delete();

                    $i = 0;
                    if (! empty($request->subjects)) {
            
                        foreach ($request->subjects as $key=>$subject) {
                            $k = 0;
                            if (!empty($subject)) {

                                $all_section=SmClassSection::where('class_id',$request->class_id)->get();
                                foreach($all_section as $section){
                         
                                $assign_subject = new SmAssignSubject();
                                $assign_subject->class_id = $request->class_id;
                                $assign_subject->section_id = $section->section_id;
                                $assign_subject->subject_id = $subject;
                                $assign_subject->teacher_id = $request->teachers[$key];
                                $assign_subject->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                                $assign_subject->academic_id = getAcademicId();
                                $assign_subject->school_id = Auth::user()->school_id;

                                
                                $assign_subject->save();
                                event(new CreateClassGroupChat($assign_subject));
                                $k++;
                                }
                            }
                        }
                    }

                }else{
                    SmAssignSubject::where('class_id', $request->class_id)->where('section_id', $request->section_id)->delete();
               
                    $i = 0;
                    if (! empty($request->subjects)) {
            
                        foreach ($request->subjects as $subject) {
                                
                            if (!empty($subject)) {
                                $assign_subject = new SmAssignSubject();
                                $assign_subject->class_id = $request->class_id;
                                $assign_subject->section_id = $request->section_id;
                                $assign_subject->subject_id = $subject;
                                $assign_subject->teacher_id = $request->teachers[$i];
                                $assign_subject->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                                $assign_subject->academic_id = getAcademicId();
                                $assign_subject->school_id = Auth::user()->school_id;
                                $result =  $assign_subject->save();
                                event(new CreateClassGroupChat($assign_subject));
                                $i++;
                            }
                        }
                    }
             }
            }


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Record Updated Successfully');
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

            // return redirect()->back()->with('message-success', 'Record Updated Successfully');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function assignSubjectFind(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required'
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
            $assign_subjects = SmAssignSubject::where('class_id', $request->class)->where('section_id', $request->section)->get();
            $subjects = SmSubject::get();
            $teachers = SmStaff::status()->where('role_id', 4)->get();
            $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            if ($assign_subjects->count() == 0) {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendError('No Result Found');
                }
                Toastr::error('No Result Found', 'Failed');
                return redirect()->back();
                // return redirect()->back()->with('message-danger', 'No Result Found');
            } else {
                $class_id = $request->class;

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['classes'] = $classes->toArray();
                    $data['assign_subjects'] = $assign_subjects->toArray();
                    $data['teachers'] = $teachers->toArray();
                    $data['subjects'] = $subjects->toArray();
                    $data['class_id'] = $class_id;
                    return ApiBaseMethod::sendResponse($data, null);
                }
                return view('backEnd.academics.assign_subject', compact('classes', 'assign_subjects', 'teachers', 'subjects', 'class_id'));
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function ajaxSelectSubject(Request $request)
    {


        try {
            $subject_all = SmAssignSubject::where('class_id', '=', $request->class)->where('section_id', $request->section)->distinct('subject_id')->where('school_id', Auth::user()->school_id)->get();
            $students = [];
            foreach ($subject_all as $allSubject) {
                $students[] = SmSubject::find($allSubject->subject_id);
            }

            return response()->json([$students]);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Error msg'], 404);
        }
    }
}
