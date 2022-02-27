<!DOCTYPE html>
<html lang="en">
<head>
  <title>@lang('lang.official') @lang('lang.transcript') [{{ $studentDetails->full_name }}] </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/print/bootstrap.min.css"/>
  <script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/print/jquery.min.js"></script>
  <script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/print/bootstrap.min.js"></script>
</head> 
<style>
    body{ font-size: 12px;  background: black; font-family: "Poppins", sans-serif; }
   .myBorderTable td,.myBorderTable th{
        text-align: center;
        padding: 2px !important;
        font-size: 12px;
        border: 1px solid #ddd !important;
        font-family: "Poppins", sans-serif;
    }
    
   
    h4{
        text-align: center !important;
        font-family: "Poppins", sans-serif;
    }
     .transcript-heading{
        border: 1px solid #dddddd;
        text-align: left !important;
        font-size: 12px;
        padding: 5px;
        font-family: "Poppins", sans-serif;
    }
.col-md-6{
    width:48% !important;
    float: left !important;  
    overflow: hidden;
    flex: 0%; 
    padding: 5px;
    text-align: center;
    font-family: "Poppins", sans-serif;
    margin: 1%;
    margin-top: 0px;
}
 .container{
     background: white;
     min-height: auto;
     margin-top: 20px;
     margin-bottom: 20px;
     padding: 20px;
    font-family: "Poppins", sans-serif;
    min-height: 842px;
    min-width: 595px;
 }
</style>
 <body>

 
@php 
    $generalSetting= App\SmGeneralSettings::find(1);
    if(!empty($generalSetting)){
        $school_name =$generalSetting->school_name;
        $site_title =$generalSetting->site_title;
        $school_code =$generalSetting->school_code;
        $address =$generalSetting->address;
        $phone =$generalSetting->phone; 
    }
    $section = App\SmSection::find($studentDetails->section_id);
    $class = App\SmClass::find($studentDetails->class_id);
@endphp

