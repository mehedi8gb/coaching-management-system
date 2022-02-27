
<!DOCTYPE html>
<html lang="en">
<head>
  <title>@lang('lang.exam_schedule')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/print/bootstrap.min.css"/>
  <script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/print/jquery.min.js"></script>
  <script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/print/bootstrap.min.js"></script>
</head>
<style>
 table,th,tr,td{
     font-size: 11px !important;
 }
 
</style>
<body>
 

@php 
    $generalSetting= App\SmGeneralSettings::find(1); 
    if(!empty($generalSetting)){
        @$school_name =@$generalSetting->school_name;
        @$site_title =@$generalSetting->site_title;
        @$school_code =@$generalSetting->school_code;
        @$address =@$generalSetting->address;
        @$phone =@$generalSetting->phone; 
    } 
    @$class=App\SmClass::find(@$class_id);
    @$section=App\SmSection::find(@$section_id);
@endphp
<div class="container-fluid"> 
                    
                    <table  cellspacing="0" width="100%">
                        <tr>
                            <td> 
                                <img class="logo-img" src="{{ url('/')}}/{{@$generalSetting->logo }}" alt=""> 
                            </td>
                            <td> 
                                <h3 style="font-size:22px !important" class="text-white"> {{isset($school_name)?$school_name:'Infix School Management ERP'}} </h3> 
                                <p style="font-size:18px !important" class="text-white mb-0"> {{isset($address)?$address:'Infix School Address'}} </p> 
                                <p style="font-size:15px !important" class="text-white mb-0"> @lang('lang.class_routine') </p> 
                          </td>
                            <td style="text-aligh:center"> 
                                
                                <p style="font-size:14px !important; border-bottom:1px solid gray" align="left" class="text-white">@lang('lang.class'): {{ @$class->class_name}} </p> 
                                <p style="font-size:14px !important; border-bottom:1px solid gray" align="left" class="text-white">@lang('lang.section'): {{ @$section->section_name}} </p> 
                               
                          </td>
                        </tr>
                    </table>

                    <hr>
           
                <table class="table table-bordered table-striped" style="width: 100%; table-layout: fixed">
                    
                         
                       <tr>
                            <th>@lang('lang.class') @lang('lang.period')</th>
                            @foreach($sm_weekends as $sm_weekend)
                            <th>{{@$sm_weekend->name}}</th>
                            @endforeach
                        </tr>
               
                        @foreach($class_times as $class_time)
                        <tr>
                            <td>
                                {{@$class_time->period}}
                                <br>
                                {{date('h:i A', strtotime(@$class_time->start_time)).' - '.date('h:i A', strtotime(@$class_time->end_time))}}
                            </td>

                            @foreach($sm_weekends as $sm_weekend)

                            <td>
                                @if(@$class_time->is_break == 0)
                                @if(@$sm_weekend->is_weekend != 1)
                                

                                @php
                                    $assinged_class_routine = App\SmClassRoutineUpdate::assingedClassRoutine(@$class_time->id, @$sm_weekend->id, @$class_id, @$section_id);
                                @endphp
                                @if(@$assinged_class_routine == "")

                                @if(in_array(247, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )

                                <div class="col-lg-6 text-right">
                                    <a href="{{url('add-new-routine', [@$class_time->id, @$sm_weekend->id, @$class_id, @$section_id])}}" class="primary-btn small tr-bg icon-only mr-10 modalLink" data-modal-size="modal-md" title="Create Class routine">
                                        <span class="ti-plus" id="addClassRoutine"></span>
                                    </a>
                                </div>
                                @endif

                                @else
                                    <span class="">{{@$assinged_class_routine->subject !=""?@$assinged_class_routine->subject->subject_name:""}}</span>
                                    <br>
                                    <span class="">{{@$assinged_class_routine->classRoom!=""?@$assinged_class_routine->classRoom->room_no:""}}</span></br>
                                    <span class="tt">{{@$assinged_class_routine->teacherDetail!=""?@$assinged_class_routine->teacherDetail->full_name:""}}</span></br>
                                    @if(in_array(248, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )

                                    <a href="{{url('edit-class-routine', [@$class_time->id, @$sm_weekend->id, @$class_id, @$section_id, @$assinged_class_routine->subject_id, @$assinged_class_routine->room_id, @$assinged_class_routine->id, @$assinged_class_routine->teacher_id])}}" class="modalLink" data-modal-size="modal-md" title="Edit Class routine"><span class="ti-pencil-alt" id="addClassRoutine"></span></a>
                                    @endif
                                    @if(in_array(249, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )

                                    <a href="{{url('delete-class-routine-modal', [@$assinged_class_routine->id])}}" class="modalLink" data-modal-size="modal-md" title="Delete Class routine"><span class="ti-trash" id="addClassRoutine"></span></a>
                               
                                    @endif
                                    @endif

                                
                                @else
                                        @lang('lang.weekend')

                                @endif
                                @endif
                            </td>

                            @endforeach
                        </tr>
                        @endforeach
                        
                </table>
        </div>  
 

</body>
</html>
    
