<?php

namespace App\Http\Controllers\Admin\Homework;

use App\User;
use Response;
use ZipArchive;
use App\SmClass;
use App\SmStaff;
use App\SmParent;
use App\SmStudent;
use App\SmHomework;
use App\SmClassSection;
use App\SmNotification;
use App\SmAssignSubject;
use App\SmHomeworkStudent;
use Illuminate\Http\Request;
use App\SmUploadHomeworkContent;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\HomeworkNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Admin\Homework\SmHomeworkRequest;
use App\Http\Requests\Admin\Homework\SearchHomeworkRequest;
use App\Http\Requests\Admin\Homework\SearchHomeworkEvaluationRequest;

class SmHomeworkController extends Controller
{
    public function __construct()
	{
        $this->middleware('PM');
      
	}

    public function homeworkList(Request $request)
    {
        try {
            set_time_limit(900);
               $homeworkLists = SmHomework::query();
            if (teacherAccess()) {
                $homeworkLists = SmHomework::where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->orderby('id','DESC')
                ->get();

            } else {
                $homeworkLists = SmHomework::where('academic_id', getAcademicId())
                ->where('school_id',Auth::user()->school_id)
                ->where('created_by',Auth::user()->id)
                ->orderby('id','DESC')
                ->get();
            }
            
        
            
            if (teacherAccess()) {
                $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                $classes= $teacher_info->classes;
            } else {
                $classes = SmClass::get();
            }

            return view('backEnd.homework.homeworkList', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchHomework(SearchHomeworkRequest $request)
    {

        try {

            $homeworkLists = SmHomework::query()->with('classes','sections','subjects','users');
           
            $homeworkLists->where('class_id', $request->class_id);

            if ($request->section_id != "") {
                $homeworkLists->where('section_id', $request->section_id);
            }
            if ($request->subject_id != "") {
                $homeworkLists->where('subject_id', $request->subject_id);
            }
            if (Auth::user()->role_id == 1) {
                $homeworkLists = $homeworkLists->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->orderby('id','DESC')
                                ->get();

            } else {
                $homeworkLists = $homeworkLists->where('academic_id', getAcademicId())
                                ->where('school_id',Auth::user()->school_id)
                                ->where('created_by',Auth()->user()->id)
                                ->orderby('id','DESC')
                                ->get();
            }
            $classes = SmClass::where('active_status', '=', '1')
                    ->where('academic_id', getAcademicId())
                    ->where('school_id',Auth::user()->school_id)
                    ->get();

            return view('backEnd.homework.homeworkList', compact('homeworkLists', 'classes'));
        } catch (\Exception $e) {
          
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addHomework()
    {
        try {
            if (teacherAccess()) {
                $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
               $classes= $teacher_info->classes;
        } else {
               $classes = SmClass::get();
        }
            return view('backEnd.homework.addHomework', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function saveHomeworkData(SmHomeworkRequest $request)
    {
  

        try {
            
            $destination='public/uploads/homeworkcontent/';
            $sections=[];
            foreach($request->section_id as $section){
                $sections[]=$section;
                $homeworks = new SmHomework();
                $homeworks->class_id = $request->class_id;
                $homeworks->section_id = $section;
                $homeworks->subject_id = $request->subject_id;
                $homeworks->homework_date = date('Y-m-d', strtotime($request->homework_date));
                $homeworks->submission_date = date('Y-m-d', strtotime($request->submission_date));
                $homeworks->marks = $request->marks;
                $homeworks->description = $request->description;
                $homeworks->file =fileUpload($request->homework_file,$destination);
                $homeworks->created_by = Auth()->user()->id;
                $homeworks->school_id = Auth::user()->school_id;
                $homeworks->academic_id = getAcademicId();
                $homeworks->save();
            }
            
            $students = SmStudent::where('class_id', $request->class_id)
                        ->whereIn('section_id', $sections)
                        ->get();

            foreach ($students as $student) {

                $notification = new SmNotification;
                $notification->user_id = $student->user_id;
                $notification->role_id = 2;
                $notification->date = date('Y-m-d');
                $notification->message = app('translator')->get('common.homework_assigned');
                $notification->school_id = Auth::user()->school_id;
                $notification->academic_id = getAcademicId();
                $notification->save();
                
                try{
                    $user=User::find($student->user_id);
                    Notification::send($user, new HomeworkNotification($notification));
                }catch (\Exception $e) {
                    Log::info($e->getMessage());
                }

                $parent = SmParent::find($student->parent_id);
                        $notification = new SmNotification();
                        $notification->role_id = 3;
                        $notification->message = app('translator')->get('common.homework_assigned_child');
                        $notification->date = date('Y-m-d');
                        $notification->user_id = $parent->user_id;
                        $notification->url = "homework-list";
                        $notification->school_id = Auth::user()->school_id;
                        $notification->academic_id = getAcademicId();
                        $notification->save();

                        try{
                            $user=User::find($parent->user_id);
                            Notification::send($user, new HomeworkNotification($notification));
                        }catch (\Exception $e) {
                            Log::info($e->getMessage());
                        }
            }

            Toastr::success('Operation successful', 'Success');
            return redirect('homework-list');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function downloadHomeworkData($id,$student_id)
    {
        try{
            $hwContent=SmUploadHomeworkContent::where('homework_id',$id)->where('student_id',$student_id)->get();           


            $file_paths=[];
            foreach ($hwContent as $key => $files_row) {
                $only_files=json_decode($files_row->file);
                foreach ($only_files as $second_key => $upload_file_path) {
                    $file_paths[]= $upload_file_path;
                }
            }
            if (count($file_paths)==1) {
                return Response::download($file_paths[0]);
            }else{

                $zip_file_name = str_replace(' ', '_',time().'.zip'); // Name of our archive to download
    
                $new_file_array=[];
                foreach ($file_paths as $key => $file) {
                    $file_name_array=explode('/',$file);
                    $file_original=$file_name_array[array_key_last($file_name_array)];
                    $new_file_array[$key]['path']=$file;
                    $new_file_array[$key]['name']=$file_original;
                    
                }
                $public_dir=public_path('uploads/homeworkcontent');
                $zip = new ZipArchive;
                if ($zip->open($public_dir . '/' . $zip_file_name, ZipArchive::CREATE) === TRUE) {    
                    // Add Multiple file   
                    foreach($new_file_array as $key=> $file) {
                        $zip->addFile($file['path'], @$file['name']);
                    }      
                    $zip->close();
                }

                $zip_file_url=asset('public/uploads/homeworkcontent/'.$zip_file_name);
                session()->put('homework_zip_file', $zip_file_name);
                return Redirect::to($zip_file_url);
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function evaluationHomework(Request $request, $class_id, $section_id, $homework_id)
    {

        try {
            $homeworkDetails = SmHomework::where('class_id', $class_id)
                            ->where('section_id', $section_id)
                            ->where('id', $homework_id)                           
                            ->first();

            $students = SmStudent::where('class_id', $class_id)
                        ->where('section_id', $section_id)
                        ->get();

            return view('backEnd.homework.evaluationHomework', compact('homeworkDetails', 'students', 'homework_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function saveHomeworkEvaluationData(Request $request)
    {

        try {
            if (!$request->student_id) {
                Toastr::error('Their are no students selected', 'Failed');
                return redirect()->back();
            } else {
                $student_idd = count($request->student_id);
                if ($student_idd > 0) {
                    for ($i = 0; $i < $student_idd; $i++) {
                         if (checkAdmin()) {
                            SmHomeworkStudent::where('student_id', $request->student_id[$i])
                            ->where('homework_id', $request->homework_id)
                            ->delete();
                        }else{
                             SmHomeworkStudent::where('student_id', $request->student_id[$i])
                             ->where('homework_id', $request->homework_id)
                             ->where('school_id',Auth::user()->school_id)
                             ->delete();
                        }
                        $homeworkstudent = new SmHomeworkStudent();
                        $homeworkstudent->homework_id = $request->homework_id;
                        $homeworkstudent->student_id = $request->student_id[$i];
                        $homeworkstudent->marks = $request->marks[$i];
                        $homeworkstudent->teacher_comments = $request->teacher_comments[$request->student_id[$i]];
                        $homeworkstudent->complete_status = $request->homework_status[$request->student_id[$i]];
                        $homeworkstudent->created_by = Auth()->user()->id;
                        $homeworkstudent->school_id = Auth::user()->school_id;
                        $homeworkstudent->academic_id = getAcademicId();
                        $results = $homeworkstudent->save();
                    }
                    $homeworks = SmHomework::find($request->homework_id);
                    $homeworks->evaluation_date = date('Y-m-d');
                    $homeworks->evaluated_by = Auth()->user()->id;
                    $homeworks->update();
                }
                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('homework-list');
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function evaluationReport(Request $request)
    {
        try {
            if (teacherAccess()) {
                $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                $classes= $teacher_info->classes;
            } else {
                $classes = SmClass::get();
            }
            return view('backEnd.reports.evaluation', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchEvaluation(SearchHomeworkEvaluationRequest $request)
    {
        
 

        try {
          
                $homeworkLists = SmHomework::query()->with('subjects','sections','classes','classes.classSections')->withCount('homeworkCompleted');
                //  ->with(array('user' => function($query) {
                //     $query->select('id','full_name');
                // }));
                if($request->class_id !=null){
                  $homeworkLists ->where('class_id', '=', $request->class_id);
                }
                if($request->subject_id !=null){
                    $homeworkLists->where('subject_id', '=', $request->subject_id);
                }

                if($request->section_id !=null){

                    $homeworkLists->where('section_id', '=', $request->section_id);
                }
                if (teacherAccess()) {
                    $homeworkLists->where('created_by',Auth::user()->id);
                }
                
               $homeworkLists=$homeworkLists->take(10)->get();
            

                if (teacherAccess()) {
                    $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                    $classes= $teacher_info->classes;
                } else {
                    $classes = SmClass::get();
                }
            return view('backEnd.reports.evaluation', compact('homeworkLists', 'classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchEvaluationData(Request $request){


        $homeworkLists = SmHomework::query()->with('subjects','sections','classes','classes.classSections')->withCount('homeworkCompleted',);
        //  ->with(array('user' => function($query) {
        //     $query->select('id','full_name');
        // }));
        if($request->class_id !=null){
          $homeworkLists ->where('class_id', '=', $request->class_id);
        }
        if($request->subject_id !=null){
            $homeworkLists->where('subject_id', '=', $request->subject_id);
        }

        if($request->section_id !=null){

            $homeworkLists->where('section_id', '=', $request->section_id);
        }
        if (teacherAccess()) {
            $homeworkLists->where('created_by',Auth::user()->id);
        }
       $homeworkLists=$homeworkLists;
    

        if (teacherAccess()) {
            $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
            $classes= $teacher_info->classes;
        } else {
            $classes = SmClass::get();
        }
        
        return Datatables::of($homeworkLists)
 
                ->addColumn('action', function($row){
                    $btn = '<div class="dropdown">
                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">'.app('translator')->get('common.select').'</button>

                                <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" target="_blank" href="'.route('student_view', [$row->id]).'">'.app('translator')->get('common.view').'</a>'.
                                        (userPermission(66) === true ? '<a class="dropdown-item" href="'.route('student_edit', [$row->id]).'">'.app('translator')->get('common.edit').'</a>' : '').
                                    
                                        (userPermission(67) === true ? (Config::get('app.app_sync') ? '<span  data-toggle="tooltip" title="Disabled For Demo "><a  class="dropdown-item" href="#"  >'.app('translator')->get('common.disable').'</a></span>' :
                                        '<a onclick="deleteId('.$row->id.');" class="dropdown-item" href="#" data-toggle="modal" data-target="#deleteStudentModal" data-id="'.$row->id.'"  >'.app('translator')->get('common.disable').'</a>') : '' ).
                                    
                                '</div>
                            </div>';

                        return $btn;
                })
                
                ->rawColumns(['action'])
                ->make(true);

                // return view('backEnd.reports.evaluation', compact('homeworkLists', 'classes')); 
    }
    public function viewEvaluationReport($homework_id)
    {

        try {
            $homeworkDetails = SmHomework::where('id', $homework_id)->first();
            $homework_students = SmHomeworkStudent::with('studentInfo','users','homeworkDetail')->where('homework_id', $homework_id)->get();

            return view('backEnd.reports.viewEvaluationReport', compact('homeworkDetails', 'homework_students'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function homeworkEdit($id)
    {
        try {
            $homeworkList = SmHomework::find($id);
        if (teacherAccess()) {
                $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
                $classes= $teacher_info->classes;
        } else {
               $classes = SmClass::get();
        }
            $sections = SmClassSection::where('class_id', '=', $homeworkList->class_id)->get();

            $subjects = SmAssignSubject::where('class_id', $homeworkList->class_id)
                        ->where('section_id', $homeworkList->section_id)
                        ->get();

            return view('backEnd.homework.homeworkEdit', compact('homeworkList', 'classes', 'sections', 'subjects'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function homeworkUpdate(SearchHomeworkEvaluationRequest $request)
    {


        try {
            $destination ="public/uploads/homeworkcontent/";
            $homeworks = SmHomework::find($request->id);
            $homeworks->class_id = $request->class_id;
            $homeworks->section_id = $request->section_id;
            $homeworks->subject_id = $request->subject_id;
            $homeworks->homework_date = date('Y-m-d', strtotime($request->homework_date));
            $homeworks->submission_date = date('Y-m-d', strtotime($request->submission_date));
            $homeworks->marks = $request->marks;
            $homeworks->description = $request->description;
           
            $homeworks->file = fileUpdate($homeworks->file,$request->homework_file,$destination);
         
            $homeworks->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('homework-list');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function homeworkDelete($id)
    {
        try{
        $tables = \App\tableList::getTableList('homework_id', $id);

        try {
            if ($tables==null) {
                 $homework = SmHomework::find($id);
           
                Session::put('path', $homework);
                $result = SmHomework::destroy($id);
                if ($result) {
                    $data = Session::get('path');
                    if ($data->file != "") {
                        $path = url('/') . '/public/uploads/homework/' . $homework->file;
                        if (file_exists($path)) {}
                    }

                } 
                Toastr::success('Operation successful', 'Success');
                return redirect('homework-list');
            } else {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }


        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
            Toastr::error($msg, 'Failed');
            return redirect()->back();
          }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}