<div class="container"> 
    <table  style="width: 100%; border: 0px;"> 
            <tr> 
                <td style="width: 30%">
                    <img class="logo-img" src="{{ url('/')}}/{{$generalSetting->logo }}" alt=""> 
                </td>
                <td style="text-align: left; width: 70%"> 
                    <h3 class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3> 
                    <p class="text-white mb-0"> {{isset($address)?$address:'Infix School Address'}} </p>
                </td> 
            </tr> 
    </table>
    <div class="container-fluid p-0">                                        
        <div class="row  mt-40 " style="margin-top:25px">
            <div class="col-lg-12 transcript-heading" style="text-align:center">
                <h4 class="text-center text-uppercase" style="text-align:center"> @lang('lang.official') @lang('lang.transcript') </h4>
            </div>
        </div>          
        <div class="row" style="margin-top:15px">
            <div class="col-lg-12 transcript-heading"> 
                <table style="width:100%">
                    <tr>
                        <td>
                            <strong>@lang('lang.student_name'):</strong> {{ $studentDetails->full_name }} <br>
                                @php  $mother=App\SmStudent::where('sm_students.id',$studentDetails->student_id)->join('sm_parents','sm_parents.id','=','sm_students.parent_id')->first();
                            @endphp
                            <strong>@lang('lang.mother_name'):</strong> {{ $mother->mothers_name }}<br>
                            <strong>@lang('lang.school_name'):</strong> {{ App\SmGeneralSettings::find(1)->school_name}}<br>
                        </td>
                        <td>
                            <strong>@lang('lang.transcript') @lang('lang.no'):</strong> {{ $studentDetails->admission_number }}<br>
                            <strong>@lang('lang.academic_year'): </strong> {{ App\YearCheck::getYear() }}<br>
                            <strong>@lang('lang.admission_no'):</strong> {{ $studentDetails->admission_number }}<br>
                        </td>
                        <td>
                            <strong>@lang('lang.class'):</strong> {{ $current_class->class_name }}<br>
                            <strong>@lang('lang.section') :</strong> {{ $current_section->section_name }}<br>
                            <strong>@lang('lang.date_of_birth'):</strong> {{ $studentDetails->date_of_birth != ""? App\SmGeneralSettings::DateConvater($studentDetails->date_of_birth):''}}
                    
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row" style="margin-top:15px">
            <div class="col-lg-12">
                <h4 class="text-center">@lang('lang.academic_records')</h4>
            </div>
        </div>
        <div class="row" style="margin-top:10px">
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
                            if ($is_mark_available == "") {
                                return redirect('session-student')->with('message-danger', 'Ops! Your result is not found! Please check mark register.');
                            }
                        }
                    }
                    $is_result_available = App\SmResultStore::where([['class_id', $class_id], ['section_id', $section_id], ['student_id', $student_id]])->get();
                @endphp
                @if ($is_result_available->count() > 0)  
                    <div class="col-md-6">     
                        <table style="width:100%">
                            <tr>
                                <td>
                                    <strong>@lang('lang.exam_terms'):</strong> 
                                        @php
                                            $exam=App\SmExamType::where('id',$is_mark_available->exam_type_id)->first();
                                        @endphp
                                        {{ $exam->title }}
                                </td>
                                <td>
                                        <strong>@lang('lang.roll'):</strong> {{ $studentDetails->previous_roll_number }}
                                </td>
                                <td>
                                        <strong>@lang('lang.class'):</strong> 
                                        @php
                                            $class=App\SmClass::where('id',$is_mark_available->class_id)->first();
                                        @endphp
                                        {{ $class->class_name }}
                                </td>
                                <td>
                                    <strong>@lang('lang.exam_result'):</strong>
                                        {{$is_mark_available->created_at != ""? App\SmGeneralSettings::DateConvater($is_mark_available->created_at):''}}

                                </td>
                            </tr>

                        </table>
                        <br>
                        <table  class="table  table-bordered myBorderTable" style="width:100%;">
                            <thead>
                                <tr style="text-align: center;vertical-align:middle">
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
                                        $sumation= 0;
                                @endphp
                                @foreach($subjects as $data)
                                <tr style="text-align: center; border:1px solid #ddd">
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
                                                <td>0.00</td>
                                                <td>0.00</td>
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
                                                        $sumation= $sumation + $mark_grade->gpa;
                                                    
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
                                                if($total_fail != 0){
                                                $grade_point_final=  '0.00';
                                                }else{
                                                    
                                                    if($sumation != 0){
                                                        if($subjects->count() != 0 ){
                                                        $grade_point_final= $sumation/$subjects->count();
                                                        }
                                                    }else {
                                                    $grade_point_final= '0.00';
                                                    }
                                                    $sumation= 0;
                                                }
                                                if($grade_point_final!= '0.00'){ 
                                                    $average_grade = App\SmMarksGrade::where([['from', '<=', $grade_point_final], ['up', '>=', $grade_point_final]])->first();
                                                    echo @$average_grade->grade_name;

                                                }else{
                                                    echo 'F';
                                                } 
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{$colspan / 2 - 1}}" class="text-center">@lang('lang.total') @lang('lang.gpa')</td>
                                    <td colspan="{{$colspan / 2 + 1}}" class="text-center">   {{number_format($grade_point_final, 2, ",", "")   }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        </div>
            <div  class="row mt-40" style="margin-top: 40px;">
            <table style="width:100%" style="margin-top:100px">
                <tr>
                    <td style="text-align:center">
                        <hr style="width: 50%; text-align:center">
                        @lang('lang.chairman_of_the_examination_board')
                        <br>
                        @lang('lang.signature')
                    </td>
                    <td style="text-align:center">
                        <hr style="width: 50%; text-align:center">
                            @lang('lang.head_of_students_affairs')
                                <br> @lang('lang.signature')
                    </td>
                </tr>
            </table>
            </div>
        </div> 
    </div>
</div> 

</body>
</html>
