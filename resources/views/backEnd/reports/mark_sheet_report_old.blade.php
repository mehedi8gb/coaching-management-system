@extends('backEnd.master')
@section('mainContent')
<style>
    th{
        border: 1px solid black;
        text-align: center;
    }
    td{
        text-align: center;
    }
    td.subject-name{
        text-align: left;
        padding-left: 10px !important;
    }
    table.marksheet{
        width: 100%;
        border: 1px solid #828bb2 !important;
    }
    table.marksheet th{
        border: 1px solid #828bb2 !important;
    }
    table.marksheet td{
        border: 1px solid #828bb2 !important;
    }
    table.marksheet thead tr{
        border: 1px solid #828bb2 !important;
    }
    table.marksheet tbody tr{
        border: 1px solid #828bb2 !important;
    }

    .studentInfoTable{
        width: 100%;
        padding: 0px !important;
    }

    .studentInfoTable td{
        padding: 0px !important;
        text-align: left;
        padding-left: 15px !important;
    }
    h4{
        text-align: left !important;
    }
    hr{
        margin: 0px;
    }
    #grade_table th{
        border: 1px solid black;
        text-align: center;
        background: #351681;
        color: white;
    }
    #grade_table td{
        color: black;
        text-align: center !important;
        border: 1px solid black;
    }
