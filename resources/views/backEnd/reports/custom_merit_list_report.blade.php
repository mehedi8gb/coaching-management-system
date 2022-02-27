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
            <h1>@lang('lang.custom') @lang('lang.merit_list_report') </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.merit_list_report')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'custom-merit-list', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                           
                            <div class="col-lg-6 mt-30-md">
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
                            <div class="col-lg-6 mt-30-md" id="select_section_div">
                                <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section">
                                    <option data-display="@lang('lang.select_section') *" value="">@lang('lang.select_section') *</option>
                                </select>
                                @if ($errors->has('section'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('section') }}</strong>
                                </span>
                                @endif
                            </div>
                            
                            <div class="col-lg-12 mt-20 text-right">
                                <button type="submit" class="primary-btn small fix-gr-bg">
                                    <span class="ti-search pr-2"></span>
                                    @lang('lang.search')
                                </button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
</section>

 
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
@if (isset($customresult))
    

<section class="student-details">
    <div class="container-fluid p-0">
        <div class="row mt-40">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-30 mt-0">@lang('lang.custom') @lang('lang.merit_list_report')</h3>
                </div>
            </div>
            <div class="col-lg-8 pull-right">
                <a href="{{url('custom-merit-list/print', [$InputClassId, $InputSectionId])}}" class="primary-btn small fix-gr-bg pull-right" target="_blank"><i class="ti-printer"> </i> Print</a>

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
                                            <div class="offset-2">

                                            </div>
                                            <div class="col-lg-2">
                                            <img class="logo-img" src="{{ @$generalSetting->logo }}" alt="">
                                            </div>
                                            <div class="col-lg-6 ml-30">
                                                <h3 class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3> 
                                                <p class="text-white mb-0"> {{isset($address)?$address:'Infix School Address'}} </p>
                                                <p class="text-white mb-0"> @lang('lang.email')  {{isset($email)?$email:'admin@demo.com'}} ,  @lang('lang.phone')  {{isset($phone)?$phone:'admin@demo.com'}} </p> 
                                            </div>
                                            <div class="offset-2">

                                                </div>
                                        </div>
                                    </div>



                                    <div class="card-body">
                                        <div class="col-md-12">
                                            <div class="row">
                                                {{-- start col-lg-8 --}}
                                                <div class="col-lg-7"> 
                                                    <div class="row">

                                                        <div class="col-md-6">
                                                            <h3>@lang('lang.order_of_merit_list')</h3> 
                                                            <p class="mb-0">
                                                                @lang('lang.academic_year') : <span class="primary-color fw-500">{{@$generalSetting->session_year}}</span>
                                                            </p>
                                                            {{-- <p class="mb-0">
                                                                @lang('lang.exam') : <span class="primary-color fw-500">{{@$exam_name}}</span>
                                                            </p> --}}
                                                            <p class="mb-0">
                                                                @lang('lang.class') : <span class="primary-color fw-500">{{@$class_name}}</span>
                                                            </p>
                                                            <p class="mb-0">
                                                                @lang('lang.section') : <span class="primary-color fw-500">{{@$section->section_name}}</span>
                                                            </p>  
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h3>@lang('lang.subjects')</h3> 
                                                                @foreach($assign_subjects as $subject)
                                                                    <p class="mb-0">
                                                                        <span class="primary-color fw-500">{{@$subject->subject->subject_name}}</span>
                                                                    </p>
                                                                @endforeach  
                                                        </div>

                                                    </div>

                                                </div>
                                                {{-- end col-lg-8 --}}



                                                {{-- sm_marks_grades --}}
                                                <div class="col-lg-4 text-black"> 
                                                    @php $marks_grade=DB::table('sm_marks_grades')->where('created_at', 'LIKE', '%' . App\YearCheck::getYear() . '%')->get(); @endphp
                                                    @if(@$marks_grade)
                                                        <table class="table  table-bordered table-striped " id="grade_table">
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
                                                {{--end sm_marks_grades --}}

                                            </div> 
                                        </div>
                                        <h3 class="primary-color fw-500 text-center">Custom Merit List</h3>
                                        <hr>
{{-- {{ dd($custom_result_setup) }} --}}
                                        <div class="table-responsive">
                                            
                                                <div class="student_marks_table">
                                                        <table class="custom_table">
                                                          <thead>
                                                            <tr>
                                                              <th colspan="" class="full_width" >@lang('lang.sl')</th>
                                                              <th colspan="" class="full_width" >Ad.no</th>
                                                              <th colspan="" class="full_width" >@lang('lang.student')</th>
                                                            <th colspan="" class="full_width" >@lang('lang.first_term') ({{ $custom_result_setup->percentage1 }}%)</th>
                                                              <th colspan="" class="full_width" >@lang('lang.second_term') ({{ $custom_result_setup->percentage2 }}%)</th>
                                                              <th colspan="" class="full_width" >@lang('lang.third_term') ({{ $custom_result_setup->percentage3 }}%)</th>
                                                              <th colspan="" class="full_width" >@lang('lang.final_result')</th>
                                                              <th colspan="" class="full_width" >@lang('lang.grade')</th>
                                                            </tr>
                                                          </thead>
                                                          <tbody>
                                                              @php $count=1; @endphp
                                                              @foreach($customresult as $row)
                                                            <tr>
                                                             <td >{{$count++}}</td>
                                                            <td >{{@$row->admission_no}}</td>
                                                            <td >{{@$row->full_name}}</td>
                                                            <td >{{@$row->gpa1}}</td>
                                                            <td >{{@$row->gpa2}}</td>
                                                            <td >{{@$row->gpa3}}</td>
                                                            <td >{{@$row->final_result}}</td>
                                                            <td >{{@$row->final_grade}}</td> 
                                                            </tr>
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
    </div>
</section>
@endif       

@endsection
