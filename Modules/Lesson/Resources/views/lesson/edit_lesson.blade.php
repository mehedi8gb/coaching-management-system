@extends('backEnd.master')
@section('title') 
@lang('lesson::lesson.edit_lesson')
@endsection
@section('mainContent')

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lesson::lesson.edit_lesson')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('lesson::lesson.lesson')</a>
                <a href="#">@lang('lesson::lesson.edit_lesson')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($lesson))
        @if(userPermission(803))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('/lesson')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('common.add')
                </a>
            </div>
        </div>

        @endif
        @endif

 
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'lesson-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
        <div class="row">
           
            <div class="col-lg-3">
            
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">
                                    @lang('lesson::lesson.edit_lesson')
                              
                                  
                            </h3>
                        </div>
                        <div class="white-box">
                            <div class="add-visitor"> 
                                <div class="row ">
                                     <div class="col-lg-12">

                                       <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class" disabled="">
                                        <option data-display="@lang('common.select_class') *" value="">@lang('common.class') *</option>
                                        @foreach($classes as $class)
                                        <option value="{{ @$class->id}}"  {{ @$class->id == @$lesson->class_id?'selected':''}}>{{ @$class->class_name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('class'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('class') }}</strong>
                                    </span>
                                    @endif

                                </div>
                                </div> 
                                <div class="row mt-25">

                                        <div class="col-lg-12" id="select_section_div">
                                            
                                           <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }} select_section" id="select_section" name="section" disabled="">
                                                <option data-display="@lang('common.select_section') *" value="">@lang('common.select_section') *</option>                     @foreach($sections as $section)
                                            <option value="{{@$section->id}}" {{ @$section->id == @$lesson->section_id?'selected':''}}>{{@$section->section_name}}</option>
                                            @endforeach
                                            </select>
                                               <div class="pull-right loader loader_style" id="select_section_loader">
                                                    <img src="{{asset('public/backEnd/image/pre-loader.gif')}}" alt="" class="loader_img_style">
                                                </div>   
                                                @if ($errors->has('section'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('section') }}</strong>
                                                </span>
                                                 @endif
                                                 
                                        </div>
                                 </div>
                                       <div class="row mt-25" id="">
                                     <div class="col-lg-12" id="select_subject_div">
                                         <select class="w-100 bb niceSelect form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}" id="select_subject" name="subject" disabled="">
                                            <option data-display="@lang('common.select_subjects') *" value="">@lang('common.select_subjects')*</option>
                                            @foreach($subjects as $subject)
                                             <option value="{{@$subject->id}}" {{ @$subject->id == @$lesson->subject_id?'selected':''}}>{{@$subject->subject_name}} ({{$subject->subject_type=='T' ? 'Theory' : 'Practical'}})</option>
                                             @endforeach
                                        </select>
                                        <div class="pull-right loader" id="select_subject_loader" style="margin-top: -30px;padding-right: 21px;">
                                            <img src="{{asset('public/backEnd/image/pre-loader.gif')}}" alt="" style="width: 28px;height:28px;">
                                        </div> 
                                        @if ($errors->has('subject'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('subject') }}</strong>
                                        </span>
                                        @endif
                                      </div>  
                                </div>
                             
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box mt-10">
                            <div class="row">
                                
                            </div>
                            <table class="" id="productTable">
                                <thead>
                                    <tr>
                                  
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lesson_detail as $lesson_item)
                                  <tr id="row1" >
                                    <td class="pt-2">
                                        <input type="hidden" name="lesson_detail_id[]" value="{{$lesson_item->id}}">
                                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">  
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('lesson') ? ' is-invalid' : '' }}"
                                                type="text" id="lesson" name="lesson[]" autocomplete="off" value="{{$lesson_item->lesson_title}}" required="" style="padding-top: 20px">
                                                <label style="top: -5px;">@lang('lesson::lesson.title')</label>
                                        </div>
                                    </td>
                                 
                                    <td class="pt-2">
                                      <a href="{{url('/lesson/lesson-item-delete/'.$lesson_item->id)}}" onclick="return confirm('Are You Sure??')">   <button class="primary-btn icon-only fix-gr-bg" type="button">
                                             <span class="ti-trash"></span>
                                        </button>
                                        </a>
                                       
                                    </td>
                                </tr>
                                 @endforeach
                               </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                               @php 
                                  $tooltip = "";
                                  if(userPermission(803)){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                <div class="row mt-40">
                                    <div class="col-lg-12">
                                        <div class="white-box">                               
                                            <div class="row mt-40">
                                                <div class="col-lg-12 text-center">
                                                  <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{ @$tooltip}}">
                                                        <span class="ti-check"></span>
                                                        
                                                        @lang('lesson::lesson.update_lesson')

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            
            {{ Form::close() }}

            <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-0">@lang('lesson::lesson.lesson_list')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                    <thead>
                             
                                <tr>
                                    <th>@lang('common.sl')</th>                                 
                                    <th>@lang('common.class')</th>
                                    <th>@lang('common.section')</th>
                                    <th>@lang('lesson::lesson.subject')</th>
                                    <th>@lang('lesson::lesson.lesson')</th>                              
                                    <th>@lang('common.action')</th>
                                </tr>
                    </thead>

                    <tbody>
                    @php $count =1 ; @endphp
                                @foreach($lessons as $lesson)
                                <tr>
                                    <td>{{$count++}}</td>                                   
                                    <td>{{$lesson->class !=""?$lesson->class->class_name:""}}</td>
                                    <td>{{$lesson->section !=""?$lesson->section->section_name:""}}</td>
                                    <td>{{$lesson->subject !=""?$lesson->subject->subject_name:""}}</td>
                                   
                                    <td>
                                        
                                        @php
                                            $lesson_title=Modules\Lesson\Entities\SmLesson::lessonName($lesson->class_id,$lesson->section_id,$lesson->subject_id);
                                        @endphp
                                        @foreach($lesson_title as $data)
                                      
                                        <div class="row">
                                            <div class="col-sm-10">   {{$data->lesson_title}}, </div>
                                        </div>
                                        @endforeach
                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('common.select')
                                            </button>
                                       
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(userPermission(803))
                                                <a class="dropdown-item"
                                                href="{{route('lesson-edit',[$lesson->class_id,$lesson->section_id,$lesson->subject_id])}}">@lang('common.edit')</a>
                                                 @endif
                                                @if(userPermission(804))
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#deleteExamModal{{$lesson->id}}"
                                                        href="#">@lang('common.delete')</a>
                                                 @endif
                                            </div>
                                           
                                        </div> 
                                    </td>
                                </tr>
                                    <div class="modal fade admin-query" id="deleteExamModal{{$lesson->id}}" >
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('common.delete_exam')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                    </div>

                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                                                         {{ Form::open(['route' => array('lesson-delete',$lesson->id), 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                                        <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                                                         {{ Form::close() }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
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
