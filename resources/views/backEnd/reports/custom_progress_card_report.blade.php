@extends('backEnd.master')

@section('mainContent')

<link rel="stylesheet" href="{{ asset('/') }}public/backEnd/css/custom_result/style.css">
<style>
    tr{
        border: 1px solid #351681;

    }
    table.meritList{
        width: 100%;
        border: 1px solid #351681;
    }
    table.meritList th{
        padding: 2px;
        text-transform: capitalize !important;
        font-size: 11px !important;  
        text-align: center !important;
        border: 1px solid #351681;
        text-align: center; 

    }
    table.meritList td{
        padding: 2px;
        font-size: 11px !important;
        border: 1px solid #351681;
        text-align: center !important;
    } 
 .single-report-admit table tr td { 
    padding: 5px 5px !important;

        border: 1px solid #351681;
} 
.single-report-admit table tr th { 
    padding: 5px 5px !important;
    vertical-align: middle;
        border: 1px solid #351681;
}
.main-wrapper {
     display: block !important ;  
}
#main-content {
    width: auto !important;
}
hr{
    margin: 0px;
}
.gradeChart tbody td{
        padding: 0;
        border: 1px solid #351681;
    }
    table.gradeChart{
        padding: 0px;
        margin: 0px;
        width: 60%;
        text-align: right; 
    }
    table.gradeChart thead th{
        border: 1px solid #000000;
        border-collapse: collapse;
        text-align: center !important;
        padding: 0px;
        margin: 0px;
        font-size: 9px;
    }
    table.gradeChart tbody td{
        border: 1px solid #000000;
        border-collapse: collapse;
        text-align: center !important;
        padding: 0px;
        margin: 0px;
        font-size: 9px;
    }


    #grade_table th{
        border: 1px solid black;
        text-align: center;
        background: #351681;
        color: white;
    }
    #grade_table td{
        color: black;
        text-align: center;
        border: 1px solid black;
    }
