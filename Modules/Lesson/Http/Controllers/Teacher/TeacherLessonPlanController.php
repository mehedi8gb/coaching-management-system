<?php

namespace Modules\Lesson\Http\Controllers\Teacher;
use App\SmClass;
use App\SmStaff;
use App\SmWeekend;
use Carbon\Carbon;
use App\SmClassTime;
use App\SmAssignSubject;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Lesson\Entities\SmLesson;
use Modules\Lesson\Entities\LessonPlanner;
use Modules\Lesson\Entities\SmLessonTopic;
use Illuminate\Contracts\Support\Renderable;

class TeacherLessonPlanController extends Controller
{
    public function teacherLessonPlan(Request $request)
    {

        

            try {
                  $this_week= $weekNumber = date("W");    
                $period =  CarbonPeriod::create(Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d'), Carbon::now()->endOfWeek(Carbon::FRIDAY)->format('Y-m-d'));
                $dates=[];
                foreach ($period as $date){
                        $dates[] = $date->format('Y-m-d');
                     
                        }  

                 $login_id = Auth::user()->id;
                 $teachers = SmStaff::where('active_status', 1)->where('user_id',$login_id)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->first();
               
              
                $class_times = SmClassTime::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->orderBy('period', 'ASC')->get();
                $teacher_id =$teachers->id;
                $sm_weekends = SmWeekend::where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
              
                return view('lesson::teacher.teacherLessonPlan', compact('dates','this_week','class_times', 'teacher_id', 'sm_weekends', 'teachers'));
            } catch (\Exception $e) {
               
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        
    }
    public function teacherLessonPlanOverview(){

        try{
          
        $teacher_info=SmStaff::where('user_id',Auth::user()->id)->first();
        $classes= SmAssignSubject::where('teacher_id',$teacher_info->id)
            ->join('sm_classes','sm_classes.id','sm_assign_subjects.class_id')
            ->where('sm_assign_subjects.academic_id', getAcademicId())
            ->where('sm_assign_subjects.active_status', 1)
            ->where('sm_assign_subjects.school_id',Auth::user()->school_id)
            ->select('sm_classes.id','class_name')
            ->distinct('sm_classes.id')
            ->get();

                $login_id = Auth::user()->id;
                $teacher = SmStaff::where('active_status', 1)->where('user_id',$login_id)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->first();
                $teachers=$teacher->id;

                $lessonPlanDetail=LessonPlanner::where('academic_id', getAcademicId())
                                ->where('school_id', Auth::user()->school_id)
                                ->get();

                $lessons=SmLesson::where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()->school_id)
                        ->get();
                $topics = SmLessonTopic::where('academic_id', getAcademicId())
                        ->where('school_id', Auth::user()
                        ->school_id)
                        ->get();

                
                return view('lesson::teacher.teacher_lesson_plan_overview',compact('lessonPlanDetail','lessons','topics','classes','teachers'));
        }catch(\Exception $e){
            
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

     public function searchTeacherLessonPlanOverview(Request $request)

    {
       
         $request->validate([
            'class' => 'required',          
           'section' => 'required',
           'subject' => 'required',

       ]);
       try{ 
        $total=LessonPlanner::lessonPlanner($request->teacher,$request->class,$request->section,$request->subject)              ->count();
        $completed_total=LessonPlanner::lessonPlanner($request->teacher,$request->class,$request->section,$request->subject)->where('completed_status','completed')->count();
        if($total>0){
        $percentage=$completed_total/$total*100;
        }else{
            $percentage=0;
        }                
        if($request->teacher !="" && $request->class != "" && $request->section != "" && $request->subject != ""){
        $lessonPlanner = LessonPlanner::lessonPlanner($request->teacher,$request->class,$request->section,$request->subject)->groupBy('lesson_detail_id')->get(); 
        $alllessonPlanner = LessonPlanner::lessonPlanner($request->teacher,$request->class,$request->section,$request->subject)->get(); 
                                        
        }
    

        
        $classes = SmClass::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

        $teachers = $request->teacher;
        return view('lesson::teacher.teacher_lesson_plan_overview',compact('total','completed_total','alllessonPlanner','lessonPlanner','classes','teachers','percentage'));

       }catch(\Exception $e){
           
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
       }
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function changeWeek($next_date)
    {
        try {
            $start_date=Carbon::parse($next_date)->addDay(1);
            $date = Carbon::parse($next_date)->addDay(1);

       
            $end_date=Carbon::parse($start_date)->addDay(7);
            $this_week= $week_number = $end_date->weekOfYear;
            
            $period = CarbonPeriod::create($start_date, $end_date);
            

            $dates=[];
            foreach ($period as $date){
                    $dates[] = $date->format('Y-m-d');
                 
             }
          
                 $login_id = Auth::user()->id;
                 $teachers = SmStaff::where('active_status', 1)->where('user_id',$login_id)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->first();
               
                $user = Auth::user();
                $class_times = SmClassTime::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->orderBy('period', 'ASC')->get();
                $teacher_id =$teachers->id;
                $sm_weekends = SmWeekend::where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
              
                return view('lesson::teacher.teacherLessonPlan', compact('dates','this_week','class_times', 'teacher_id', 'sm_weekends', 'teachers'));
            
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
             return redirect()->back();
        }

    }


    public function discreaseChangeWeek($pre_date)
    {
            try {
                $end_date=Carbon::parse($pre_date)->subDays(1);     
                $start_date=Carbon::parse($end_date)->subDays(6);
             
                $this_week= $week_number = $end_date->weekOfYear;
                ;
                $period = CarbonPeriod::create($start_date, $end_date);
                

                $dates=[];
                foreach ($period as $date){
                        $dates[] = $date->format('Y-m-d');
                     
                 }

                 $login_id = Auth::user()->id;
                 $teachers = SmStaff::where('active_status', 1)->where('user_id',$login_id)->where('role_id', 4)->where('school_id', Auth::user()->school_id)->first();
               
                $user = Auth::user();
                $class_times = SmClassTime::where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->orderBy('period', 'ASC')->get();
                $teacher_id =$teachers->id;
                $sm_weekends = SmWeekend::where('school_id', Auth::user()->school_id)->orderBy('order', 'ASC')->where('active_status', 1)->get();
              
                return view('lesson::teacher.teacherLessonPlan', compact('dates','this_week','class_times', 'teacher_id', 'sm_weekends', 'teachers'));
        } catch (\Exception $e) {
             Toastr::error('Operation Failed', 'Failed');
             return redirect()->back();
        }
       
       
           
    }

    
}
