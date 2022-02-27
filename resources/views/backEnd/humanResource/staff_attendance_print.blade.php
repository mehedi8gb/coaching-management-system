<!DOCTYPE html>
<html lang="en">
<head>
  <title>@lang('lang.staff') @lang('lang.attendance')  </title>
  <meta charset="utf-8"> 
</head>
<style>
 table,th,tr,td{
     font-size: 11px !important;
     padding: 0px !important;
     text-align: center !important;
 }
 
 #attendance.th,#attendance.tr,#attendance.td{
     font-size: 10px !important;
     padding: 0px !important;
     text-align: center !important;
     border:1px solid #ddd;
     vertical-align: middle !important;
 }
 #attendance th{
     background: #ddd;
     text-align: center;
 }
 #attendance{
     border: 1px solid black;
        border-collapse: collapse;
 }
 #attendance tr{
     border: 1px solid black;
        border-collapse: collapse;
 }
 #attendance th{
     border: 1px solid black;
        border-collapse: collapse;
        text-align: center !important;
        font-size: 11px;
 }
 #attendance td{
     border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
        font-size: 10px;
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
<div class="container-fluid"> 
                    
                    <table  cellspacing="0" width="100%">
                        <tr>
                            <td> 
                                <img class="logo-img" src="{{ url('/')}}/{{$generalSetting->logo }}" alt=""> 
                            </td>
                            <td> 
                                <h3 style="font-size:22px !important" class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3> 
                                <p style="font-size:18px !important" class="text-white mb-0"> {{isset($address)?$address:'Infix School Address'}} </p>  
                          </td>
                            <td style="text-aligh:center"> 
                                <p style="font-size:14px !important; border-bottom:1px solid gray" align="left" class="text-white">@lang('lang.role'): {{ $role->name}} </p> 
                                <p style="font-size:14px !important; border-bottom:1px solid gray" align="left" class="text-white">@lang('lang.month'): {{ date("F", strtotime('00-'.$month.'-01')) }} </p> 
                                <p style="font-size:14px !important; border-bottom:1px solid gray" align="left" class="text-white">@lang('lang.year'): {{ $year }} </p>
                               
                          </td>
                        </tr>
                    </table>


                    <h3 style="text-align:center">@lang('lang.staff') @lang('lang.attendance') @lang('lang.report')</h3>
           
                <table id="attendance" style="width: 100%; table-layout: fixed">
                    
                         
                        <tr>
                            <th width="7%">@lang('lang.staff') @lang('lang.name')</th>
                            <th width="7%">@lang('lang.staff') @lang('lang.no')</th>
                            <th>P</th>
                            <th>L</th>
                            <th>A</th>
                            <th>H</th>
                            <th>F</th>
                            <th width="5%">%</th>
                            @for($i = 1;  $i<=$days; $i++)
                            <th class="{{($i<=18)? 'all':'none'}}">
                                {{$i}} <br>
                                <!-- @php
                                    $date = $year.'-'.$month.'-'.$i;
                                    $day = date("D", strtotime($date));
                                    echo $day;
                                @endphp -->
                            </th>
                            @endfor
                        </tr>
               
                        @foreach($attendances as $values)
                        @php $total_attendance = 0; @endphp
                        @php $count_absent = 0; @endphp
                        <tr>
                            <td>
                                @php $student = 0; @endphp
                                @foreach($values as $value)
                                    @php $student++; @endphp
                                    @if($student == 1)
                                        {{$value->staffInfo->full_name}}
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @php $student = 0; @endphp
                                @foreach($values as $value)
                                    @php $student++; @endphp
                                    @if($student == 1)
                                        {{$value->staffInfo->staff_no}}
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @php $p = 0; @endphp
                                @foreach($values as $value)
                                    @if($value->attendence_type == 'P')
                                        @php $p++; $total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$p}}
                            </td>
                            <td>
                                @php $l = 0; @endphp
                                @foreach($values as $value)
                                    @if($value->attendence_type == 'L')
                                        @php $l++; $total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$l}}
                            </td>
                            <td>
                                @php $a = 0; @endphp
                                @foreach($values as $value)
                                    @if($value->attendence_type == 'A')
                                        @php $a++; $count_absent++; $total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$a}}
                            </td>
                            <td>
                                @php $h = 0; @endphp
                                @foreach($values as $value)
                                    @if($value->attendence_type == 'H')
                                        @php $h++; $total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$h}}
                            </td>
                            <td>
                                @php $f = 0; @endphp
                                @foreach($values as $value)
                                    @if($value->attendence_type == 'F')
                                        @php $f++; $total_attendance++; @endphp
                                    @endif
                                @endforeach
                                {{$f}}
                            </td>
                            <td>  
                               @php
                                 $total_present = $total_attendance - $count_absent;
                                 if($count_absent == 0){
                                     echo '100%';
                                 }else{
                                     $percentage = $total_present / $total_attendance * 100;
                                     echo number_format((float)$percentage, 2, '.', '').'%';
                                 }
                               @endphp

                            </td>
                            @for($i = 1;  $i<=$days; $i++)
                                @php
                                    $date = $year.'-'.$month.'-'.$i;
                                @endphp
                                <td class="{{($i<=18)? 'all':'none'}}">
                                    @foreach($values as $value)
                                        @if(strtotime($value->attendence_date) == strtotime($date))
                                            {{$value->attendence_type}}
                                        @endif
                                    @endforeach
                                </td>
                            @endfor
                        </tr>
                        @endforeach
                        
                </table>
        </div>  
 

</body>
</html>
    