</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>Custom @lang('lang.progress_card_report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">Custom @lang('lang.progress_card_report')</a>
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'custom-progress-card', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
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

@if(isset($assigned_exam))
 @if ($assigned_exam->count()==3)
     
 
@php 
    $generalSetting= App\SmGeneralSettings::find(1);
    if(!empty($generalSetting)){
        $school_name =$generalSetting->school_name;
        $site_title =$generalSetting->site_title;
        $school_code =$generalSetting->school_code;
        $address =$generalSetting->address;
        $phone =$generalSetting->phone; 
        $email =$generalSetting->email; 
    }

@endphp
    <section class="student-details">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-30">Custom @lang('lang.progress_card_report')</h3>
                    </div>
                </div>
                <div class="col-lg-8 pull-right mt-0">

                        <div class="print_button pull-right">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'custom-progress-card/print', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student', 'target' => '_blank']) }}

                            <input type="hidden" name="class" value="{{$input_class}}">
                            <input type="hidden" name="section" value="{{$input_section}}">
                            <input type="hidden" name="student" value="{{$input_student}}">
                            
                            
                            <button type="submit" class="primary-btn small fix-gr-bg"><i class="ti-printer"> </i> Print
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
                                                           
                                                            <div class="offset-2 col-lg-2">
                                                            <img class="logo-img" src="{{ $generalSetting->logo }}" alt="">
                                                            </div>
                                                            <div class="col-lg-6 ml-30">
                                                                <h3 class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3> 
                                                                <p class="text-white mb-0"> {{isset($address)?$address:'Infix School Address'}} </p>
                                                                <p class="text-white mb-0">Email:  {{isset($email)?$email:'admin@demo.com'}} ,   Phone:  {{isset($phone)?$phone:'admin@demo.com'}} </p> 
                                                            </div>
                                                            <div class="offset-2">
                
                                                            </div>
                                                        </div>
                                                <div>
                                                    <img class="report-admit-img"  src="{{ file_exists(@$studentDetails->student_photo) ? asset($studentDetails->student_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" width="100" height="100" alt="{{asset($studentDetails->student_photo)}}">
                                                </div>
                                                
                                                
                                            </div>
                                        <div class="card-body">
                                                <div class="row">
                                                
                                                        <div class="col-lg-7 text-black"> 
                                                            <h3 style="border-bottm:1px solid #ddd; padding: 15px; text-align:center">Student Information</h3>

                                                            <h3>{{  $studentDetails->full_name }}</h3>
                                                            
                                                            <div class="row">

                                                                <div class="col-lg-3">
                                                                    <p class="mb-0 d-flex">
                                                                        @lang('lang.academic_year'): <span class="primary-color fw-500">{{$generalSetting->session_year}}</span>
                                                                    </p>
                                                                    <p class="mb-0 d-flex">
                                                                            @lang('lang.roll') : <span class="primary-color fw-500">{{$studentDetails->roll_no}}</span>
                                                                    </p>
                                                                    <p class="mb-0"> @lang('lang.admission') @lang('lang.no'):<span class="primary-color fw-500"> {{$studentDetails->admission_no}}</span> </p>
                                                                </div>

                                                                <div class="col-lg-3">
                                                                    <p class="mb-0">
                                                                        @lang('lang.class') : <span class="primary-color fw-500">{{ $studentDetails->class_name }}</span>
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
                                                        <div class="col-lg-4 text-black"> 
                                                            @php $marks_grade=DB::table('sm_marks_grades')->where('created_at', 'LIKE', '%' . App\YearCheck::getYear() . '%')->get(); @endphp
                                                                @if(@$marks_grade)
                                                                <table class="table  table-bordered table-striped text-black" id="grade_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>@lang('lang.starting')</th>
                                                                            <th>@lang('Ending')</th>
                                                                            <th>@lang('lang.gpa')</th>
                                                                            <th>@lang('lang.grade')</th>
                                                                            <th>@lang('lang.evalution')</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                            
                                                                    @foreach($marks_grade as $grade_d)
                                                                        <tr>
                                                                            <td>{{$grade_d->percent_from}}</td>
                                                                            <td>{{$grade_d->percent_upto}}</td>
                                                                            <td>{{$grade_d->gpa}}</td>
                                                                            <td>{{$grade_d->grade_name}}</td>
                                                                            <td class="text-left">{{$grade_d->description}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @endif
                                            
                                                        </div>
        
                                                    </div>
                                            <div>
                                                    
                                            </div>


                                           


                                            <h3 class="primary-color fw-500 text-center">Progress Card</h3>
                                            

                                            {{-- Start Test --}}
                                            <div class="table-responsive">

                                            
                                            <div class="student_marks_table">
                                                    <table class="custom_table">
                                                      <thead>
                                                        <tr>

                                                            @foreach ($assigned_exam as $key => $item)
                                                              @php
                                                                  $percentage=0;
                                                              @endphp
                                                                @if ($key==0)
                                                                    @php
                                                                        $percentage=$custom_result_setup->percentage1;
                                                                    @endphp
                                                                @endif
                                                                @if ($key==1)
                                                                    @php
                                                                    $percentage=$custom_result_setup->percentage2;
                                                                    @endphp
                                                                @endif
                                                                @if ($key==2)
                                                                    @php
                                                                    $percentage=$custom_result_setup->percentage3;
                                                                    @endphp
                                                                @endif
                                                                 <th colspan={{ $assign_subjects->count()*2+1 }}width" >{{$item->title}} {{ $percentage }}% </th>
                                                            @endforeach

                                                        <th colspan="{{$assign_subjects->count()*2+1}}" class="full_width" >result</th>

                                                        </tr>
                                                        <tr>
                                                            @foreach ($assign_subjects as $subject)
                                                                <td colspan="2">{{ $subject->subject_name }} </td>
                                                            @endforeach
                                                         

                                                          <td rowspan="2" > GPA </td>

                                                          @foreach ($assign_subjects as $subject)
                                                                <td colspan="2">{{ $subject->subject_name }} </td>
                                                            @endforeach

                                                          <td rowspan="2" > GPA </td>

                                                          @foreach ($assign_subjects as $subject)
                                                          <td colspan="2">{{ $subject->subject_name }} </td>
                                                      @endforeach

                                                          <td rowspan="2" > GPA </td>

                                                          <td rowspan="4" > GPA </td>
                                                        </tr>
                                                      </thead>
                                                      <tbody>
                                                          @php
                                                              $final_result=0;
                                                          @endphp
                                                        <tr>
                                                           @foreach ($assigned_exam as $key => $exam)

                                                            @foreach ($assign_subjects as $subject)
                                                                <td >Mark</td>
                                                                <td >GPA</td>
                                                            @endforeach
                                                         
                                                       <td rowspan="2" >
                                                            @php
                                                                  $percentage=0;
                                                              @endphp
                                                                @if ($key==0)
                                                                    @php
                                                                        $percentage='percentage1';
                                                                    @endphp
                                                                @endif
                                                                @if ($key==1)
                                                                    @php
                                                                    $percentage='percentage2';
                                                                    @endphp
                                                                @endif
                                                                @if ($key==2)
                                                                    @php
                                                                    $percentage='percentage3';
                                                                    @endphp
                                                                @endif
                                                            @php
                                                                $term_gpa=App\CustomResultSetting::termResult($exam->exam_type_id,$input_class,$input_section,$input_student,$assign_subjects->count());
                                                                echo number_format((float)$term_gpa, 2, '.', '');
                                                                $term_final_gpa=App\CustomResultSetting::getFinalResult($exam->exam_type_id,$input_class,$input_section,$input_student,$percentage);
                                                                $final_result= $final_result+ $term_final_gpa;
                                                            @endphp
                                                       </td>
                                                       @endforeach  
                                                          


                                                    <td rowspan="2" >{{ number_format((float)$final_result, 2, '.', '') }}</td>
                                                        </tr>
                                                        @foreach ($assigned_exam as $exam)
                                                            @foreach ($assign_subjects as $subject)
                                                                <td >
                                                                    @php
                                                                        $gpa=App\CustomResultSetting::getSubjectGpa($exam->exam_type_id,$input_class,$input_section,$input_student,$subject->subject_id);
                                                                        $subject_mark=$gpa[$subject->subject_id][0];
                                                                        $subject_gpa=$gpa[$subject->subject_id][1];
                                                                        echo $subject_mark;
                                                                    @endphp
                                                                </td>
                                                                <td >
                                                                    @php
                                                                        $grade=App\CustomResultSetting::getDrade($subject_mark);
                                                                        echo $grade;
                                                                    @endphp
                                                                </td>
                                                            @endforeach
                                                        @endforeach  
                                                               
                                                       
                                                      </tbody>
                                                    </table>
                                                  </div>
                                        </div> 
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
@endif

            

@endsection
