<!DOCTYPE html>
<html lang="en">
<head>
  <title>Merit List </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/print/bootstrap.min.css"/>
  <script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/print/jquery.min.js"></script>
  <script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/print/bootstrap.min.js"></script>
</head>
<style>
    body, table, th, td {
        font-size: 10px;
        font-family: 'Poppins', sans-serif;

    }
    
   .marklist th, .marklist td{
        border: 1px solid #ddd;  
        text-align: center !important;
        font-family: 'Poppins', sans-serif;
        
    }
    .marklist th{
        text-transform: capitalize;
        text-align: center;  
    }
    .marklist td{
        text-align: center; 
    }
 
 
    .container{ 
        padding-bottom: 50px;
        background: white;
        font-family: 'Poppins', sans-serif;
    }
    h1,h2,h3,h4{

        font-family: "Poppins", sans-serif; 
        margin-bottom: 15px;
    }
    hr{
        margin: 0px;
    }
    .mt-10 {
        margin-top:10px;
    }
    .mb-10{
        margin-bottom:10px;
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



   .custom_table td {
    border: 1px solid #726E6D;
    padding: .3rem;
    text-align: center;
  }
  .custom_table th{
    border: 1px solid #726E6D;
    /* text-transform: uppercase; */
    text-align: center;
  }
  thead{
    font-weight:bold;
    text-align:center;
    color: #222;
    font-size: 10px
  }
  
  table {
    border-collapse: collapse;
  }
  
  .footer {
    text-align:right;
    font-weight:bold;
  }


.custom_table{
    width:100%;
}
.custom_table {
    width: 98%;
    margin: auto;
}
.custom_table th{
    
        border: 1px solid black;
        text-align: center;
        background: #351681;
        color: white;
        font-size: 10px;
        line-height: 1;
        padding: .5rem;
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
@endphp
<div class="container"> 
        <table style="width:100%; border:0;"> 
                <tr >
                    
                    <th>
                        <img class="logo-img" src="{{ url('/')}}/{{$generalSetting->logo }}" alt=""> 
                    </th>
                    <th colspan="2"> 
                        <h3 class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3> 
                        <p class="text-white mb-0" style="padding-right:10px !important;"> {{isset($address)?$address:'Infix School Address'}} </p>
                    </th> 

                </tr>  
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
                <tr>
                    <td  style=" padding:10px; vertical-align:top;">
                        
                        <p class="mb-0" style="padding-right:10px !important; font-size:11px;"> @lang('lang.academic_year') : <span class="primary-color fw-500">{{$generalSetting->session_year}}</span> </p>
                        {{-- <p class="mb-0" style="padding-right:10px !important; font-size:11px;"> @lang('lang.exam') : <span class="primary-color fw-500">{{@$exam_name}}</span> </p> --}}
                        <p class="mb-0" style="padding-right:10px !important; font-size:11px;"> @lang('lang.class') : <span class="primary-color fw-500">{{@$class_name}}</span> </p>
                        <p class="mb-0" style="padding-right:10px !important; font-size:11px;"> @lang('lang.section') : <span class="primary-color fw-500">{{@$section->section_name}}</span> </p>
                    </td>
                    <td  style="  padding:10px; vertical-align:top;"> 
                        <p style="font-weight: 700;">@lang('lang.subjects') @lang('lang.list')</p> 
                        <div class="row">
                            <div class="col-md-12 w-100" style="columns: 2">
                                @foreach($assign_subjects as $subject)
                                <p class="mb-0" style="padding-right:10px !important; font-size:11px;"> <span class="primary-color fw-500">{{$subject->subject->subject_name}}</span> </p>
                                @endforeach 
                            </div>
                        </div>
                    </td>
                    <td  style=" padding:10px; vertical-align:top;">
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
                    </td>
                </tr> 
        </table>


        
        <h4 style=" text-align: center;  padding: 10px;">Student Merit List</h4> 

       
        <div class="student_marks_table">
            <table class="custom_table">
              <thead>
                <tr>
                  <th colspan="" class="full_width" >@lang('lang.sl')</th>
                  <th colspan="" class="full_width" >Ad. No.</th>
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
                                       


        <table style="width:100%">
            <tr> 
                <td> 
                    <p style="padding-top:10px; text-align:right; float:right; border-top:1px solid #ddd; display:inline-block; margin-top:50px;">( Exam Controller )</p> 
                </td>
            </tr>

        </table>
       
               
 

</body>
</html>
    
