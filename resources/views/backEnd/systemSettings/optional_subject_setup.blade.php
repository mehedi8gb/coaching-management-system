@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.assign_optional_subject')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.academics')</a>
                <a href="{{route('assign_subject')}}">@lang('lang.assign_subject')</a>
                <a href="{{route('assign_subject_create')}}">@lang('lang.assign_optional_subject')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.assign_optional_subject')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12"> 
                <div class="white-box">
                    @if(in_array(425, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'optional_subject_setup_post', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                    @endif    
                    <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                          
                            

                                <div class="col-lg-4 mt-30-md">
                                    <label>@lang('lang.select') @lang('lang.class') *</label>
                            
                                    @foreach($classes as $class)
                                        <div class="input-effect">
                                            <input type="checkbox" id="class{{@$class->id}}" class="common-checkbox exam-checkbox" name="class[]" value="{{@$class->id}}" {{isset($editData)? (@$class->id == @$editData->class_id? 'checked':''):''}}>
                                            <label for="class{{@$class->id}}">{{@$class->class_name}}</label>
                                        </div>
                                    @endforeach
                                <div class="input-effect">
                                    <input type="checkbox" id="all_exams" class="common-checkbox" name="all_exams[]" value="0" {{ (is_array(old('class')) and in_array(@$class->id, old('class'))) ? ' checked' : '' }}>
                                    <label for="all_exams">@lang('lang.all') @lang('lang.select')</label>
                                </div>
                                @if($errors->has('class'))
                                <span class="text-danger validate-textarea-checkbox" role="alert">
                                    <strong>{{ $errors->first('class') }}</strong>
                                </span>
                            @endif
                                </div>


                                    <div class="col-lg-4">

                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('gpa_above') ? ' is-invalid' : '' }}"
                                            type="text" name="gpa_above" id="exam_mark_main" onkeypress="return isNumberKey(event)" autocomplete="off" value="{{isset($editData)?  number_format(@$editData->gpa_above, 2, '.', ' ') : 0}}" >
                                            <label>GPA Above *</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('gpa_above'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('gpa_above') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                               @php 
                                    $tooltip = "";
                                    if(in_array(425, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 ){
                                            $tooltip = "";
                                        }else{
                                            $tooltip = "You have no permission to add";
                                        }
                                @endphp
                                <div class="col-lg-4 mt-30-md" id="select_subject_div">
                                    <button type="submit" class="primary-btn small fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                        <span class="pr-2"></span>
                                        @if (isset($editData))
                                        @lang('lang.update')
                                        @else
                                        @lang('lang.save')
                                        @endif
                                       
                                    </button>
                                </div> 



                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</section>
 @if(isset($class_optionals))
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-12 col-md-12">
                    <div class="main-title">
                        <h3 class="mb-30"> @lang('lang.optional') @lang('lang.subject')  </h3>
                    </div>
                </div>
                
            </div>
            <div class="row"> 
                <div class="col-lg-12">

               
                <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                    <thead>
                       @if(session()->has('message-success-delete') != "" ||
                        session()->get('message-danger-delete') != "")
                        <tr>
                            <td colspan="5">
                                 @if(session()->has('message-success-delete'))
                                  <div class="alert alert-success">
                                      {{ session()->get('message-success-delete') }}
                                  </div>
                                @elseif(session()->has('message-danger-delete'))
                                  <div class="alert alert-danger">
                                      {{ session()->get('message-danger-delete') }}
                                  </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>@lang('lang.sl')</th>
                            <th>@lang('lang.class') @lang('lang.name')</th>
                            <th>@lang('lang.gpa_above')</th>
                            <th>@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $i=0; @endphp
                        @foreach($class_optionals as $class_optional)
                        <tr>
                            <td>{{++$i}}</td>
                            <td>{{@$class_optional->class_name}}</td>
                            <td>{{ number_format(@$class_optional->gpa_above, 2, '.', ' ')}}</td>
                           
                            <td>
                                <div class="row">
                                   
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(in_array(426, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                                    <a class="dropdown-item" href="{{route('class_optional_edit', [@$class_optional->id])}}">@lang('lang.edit')</a>
                                                @endif
                                                @if(in_array(427, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#deleteSubjectModal{{@$class_optional->id}}"  href="#">@lang('lang.delete')</a>
                                                @endif
                                            </div>
                                        </div>
                                    
                                </div>


                               

                            </td>
                        </tr>
                         <div class="modal fade admin-query" id="deleteSubjectModal{{@$class_optional->id}}" >
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">@lang('lang.delete') @lang('lang.optional') @lang('lang.subject')</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="text-center">
                                            <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                        </div>

                                        <div class="mt-40 d-flex justify-content-between">
                                            <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                            <a href="{{route('delete_optional_subject', [@$class_optional->id])}}" class="text-light">
                                            <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                             </a>
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
    </section>
@endif
  
 

@endsection
