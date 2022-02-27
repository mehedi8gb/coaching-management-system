@extends('backEnd.master')
@section('title') 
@lang('lesson::lesson.lesson_plan_overview')
@endsection
@section('mainContent')


<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>{{$student_detail->full_name}}-@lang('lesson::lesson.lesson_plan_overview')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('lesson::lesson.lesson')</a>
                <a href="#">@lang('lesson::lesson.lesson_plan_overview')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
  
    </div>
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                               
                            </h3>
                            </div>
                        </div>
                    </div>
                <div class="col-lg-12">
                    <table id="table_id" class="display school-table" cellspacing="0" width="100%"> 
                        <thead>                            
                            <tr>
                            <th>@lang('lesson::lesson.lesson')</th>
                            <th>@lang('lesson::lesson.topic')</th>
                            <th>@lang('lesson::lesson.sub_topic')</th>
                            <th>@lang('lesson::lesson.completed_date') </th>
                            <th>@lang('lesson::lesson.upcoming_date') </th>
                            <th>@lang('common.status')</th>
                         
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($lessonPlanner as $data)
                            
                       
                        <tr>
                            <td>{{@$data->lessonName !=""?@$data->lessonName->lesson_title:""}}</td>

                            <td> 
                                @php 
                                $alllessonPlannerTopic=DB::table('lesson_planners') 
                                                    ->join('sm_lessons','sm_lessons.id','=','lesson_planners.lesson_detail_id')
                                                    ->join('sm_lesson_topic_details','sm_lesson_topic_details.id','=','lesson_planners.topic_detail_id')                                                
                                                    ->where('lesson_detail_id',$data->lesson_detail_id) 
                                                    ->where('lesson_planners.active_status', 1)
                                                    ->select('lesson_planners.*','sm_lessons.lesson_title','sm_lesson_topic_details.topic_title')
                                                    ->get();                               
                                @endphp
                                @foreach($alllessonPlannerTopic as $allData)                             
                                {{@$allData->topic_title}}<br>
                                @endforeach

                            </td>

                            <td>
                                @foreach($alllessonPlannerTopic as $allData)
                                @php
                                    $topicdate=DB::table('lesson_planners')->where('id',$allData->id)->first();                                  
                                @endphp 
                                {{@$topicdate->sub_topic !=""?@$topicdate->sub_topic:""}}<br> 
                                @endforeach  
                            </td>
                            <td>
       
                                @foreach($alllessonPlannerTopic as $allData)
                                @php
                                    $topicdate=DB::table('lesson_planners')->where('id',$allData->id)->first();                                  
                                @endphp 
                                {{@$topicdate->competed_date !=""?@$topicdate->competed_date:""}}<br> 
                                @endforeach
                           
                                </td>
                                <td>
                                    @foreach($alllessonPlannerTopic as $allData)
                                    @php
                                    $topicdate=DB::table('lesson_planners')->where('id',$allData->id)->first();                                  
                                    @endphp 
                                
                                      
                                           
                                           @if(date('Y-m-d')< $topicdate->lesson_date && $topicdate->competed_date=="")
                                            @lang('lesson::lesson.upcoming')     ({{$topicdate->lesson_date}})<br>                                          
                                           @elseif($topicdate->competed_date=="")
                                            @lang('lesson::lesson.assigned_date')({{$topicdate->lesson_date}})  
                                           <br>
                                           @else
                                          
                                           @endif
                                       
                                 
                                     @endforeach
                                </td>
                                <td>
                                     @foreach($alllessonPlannerTopic as $allData)
                                     @php
                                     $topicdate=DB::table('lesson_planners')->where('id',$allData->id)->first();                                  
                                     @endphp 
                                      @if(date('Y-m-d')< $topicdate->lesson_date && $topicdate->competed_date=="")
                                                @lang('lesson::lesson.upcoming') <br>                                          
                                               @elseif($topicdate->competed_date=="")
                                                @lang('lesson::lesson.incomplete') 
                                               <br>
                                               @else
                                              <strong>@lang('lesson::lesson.completed')</strong> <br>
                                               @endif
                                      @endforeach
                                </td>

         
                        </tr>
                        @endforeach
                       
                    </tbody>
                </table>
            </div>
       
        </div>
    </div>
</div>
</div>
</section>
@endsection
