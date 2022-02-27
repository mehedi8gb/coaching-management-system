<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\SmClass;
use App\SmSection;
use App\SmStudent;
use App\SmSubject;
use App\SmVehicle;
use App\SmRoomList;
use App\SmClassSection;
use App\SmAssignSubject;
use App\SmAssignVehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Scopes\StatusAcademicSchoolScope;

class SmStudentAjaxController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
     
    }
    public function ajaxSectionSibling(Request $request)
    {
        try {
            $sectionIds = SmClassSection::where('class_id', '=', $request->id)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

            $sibling_sections = [];
            foreach ($sectionIds as $sectionId) {
                $sibling_sections[] = SmSection::find($sectionId->section_id);
            }
            return response()->json([$sibling_sections]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }
    public function ajaxSiblingInfo(Request $request)
    {
        try {

            $siblings=SmStudent::query();
            
            if ($request->id != "") {
                $siblings->where('id', '!=', $request->id);
            } 
            $siblings = $siblings->status()->withoutGlobalScope(StatusAcademicSchoolScope::class)->get();
            
            return response()->json($siblings);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxSiblingInfoDetail(Request $request)
    {
        try {
            $sibling_detail = SmStudent::find($request->id);
            $parent_detail =  $sibling_detail->parents;
            return response()->json([$sibling_detail, $parent_detail]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxGetVehicle(Request $request)
    {
        try {
            $school_id = 1;
            if(Auth::check()){
                $school_id = Auth::user()->school_id;
            } else if(app()->bound('school')){
                $school_id = app('school')->id;
            }
            $vehicle_detail = SmAssignVehicle::where('route_id', $request->id)->where('school_id', $school_id)->first();
            $vehicles = explode(',', $vehicle_detail->vehicle_id);
            $vehicle_info = [];
            foreach ($vehicles as $vehicle) {
                $vehicle_info[] = SmVehicle::find($vehicle[0]);
            }
            return response()->json([$vehicle_info]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxVehicleInfo(Request $request)
    {
        try {
            $vehivle_detail = SmVehicle::find($request->id);
            return response()->json([$vehivle_detail]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxRoomDetails(Request $request)
    {
        try {
            $school_id = 1;
            if(Auth::check()){
                $school_id = Auth::user()->school_id;
            } else if(app()->bound('school')){
                $school_id = app('school')->id;
            }

            $room_details = SmRoomList::where('dormitory_id', '=', $request->id)->where('school_id', $school_id)->get();
            $rest_rooms = [];
            foreach ($room_details as $room_detail) {
                $count_room = SmStudent::where('room_id', $room_detail->id)->count();
                if ($count_room < $room_detail->number_of_bed) {
                    $rest_rooms[] = $room_detail;
                }
            }
            return response()->json([$rest_rooms]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxGetRollId(Request $request)
    {

        try {
            $max_roll = SmStudent::where('class_id', $request->class)
                        ->where('section_id', $request->section)
                        ->where('school_id', Auth::user()->school_id)
                        ->max('roll_no');
            // return $max_roll;
            if ($max_roll == "") {
                $max_roll = 1;
            } else {
                $max_roll = $max_roll + 1;
            }
            return response()->json([$max_roll]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxGetRollIdCheck(Request $request)
    {
        try {
            $roll_no = SmStudent::where('class_id', $request->class)
                    ->where('section_id', $request->section)
                    ->where('roll_no', $request->roll_no)
                    ->where('school_id', Auth::user()->school_id)
                    ->get();
            return response()->json($roll_no);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }
    
    public function ajaxSubjectClass(Request $request)
    {
        try {
    
            $subjects = SmAssignSubject::query();
            if (teacherAccess()) {
                $subjects->where('teacher_id',Auth::user()->staff->id) ;
            }
            $subjectIds=$subjects->where('class_id', '=',$request->id)->groupBy('subject_id')
            ->get()->pluck(['subject_id'])->toArray();
            
 
            $subjects=SmSubject::whereIn('id',$subjectIds)->get(['id','subject_name']);
            
            return response()->json([$subjects]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    
    public function ajaxStudentPromoteSection(Request $request)
    {
        // $sectionIds = SmClassSection::where('class_id', '=', $request->id)->get();
        if (teacherAccess()) {
            $sectionIds = SmAssignSubject::where('class_id', '=', $request->id)
            ->where('teacher_id', Auth::user()->staff->id)
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->groupby(['class_id','section_id'])
            ->withoutGlobalScope(StatusAcademicSchoolScope::class)
            ->get();
        } else {
            $sectionIds = SmClassSection::where('class_id', '=', $request->id)
            ->where('school_id', Auth::user()->school_id)->withoutGlobalScope(StatusAcademicSchoolScope::class)->get();
        }
        $promote_sections = [];
        foreach ($sectionIds as $sectionId) {
            $promote_sections[] = SmSection::where('id', $sectionId->section_id)->withoutGlobalScope(StatusAcademicSchoolScope::class)->first(['id','section_name']);
        }

        return response()->json([$promote_sections]);
    }

    public function ajaxGetClass(Request $request)
    {
        $classes = SmClass::where('created_at', 'LIKE', $request->year . '%')->get();

        return response()->json([$classes]);
    }

    
    public function ajaxSelectStudent(Request $request)
    {
        $students = SmStudent::where('section_id', $request->section);

        if ($request->class){
            $students = $students->where('class_id', $request->class);
        }

        $students = $students->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get(['id','full_name','user_id']);

        return response()->json([$students]);
    }
    public function ajaxPromoteYear(Request $request)
    {
        $classes = SmClass::where('academic_id', $request->year)
                ->where('school_id',Auth::user()->school_id)
                ->withOutGlobalScope(StatusAcademicSchoolScope::class)
                ->get();

        return response()->json([$classes]);
    }

    public function ajaxSectionStudent(Request $request)
    {
        try {
            if (teacherAccess()) {
                $sectionIds = SmAssignSubject::where('class_id', '=', $request->id)
                            ->where('teacher_id',Auth::user()->staff->id)               
                            ->where('school_id', Auth::user()->school_id)
                            ->groupby(['class_id','section_id'])
                            ->get();
            } else {
                $sectionIds = SmClassSection::where('class_id', '=', $request->id)               
                            ->where('school_id', Auth::user()->school_id)
                            ->get();
            }
            $sections = [];
            foreach ($sectionIds as $sectionId) {
                $sections[] = SmSection::where('id',$sectionId->section_id)->select('id','section_name')->first();
            }
            return response()->json([$sections]);
        } catch (\Exception $e) {
            return response()->json("", 404);
        }
    }

    public function ajaxSubjectSection(Request $request)
    {
        // $sectionIds = SmClassSection::where('class_id', '=', $request->id)->get();
        if (teacherAccess()) {
            $sectionIds = SmAssignSubject::where('class_id', '=', $request->class_id)
            ->where('subject_id', '=', $request->subject_id)
            ->where('teacher_id',Auth::user()->staff->id)               
            ->where('school_id', Auth::user()->school_id)
            ->groupby(['class_id','section_id'])
            ->get();
        } else {
            $sectionIds = SmAssignSubject::where('class_id', '=', $request->class_id)
            ->where('subject_id', '=', $request->subject_id)               
            ->where('school_id', Auth::user()->school_id)
            ->groupby(['class_id','section_id'])
            ->get();
        }
        $promote_sections = [];
        foreach ($sectionIds as $sectionId) {
            $promote_sections[] = SmSection::where('id',$sectionId->section_id)->first(['id','section_name']);
        }

        return response()->json([$promote_sections]);
    }

}