</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.mark_sheet_report') @lang('lang.student') </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.mark_sheet_report') @lang('lang.student')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.select_criteria')</h3>
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'mark_sheet_report_student', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            
                            <div class="col-lg-3 mt-30-md">
                                <select class="w-100 bb niceSelect form-control{{ $errors->has('exam') ? ' is-invalid' : '' }}" name="exam">
                                    <option data-display="@lang('lang.select_exam') *" value="">@lang('lang.select_exam') *</option>
                                    @foreach($exams as $exam)
                                        <option value="{{$exam->id}}" {{isset($exam_id)? ($exam_id == $exam->id? 'selected':''):''}}>{{$exam->title}}</option>
                                       
                                    @endforeach
                                </select>
                                @if ($errors->has('exam'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('exam') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-3 mt-30-md">
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
                            <div class="col-lg-3 mt-30-md" id="select_section_div">
                                <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section">
                                    <option data-display="@lang('lang.select_section') *" value="">@lang('lang.select_section') *</option>
                                </select>
                                @if ($errors->has('section'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('section') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-lg-3 mt-30-md" id="select_student_div">
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
        $email =$generalSetting->email;  
    }

@endphp
                  
<section class="student-details">
    <div class="container-fluid p-0">
        <div class="row mt-40">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.mark_sheet_report')</h3>
                </div>
            </div>
            <div class="col-lg-8 pull-right">
                <a href="{{url('mark-sheet-report/print', [$input['exam_id'], $input['class_id'], $input['section_id'], $input['student_id']])}}" class="primary-btn small fix-gr-bg pull-right" target="_blank"><i class="ti-printer"> </i> @lang('lang.print')</a>


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
                                        {{-- <div>
                                            <img class="report-admit-img"  src="{{ file_exists(@$studentDetails->student_photo) ? asset($studentDetails->student_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" width="100" height="100" alt="{{asset($studentDetails->student_photo)}}">
                                        </div> --}}
                                        
                                        
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-12">
                                                <div class="row"> 
                                                    <div class="col-lg-8">
                                                        <div class="row"> 
                                                            <div class="col-lg-2">
                                                                <img class="report-admit-img"  src="{{ file_exists(@$studentDetails->student_photo) ? asset($studentDetails->student_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" width="100" height="100" alt="{{asset($studentDetails->student_photo)}}">
                                                            </div>
                                                            <div class="col-lg-8">
                                                                 <table class="table">
                                                                    <tr>
                                                                        <td>
                                                                            <h4>@lang('lang.student_information')</h4>
                                                                            <table class="studentInfoTable">
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.student_name') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$student_detail->full_name}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.father_name') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$student_detail->parents->fathers_name}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.mother_name') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$student_detail->parents->mothers_name}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.roll_number') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$student_detail->roll_no}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.admission_no') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$student_detail->admission_no}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.date_of_birth') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$student_detail->date_of_birth != ""? App\SmGeneralSettings::DateConvater($student_detail->date_of_birth):''}}
                                                                                    </td>
                                                                                </tr>


                                                                            </table>
                                                                        </td>
                                                                        <td style="padding-left: 30px">
                                                                            <h4>@lang('lang.exam') @lang('lang.info')</h4>
                                                                            <table class="studentInfoTable">
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.exam_title') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$exam_details->title}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.academic') @lang('lang.class') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$class_name->class_name}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="font-weight-bold">
                                                                                        @lang('lang.academic') @lang('lang.section') :
                                                                                    </td>
                                                                                    <td>
                                                                                        {{$section->section_name}}
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    {{-- col-lg-4 text-black --}}
                                                    <div class="col-lg-4 text-black"> 
                                                        @php $marks_grade=DB::table('sm_marks_grades')->where('created_at', 'LIKE', '%' . App\YearCheck::getYear() . '%')->get(); @endphp
                                                            @if(@$marks_grade)
                                                            <table class="table  table-bordered table-striped text-black" id="grade_table">
                                                                <thead>
                                                                <tr>
                                                                    <th>@lang('lang.staring')</th>
                                                                    <th>@lang('lang.ending')</th>
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
                                                    {{-- col-lg-4 text-black --}}
        
                                                </div>
                                       
                                        <table class="w-100 mt-30 mb-20 table   table-bordered marksheet">
                                            <thead>
                                                <tr>
                                                    <th>@lang('lang.sl')</th>
                                                    <th>@lang('lang.subject') @lang('lang.name')</th>
                                                    <th>@lang('lang.subject') @lang('lang.marks ')</th>
                                                    <th>@lang('lang.highest_marks')</th>
                                                    <th>@lang('lang.obtained_marks')</th>
                                                    <th>@lang('lang.letter_grade')</th>
                                                    <th>@lang('lang.grade_point')</th>
                                                    @if ($optional_subject_setup!='')
                                                    <th>@lang('lang.gpa')
                                                    <hr>
                                                    <small>@lang('lang.without_additional')</small>    
                                                    </th>
                                                    <th>@lang('lang.gpa') </th>
                                                    @else
                                                    <th>@lang('lang.gpa') </th>
                                                    @endif
                                                    <th>@lang('lang.result') </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            @php $sum_gpa= 0;  $resultCount=1; $subject_count=1; $tota_grade_point=0; $this_student_failed=0; @endphp
                                            @foreach($subjects as $data)
                                               {{-- @php
                                                   if($data->subject_id==$optional_subject){
                                                       continue;
                                                   }
                                               @endphp --}}
                                                <tr>
                                                    <td width='5%'>{{$subject_count++}}</td>
                                                    <td width='15%' class="subject-name">{{$data->subject->subject_name}} </td>
                                                    
                                                    <td width='10%'>
                                                        
                                                        @php $subject_mark=App\SmAssignSubject::getSubjectMark($data->subject_id, $class_id, $section_id, $exam_type_id);

                                                         echo $subject_mark;
                                                         @endphp

                                                    </td>
                                                    <td width='10%'>
                                                        
                                                        @php $highest_mark=App\SmAssignSubject::getHighestMark($data->subject_id, $class_id, $section_id, $exam_type_id);

                                                        echo $highest_mark;
                                                         @endphp

                                                    </td>
                                                    <td width='10%'>
                                                         @php $tola_mark_by_subject=App\SmAssignSubject::getSumMark($student_detail->id, $data->subject_id, $class_id, $section_id, $exam_type_id);

                                                         echo $tola_mark_by_subject;
                                                         @endphp
                                                    </td>
                                                    <td width='10%'>

                                                        @php
                                                            $mark_grade = App\SmMarksGrade::where([['percent_from', '<=', $tola_mark_by_subject], ['percent_upto', '>=', $tola_mark_by_subject]])->first();

                                                        @endphp
                                                        {{$mark_grade->grade_name }}
                                                    </td>
                                                    <td width='10%'>

                                                        @php
                                                            $mark_grade = App\SmMarksGrade::where([['percent_from', '<=', $tola_mark_by_subject], ['percent_upto', '>=', $tola_mark_by_subject]])->first();
                                                            $tota_grade_point = $tota_grade_point + $mark_grade->gpa ;
                                                            if($mark_grade->gpa<1){
                                                                $this_student_failed =1;
                                                            }
                                                            $optional_subject_id='';
                                                            $countable_optional_gpa=0;
                                                        @endphp
                                                        {{-- {{dd($optional_subject_setup)}} --}}
                                                        @if (@$optional_subject==$data->subject_id)
                                                        (GPA above {{ @$optional_subject_setup->gpa_above }})
                                                        <hr>
                                                        @if ($optional_subject_setup!='')
                                                        @if ($mark_grade->gpa > $optional_subject_setup->gpa_above)
                                                        
                                                            @php
                                                                $countable_optional_gpa = $mark_grade->gpa-$optional_subject_setup->gpa_above;
                                                                $optional_subject_id=$optional_subject;
                                                            @endphp
                                                        @endif
                                                        @endif
                                                      
                                                        @endif
                                                        @if (@$optional_subject==$data->subject_id)
                                                        
                                                            {{@$mark_grade->gpa-$optional_subject_setup->gpa_above }}
                                                        @else
                                                            {{@$mark_grade->gpa }}
                                                        @endif 
                                                       
                                                    </td>
                                                    
                                                    @if ($optional_subject_setup!='')
                                                        @if($subject_count==2)
                                                        <td rowspan="{{count($subjects)}}" style="vertical-align: middle">{{  App\SmAssignSubject::get_student_result($student_detail->id, $data->subject_id, $class_id, $section_id, $exam_type_id,$optional_subject_id,$optional_subject_setup) }}</td>
                                                      
                                                        
                                                         @endif
                                                    @endif
                                                    

                                                    @if($subject_count==2)
                                                    <td  rowspan="{{count($subjects)}}" style="vertical-align: middle">{{  App\SmAssignSubject::get_student_result_without_optional($student_detail->id, $data->subject_id, $class_id, $section_id, $exam_type_id,$optional_subject_id,$optional_subject_setup) }}</td>
                                                       
                                                        <td rowspan="{{count($subjects)}}" style="vertical-align: middle">
                                                            @php
                                                                $gpa_result=App\SmAssignSubject::get_student_result_without_optional($student_detail->id, $data->subject_id, $class_id, $section_id, $exam_type_id,$optional_subject_id,$optional_subject_setup);
                                                                $result_grade=App\SmMarksGrade::where([['from', '<=', $gpa_result], ['up', '>=', $gpa_result]])->first();
                                                                echo $result_grade->grade_name;
                                                            @endphp
                                                        
                                                        </td>
                                                        @endif
                                                   
                                                </tr>

                                                @endforeach

                                            </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <p class="result-date">
                                                    @php
                                                     $data = App\SmMarkStore::select('created_at')->where([
                                                        ['student_id',$student_detail->id],
                                                        ['class_id',$class_id],
                                                        ['section_id',$section_id],
                                                        ['exam_term_id',$exam_type_id],
                                                    ])->first();

                                                    @endphp
                                                    @lang('lang.date_of_publication_of_result') : <b> {{date_format(date_create($data->created_at),"F j, Y, g:i a")}}</b>
                                                </p>
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
            

@endsection
