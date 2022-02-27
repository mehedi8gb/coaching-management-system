@extends('backEnd.master')
@section('mainContent')
<style type="text/css">
    .single-report-admit table tr th {

    border: 1px solid #a2a8c5 !important;
    vertical-align: middle;
    text-align: center !important;
}
    .single-report-admit table tr td {
        
    border: 1px solid #a2a8c5 !important;
    text-align: center !important;
}
</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.result') @lang('lang.archive') </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.report')</a>
                <a href="{{url('results-archive')}}">@lang('lang.result')  @lang('lang.archive')  </a> 
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.select') @lang('criteria') </h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
               
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'session_student', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                            <div class="col-lg-6 mt-30-md">
                              
                            <div class="input-effect">
                                <input class="primary-input" type="text" name="admission_number" value="{{ isset($name)?$name:''}}">
                                <label>@lang('lang.admission') @lang('lang.number')</label>
                                <span class="focus-border"></span>
                            </div>
                        
                            </div> 
                            <div class="col-lg-6 mt-30-md text-right">
                                <button type="submit" class="primary-btn small fix-gr-bg">
                                    <span class="ti-search pr-2"></span>
                                    @lang('lang.search') 
                                </button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
@if (!empty($promotes))
    

     <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="single-report-admit">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex">
                                                <div>
                                                    <img class="logo-img" src="{{ $generalSetting->logo }}" alt="">
                                                     {{-- <img class="report-admit-img" src="{{asset('public/uploads/staff/std1.jpg')}}" alt=""> --}}
                                                </div>
                                                <div class="ml-30">
                                                    <h3 class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3>
                                                {{-- {{ dd($school_name) }} --}}
                                                <p class="text-white mb-0"> {{isset($address)?$address:'Infix School Address'}} </p>
                                                </div>
                                            </div>
                                            <div>
                                                {{-- <img class="report-admit-img" src="{{asset('public/uploads/staff/std1.jpg')}}" alt=""> --}}
                                                <img class="report-admit-img" src="{{asset($studentDetails->student_photo)}}" width="100" height="100" alt="">
                                            </div>
                                        </div> 
                                        <div class="card-body">
                                            <div class="white-box"> 
                                            
                                                <div class="row  mt-40 ">
                                                    <div class="col-lg-12">
                                                        <h2 class="text-center">!!  @lang('lang.student') @lang('lang.information')</h2>
                                                    </div>
                                                </div>
                                                <div class="row mt-20">
                                                    <div class="col-lg-6">
                                                        <strong>@lang('lang.name'):</strong> {{ $studentDetails->full_name }}
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <strong>@lang('lang.transcript') @lang('lang.none'):</strong> {{ $studentDetails->admission_number }}
                                                    </div>
                                                </div>
                                                <div class="row mt-20">

                                                    <div class="col-lg-6">
                                                        @php
                                                            $mother=App\SmStudent::where('sm_students.id',$studentDetails->student_id)->join('sm_parents','sm_parents.id','=','sm_students.parent_id')->first();
                                                        @endphp
                                                        <strong>@lang('lang.mother_name'):</strong> {{ $mother->mothers_name }}
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <strong>@lang('lang.date_of_birth'):</strong>
                                                        {{ $studentDetails->date_of_birth != ""? App\SmGeneralSettings::DateConvater($studentDetails->date_of_birth):''}}
                                                    </div>
                                                </div>
                                                




                                                <div class="row  mt-40 ">
                                                    <div class="offset-md-2 col-lg-4">
                                                        <strong>@lang('lang.name'):</strong> {{ $studentDetails->full_name }}<br>
                                                        <strong>@lang('lang.class'):</strong> {{ $current_class->class_name }}<br>
                                                        <strong>@lang('lang.section') :</strong> {{ $current_section->section_name }}<br>
                                                        <strong>@lang('lang.admission_no'):</strong> {{ $studentDetails->admission_number }}<br>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <strong>@lang('lang.transcript') @lang('lang.none'):</strong> 23423423<br>
                                                        <strong>@lang('lang.academic_year'):</strong> {{ $current_session->year }}<br>
                                                        <strong>@lang('lang.roll_number'):</strong> 102<br>
                                                    </div>

                                                </div>

                                                        @foreach ($promotes as $studentDetails)     
                                                        
                                                        @php
                                                            
                                                            $student_id = $studentDetails->student_id;
                                                            $class_id = $studentDetails->previous_class_id;
                                                            $section_id = $studentDetails->previous_section_id;
                                                            $year = $studentDetails->year;

                                                            $current_class = App\SmStudent::where('sm_students.id', $student_id)->join('sm_classes', 'sm_classes.id', '=', 'sm_students.class_id')->first();
                                                            $current_section = App\SmStudent::where('sm_students.id', $student_id)->join('sm_sections', 'sm_sections.id', '=', 'sm_students.section_id')->first();
                                                            $current_session = App\SmStudent::where('sm_students.id', $student_id)->join('sm_academic_years', 'sm_academic_years.id', '=', 'sm_students.session_id')->first();
                                                        
                                                            $exams = App\SmExam::where('active_status', 1)->where('class_id', $class_id)->where('section_id', $section_id)->get();

                                                            $exam_types = App\SmExamType::where('active_status', 1)->where('created_at', 'LIKE', '%' . App\YearCheck::getYear() . '%')->get();
                                                            $classes = App\SmClass::where('active_status', 1)->where('created_at', 'LIKE', '%' . App\YearCheck::getYear() . '%')->get();

                                                            

                                                            $exam_setup = App\SmExamSetup::where([['class_id', $class_id], ['section_id', $section_id]])->get();

                                                            $subjects = App\SmAssignSubject::where([['class_id', $class_id], ['section_id', $section_id]])->get();

                                                            $assinged_exam_types = [];
                                                            foreach ($exams as $exam) {
                                                                $assinged_exam_types[] = $exam->exam_type_id;
                                                            }

                                                            $assinged_exam_types = array_unique($assinged_exam_types);

                                                            foreach ($assinged_exam_types as $assinged_exam_type) {
                                                                foreach ($subjects as $subject) {
                                                                    $is_mark_available = App\SmResultStore::where([['class_id', $class_id], ['section_id', $section_id], ['student_id', $student_id], ['subject_id', $subject->subject_id], ['exam_type_id', $assinged_exam_type]])->first();

                                                                    // return $is_mark_available;
                                                                    if ($is_mark_available == "") {
                                                                        return redirect('session-student')->with('message-danger', 'Ops! Your result is not found! Please check mark register.');
                                                                    }
                                                                }
                                                            }

                                                            $is_result_available = App\SmResultStore::where([['class_id', $class_id], ['section_id', $section_id], ['student_id', $student_id]])->get();
                                                        
                                                            @endphp

                                                                        @if ($is_result_available->count() > 0)
         

                                     
                                            <div class="row  mt-40 ">
                                                    <div class="col-lg-3">
                                                        <strong>@lang('lang.exam_terms'):</strong> 
                                                        @php
                                                            $exam=App\SmExamType::where('id',$is_mark_available->exam_type_id)->first();
                                                        @endphp
                                                        {{ $exam->title }}
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <strong>@lang('lang.roll'):</strong> {{ $studentDetails->previous_roll_number }}
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <strong>@lang('lang.class'):</strong> 
                                                        @php
                                                            $class=App\SmClass::where('id',$is_mark_available->class_id)->first();
                                                        @endphp
                                                        {{ $class->class_name }}
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <strong>@lang('lang.exam_result'):</strong>
                                                        {{$is_mark_available->created_at != ""? App\SmGeneralSettings::DateConvater($is_mark_available->created_at):''}}

                                                    </div>
                                                </div>
                                     <table class="w-100  mt-10 mb-20 table table-bordered">
                                                <thead>
                                                    <tr style="text-align: center;">
                                                        <th rowspan="2">@lang('lang.subjects')</th>
                                                        @foreach($assinged_exam_types as $assinged_exam_type)
                                                        @php
                                                            $exam_type = App\SmExamType::examType($assinged_exam_type);
                                                        @endphp
                                                            <th colspan="2" style="text-align: center;">{{$exam_type->title}}</th>
                                                        @endforeach
                                                        <th rowspan="2">@lang('lang.total')</th>
                                                        <th rowspan="2">@lang('lang.grade')</th>
                                                        <th rowspan="2">@lang('lang.gpa')</th>
                                                    </tr>
                                                <tr  style="text-align: center;">
                                                    @foreach($assinged_exam_types as $assinged_exam_type)
                                                        <th>@lang('lang.marks')</th>
                                                        <th>@lang('lang.grade')</th>
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total_fail = 0;
                                                        $total_marks = 0;
                                                    @endphp
                                                    @foreach($subjects as $data)
                                                    <tr style="text-align: center">
                                                        <td>{{$data->subject !=""?$data->subject->subject_name:""}}</td>
                                                        <?php
                                                            $totalSumSub= 0;
                                                            $totalSubjectFail= 0;
                                                            $TotalSum= 0;
                                                        foreach($assinged_exam_types as $assinged_exam_type){

                                                            $mark_parts     =   App\SmAssignSubject::getNumberOfPart($data->subject_id, $class_id, $section_id, $assinged_exam_type);

                                                            $result         =   App\SmResultStore::GetResultBySubjectId($class_id, $section_id, $data->subject_id,$assinged_exam_type ,$student_id);
                                                            if(!empty($result)){
                                                                $final_results = App\SmResultStore::GetFinalResultBySubjectId($class_id, $section_id, $data->subject_id,$assinged_exam_type ,$student_id);
                                                            }
                                                            if($result->count()>0){
                                                                ?>
                                                                    <td>
                                                                    @php
                                                                        if($final_results != ""){
                                                                            echo $final_results->total_marks;
                                                                            $totalSumSub = $totalSumSub + $final_results->total_marks;
                                                                            $total_marks = $total_marks + $final_results->total_marks;
                                                                        }else{
                                                                            echo 0;
                                                                        }
                                                                    @endphp
                                                                </td>
                                                                    <td>
                                                                        @php
                                                                            if($final_results != ""){
                                                                                if($final_results->total_gpa_grade == "F"){
                                                                                    $totalSubjectFail++;
                                                                                    $total_fail++;
                                                                                }
                                                                                echo $final_results->total_gpa_grade;
                                                                            }else{
                                                                                echo '-';
                                                                            }
                                                                        @endphp
                                                                    </td>
                                                        <?php
                                                                }else{ ?>
                                                                    <td>0</td>
                                                                    <td>0</td>
                                                                <?php
                                                                }
                                                                    }
                                                                ?>
                                                                <td>{{$totalSumSub}}</td>
                                                                <td>
                                                                    @php
                                                                        if($totalSubjectFail > 0){
                                                                            echo 'F';
                                                                        }else{
                                                                            $totalSumSub = $totalSumSub / count($assinged_exam_types);
                                                                            $mark_grade = App\SmMarksGrade::where([['percent_from', '<=', $totalSumSub], ['percent_upto', '>=', $totalSumSub]])->first();
                                                                            echo @$mark_grade->grade_name;
                                                                        }
                                                                    @endphp
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        if($totalSubjectFail > 0){
                                                                            echo 'F';
                                                                        }else{
                                                                            $mark_grade = App\SmMarksGrade::where([['percent_from', '<=', $totalSumSub], ['percent_upto', '>=', $totalSumSub]])->first();
                                                                            echo @$mark_grade->gpa;
                                                                        }
                                                                    @endphp
                                                                </td>      
                                                    </tr>
                                                    @endforeach
                                                    @php
                                                        $colspan = 4 + count($assinged_exam_types) * 2;
                                                    @endphp
                                                    <tr>
                                                        <td colspan="{{$colspan / 2 - 1}}" class="text-center">@lang('lang.total') @lang('lang.marks')</td>
                                                        <td colspan="{{$colspan / 2 + 1}}" class="text-center">{{$total_marks}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="{{$colspan / 2 - 1}}" class="text-center">@lang('lang.total') @lang('lang.grade')</td>
                                                        <td colspan="{{$colspan / 2 + 1}}" class="text-center">
                                                            @php
                                                                if($total_fail != 0){ 
                                                                    echo 'F';
                                                                }else{
                                                                    $total_exam_subject = count($subjects) + count($assinged_exam_types);
                                                                    $average_mark = $total_marks / $total_exam_subject;
                                                                    $average_grade = App\SmMarksGrade::where([['percent_from', '<=', $totalSumSub], ['percent_upto', '>=', $totalSumSub]])->first();
                                                                    echo @$average_grade->grade_name;
                                                                }
                                                            @endphp
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="{{$colspan / 2 - 1}}" class="text-center">@lang('lang.total') @lang('lang.gpa')</td>
                                                        <td colspan="{{$colspan / 2 + 1}}" class="text-center">
                                                            @php
                                                                if($total_fail != 0){
                                                                    echo '0.00';
                                                                }else{
                                                                    $total_exam_subject = count($subjects) + count($assinged_exam_types);
                                                                    $average_mark = $total_marks / $total_exam_subject;
                                                                    $average_grade = App\SmMarksGrade::where([['percent_from', '<=', $totalSumSub], ['percent_upto', '>=', $totalSumSub]])->first();
                                                                    echo @$average_grade->gpa;
                                                                }
                                                            @endphp
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                         
                                    {{-- @else
                                    <div class="alert alert-danger">
                                         <span> Ops! Previous Result not found</span>
                                    </div> --}}
                                    @endif
                    @endforeach
                                 </div>
            </div>
        </div>
    </div>
    {{-- @else
        <div class="alert alert-danger">
            <span> Ops! Previous Result not found</span>
        </div> --}}
    @endif
</section> 

@endsection('mainContent')
