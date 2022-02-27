<?php
namespace App\Http\Controllers;
use Modules\RolePermission\Entities\InfixRole;
use App\Role;
use App\SmClass;
use App\SmStaff;
use App\SmStudent;
use App\YearCheck;
use App\ApiBaseMethod;
use App\SmContentType;
use App\SmNotification;
use Illuminate\Http\Request;
use App\SmTeacherUploadContent;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmTeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function uploadContentList(Request $request)
    {

        try {
            $contentTypes = SmContentType::where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();

            if (Auth()->user()->id == 1 || Auth()->user()->id == 5) {
                $uploadContents = SmTeacherUploadContent::where('available_for_admin', 1)->orWhere('created_by', Auth::user()->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            } else {
                $uploadContents = SmTeacherUploadContent::Where('created_by', Auth::user()->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            }

            $classes = SmClass::where('active_status', '=', '1')->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['contentTypes'] = $contentTypes->toArray();
                $data['uploadContents'] = $uploadContents->toArray();
                $data['classes'] = $classes->toArray();
                return ApiBaseMethod::sendResponse($data, 'Content uploaded successfully.');
            }
            return view('backEnd.teacher.uploadContentList', compact('contentTypes', 'classes', 'uploadContents'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function saveUploadContent(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (isset($request->available_for)) {
            foreach ($request->available_for as $value) {
                if ($value == 'student') {
                    if (!isset($request->all_classes)) {
                        $request->validate([
                            'content_title' => "required|max:200",
                            'content_type' => "required",
                            'upload_date' => "required",
                            'content_file' => "required|mimes:pdf,doc,docx,jpg,jpeg,png|max:10000",
                            'class' => "required",
                            'section' => "required",
                        ]);
                    } else {
                        $request->validate([
                            'content_title' => "required|max:200",
                            'content_type' => "required",
                            'upload_date' => "required",
                             'content_file' => "required|mimes:pdf,doc,docx,jpg,jpeg,png|max:10000",
                        ]);
                    }
                }
            }
        } else {
            $request->validate(
                [
                    'content_title' => "required:max:200",
                    'content_type' => "required",
                    'available_for' => 'required|array',
                    'upload_date' => "required",
                    'content_file' => "required|mimes:pdf,doc,docx,jpg,jpeg,png|max:10000",
                ],
                [
                    'available_for.required' => 'At least one checkbox required!',
                ]
            );
        }
        try {
            $fileName = "";

            if ($request->file('content_file') != "") {
                $file = $request->file('content_file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/upload_contents/', $fileName);
                $fileName = 'public/uploads/upload_contents/' . $fileName;
            }

            $y = '2012';
            $m = '2012';
            $d = '2012';
            $uploadContents = new SmTeacherUploadContent();
            $uploadContents->content_title = $request->content_title;
            $uploadContents->content_type = $request->content_type;
            $uploadContents->school_id = Auth::user()->school_id;

            foreach ($request->available_for as $value) {
                if ($value == 'admin') {
                    $uploadContents->available_for_admin = 1;
                }

                if ($value == 'student') {
                    if (isset($request->all_classes)) {
                        $uploadContents->available_for_all_classes = 1;
                    } else {
                        $uploadContents->class = $request->class;
                        $uploadContents->section = $request->section;
                    }
                }
            }

            $uploadContents->upload_date = date('Y-m-d', strtotime($request->upload_date));
            $uploadContents->description = $request->description;
            $uploadContents->upload_file = $fileName;
            $uploadContents->created_by = Auth()->user()->id;
            // $uploadContents->created_at = '2012-11-26 13:04:39';
            $results = $uploadContents->save();
            // return  $results;

            if ($request->content_type == 'as') {
                $purpose = 'assignment';
            } elseif ($request->content_type == 'st') {
                $purpose = 'Study Material';
            } elseif ($request->content_type == 'sy') {
                $purpose = 'Syllabus';
            } elseif ($request->content_type == 'ot') {
                $purpose = 'Others Download';
            }

            foreach ($request->available_for as $value) {
                if ($value == 'admin') {
                    $roles = InfixRole::where('id', '=', 1) /* ->where('id', '!=', 2)->where('id', '!=', 3)->where('id', '!=', 9) */->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
                    foreach ($roles as $role) {
                        $staffs = SmStaff::where('role_id', $role->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
                        foreach ($staffs as $staff) {
                            $notification = new SmNotification;
                            $notification->user_id = $staff->user_id;
                            $notification->role_id = $role->id;
                            $notification->school_id = Auth::user()->school_id;
                            if ($request->content_type == 'as') {
                                $notification->url = 'assignment-list';
                            } elseif ($request->content_type == 'st') {
                                $notification->url = 'study-metarial-list';
                            } elseif ($request->content_type == 'sy') {
                                $notification->url = 'syllabus-list';
                            } elseif ($request->content_type == 'ot') {
                                $notification->url = 'other-download-list';
                            }
                            $notification->date = date('Y-m-d');
                            $notification->message = $purpose . ' updated';
                            $notification->save();
                        }
                    }
                }
                if ($value == 'student') {
                    if (isset($request->all_classes)) {
                        $students = SmStudent::select('id', 'user_id')->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
                        foreach ($students as $student) {
                            $notification = new SmNotification;
                            $notification->user_id = $student->id;
                            $notification->role_id = 2;
                            $notification->school_id = Auth::user()->school_id;
                            if ($request->content_type == 'as') {
                                $notification->url = 'student-assignment';
                            } elseif ($request->content_type == 'st') {
                                $notification->url = 'student-study-material';
                            } elseif ($request->content_type == 'sy') {
                                $notification->url = 'student-syllabus';
                            } elseif ($request->content_type == 'ot') {
                                $notification->url = 'student-others-download';
                            }
                            $notification->date = date('Y-m-d');
                            $notification->message = $purpose . ' updated';
                            $notification->save();
                        }
                    } else {
                        $students = SmStudent::select('id')->where('class_id', $request->class)->where('section_id', $request->section)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
                        foreach ($students as $student) {
                            $notification = new SmNotification;
                            $notification->user_id = $student->id;
                            $notification->role_id = 2;
                            if ($request->content_type == 'as') {
                                $notification->url = 'student-assignment';
                            } elseif ($request->content_type == 'st') {
                                $notification->url = 'student-study-material';
                            } elseif ($request->content_type == 'sy') {
                                $notification->url = 'student-syllabus';
                            } elseif ($request->content_type == 'ot') {
                                $notification->url = 'student-others-download';
                            }
                            $notification->date = date('Y-m-d');
                            $notification->message = $purpose . ' updated';
                            $notification->school_id = Auth::user()->school_id;
                            $notification->save();
                        }
                    }
                }

            }

            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function assignmentList(Request $request)
    {
        
        try{
            $user = Auth()->user();
            if ($user->role_id == 1) {
                SmNotification::where('user_id', $user->id)->where('role_id', 1)->update(['is_read' => 1]);
            }
    
            if (Auth()->user()->id == 1) {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'as')->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            } else {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'as')->Where('created_by', Auth::user()->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            }
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($uploadContents->toArray(), 'null');
            }
    
            return view('backEnd.teacher.assignmentList', compact('uploadContents'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function studyMetarialList(Request $request)
    {
        
        try{
            if (Auth()->user()->id == 1) {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'st')->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            } else {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'st')->Where('created_by', Auth::user()->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            }
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($uploadContents->toArray(), 'null');
            }
            return view('backEnd.teacher.studyMetarialList', compact('uploadContents'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function syllabusList(Request $request)
    {
        try{
            if (Auth()->user()->id == 1) {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'sy')->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            } else {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'sy')->Where('created_by', Auth::user()->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            }
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($uploadContents->toArray(), 'null');
            }
            return view('backEnd.teacher.syllabusList', compact('uploadContents'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function otherDownloadList(Request $request)
    {
        
        try{
            if (Auth()->user()->id == 1) {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'ot')->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            } else {
                $uploadContents = SmTeacherUploadContent::where('content_type', 'ot')->Where('created_by', Auth::user()->id)->where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
            }
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($uploadContents->toArray(), 'null');
            }
            return view('backEnd.teacher.otherDownloadList', compact('uploadContents'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteUploadContent(Request $request, $id)
    {

        try{
            $uploadContent = SmTeacherUploadContent::find($id);
            if ($uploadContent->upload_file != "") {
                unlink($uploadContent->upload_file);
            }
            $result = $uploadContent->delete();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Content has been deleted successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
