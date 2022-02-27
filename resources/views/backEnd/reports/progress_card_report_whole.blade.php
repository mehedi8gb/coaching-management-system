@extends('backEnd.master')
@section('mainContent')
<style type="text/css">
    .single-report-admit table tr th {

    border: 1px solid #a2a8c5 !important;
    vertical-align: middle;
}
    .single-report-admit table tr td {
        
    border: 1px solid #a2a8c5 !important;
}
</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.progress_card_report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.progress_card_report')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area mb-40">
    <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.select_criteria') </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                @if(session()->has('message-success') != "")
                    @if(session()->has('message-success'))
                    <div class="alert alert-success">
                        {{ session()->get('message-success') }}
                    </div>
                    @endif
                @endif
                 @if(session()->has('message-danger') != "")
                    @if(session()->has('message-danger'))
                    <div class="alert alert-danger">
                        {{ session()->get('message-danger') }}
                    </div>
                    @endif
                @endif
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'progress_card_report', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="col-lg-4 mt-30-md">
                                <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class">
                                    <option data-display="@lang('lang.select_class') *" value="">@lang('lang.select_class') *</option>
                                    @foreach($classes as $class)
                                    <option value="{{$class->id}}" {{isset($class_id)? ($class_id == $class->id? 'selected':''):''}}>{{$class->class_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('class'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('class') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-4 mt-30-md" id="select_section_div">
                                <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section">
                                    <option data-display="@lang('lang.select_section') *" value="">@lang('lang.select_section') *</option>
                                </select>
                                @if ($errors->has('section'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('section') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-4 mt-30-md" id="select_student_div">
                                <select class="w-100 bb niceSelect form-control{{ $errors->has('student') ? ' is-invalid' : '' }}" id="select_student" name="student">
                                    <option data-display="@lang('lang.select_student') *" value="">@lang('lang.select_student') *</option>
                                </select>
                                @if ($errors->has('student'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('student') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-12 mt-20 text-right">
                                <button type="submit" class="primary-btn small fix-gr-bg">
                                    <span class="ti-search"></span>
                                    @lang('lang.search')
                                </button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
</section>

@if(isset($is_result_available))
@php 
    $generalSetting= App\SmGeneralSettings::find(1);
    if(!empty($generalSetting)){
        $school_name =$generalSetting->school_name;
        $site_title =$generalSetting->site_title;
        $school_code =$generalSetting->school_code;
        $address =$generalSetting->address;
        $phone =$generalSetting->phone; 
    }

@endphp
    <section class="student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.progress_card_report')</h3>
                    </div>
                </div>
                <div class="col-lg-8 pull-right mt-0">

                        <div class="print_button pull-right">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'progress-card/print', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student', 'target' => '_blank']) }}

                            <input type="hidden" name="class_id" value="{{$class_id}}">
                            <input type="hidden" name="section_id" value="{{$section_id}}">
                            <input type="hidden" name="student_id" value="{{$student_id}}">
                            
                            
                            <button type="submit" class="primary-btn small fix-gr-bg"><i class="ti-printer"> </i> @lang('lang.print')
                            </button>
                           {{ Form::close() }}
                        </div>

                    </div>
            </div>
            <div class="row">
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
                              {{-- {{ dd($studentDetails) }}  --}}
                                        <div class="card-body">
                                            <div>
                                                    {{-- {{ dd($studentDetails)}} --}}
                                                    
                                                <h3>{{  $studentDetails->full_name }}</h3>
                                                
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <p class="mb-0">
                                                            @lang('lang.academic_year') : <span class="primary-color fw-500">{{$generalSetting->session_year}}</span>
                                                        </p>
                                                        <p class="mb-0">
                                                                @lang('lang.roll') : <span class="primary-color fw-500">{{$studentDetails->roll_no}}</span>
                                                            </p>

                                                        {{-- <p class="mb-0">
                                                            @lang('lang.position_in_class') : <span class="primary-color fw-500">1st</span>
                                                        </p> --}}
                                                        
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <p class="mb-0">
                                                            @lang('lang.class') : <span class="primary-color fw-500">{{ $studentDetails->class_name }}</span>
                                                        </p>
                                                        <p class="mb-0">
                                                                @lang('lang.admission') @lang('lang.no') : <span class="primary-color fw-500">{{$studentDetails->admission_no}}</span>
                                                            </p>
                                                        {{-- <p class="mb-0">
                                                            @lang('lang.section') : <span class="primary-color fw-500">{{ $studentDetails->section_name }}</span>
                                                        </p> --}}

                                                        
                                                    </div>

                                                    <div class="col-lg-3">
                                                            <p class="mb-0">
                                                                    @lang('lang.section') : <span class="primary-color fw-500">{{ $studentDetails->section_name }}</span>
                                                                </p>
                                                   
                                                        {{-- <p class="mb-0">
                                                            @lang('lang.position_in_class') : <span class="primary-color fw-500">CSE04506185</span>
                                                        </p> --}}
                                                    </div>
                                                </div>
                                            </div>


                                            <table class="w-100 mt-30 mb-20 table table-bordered">
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
                                            {{-- Start Test --}}
                                            
                                            {{-- End Test --}}
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

            

@endsection
