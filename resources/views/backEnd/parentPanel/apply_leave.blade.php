@extends('backEnd.master')
@section('mainContent')
@php
    function showPicName($data){
        $name = explode('/', $data);
        return $name[3];
    }
@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.apply_leave')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.student')</a>
                <a href="#">@lang('lang.apply_leave')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor pl_22">
<div class="container-fluid p-0">
    
    @if(isset($apply_leave))
    @if(@in_array(82, App\GlobalVariable::GlobarModuleLinks()))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('apply-leave')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif
    @endif
<div class="row">
    <div class="col-lg-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-title">
                    <h3 class="mb-30">@if(isset($apply_leave))
                            @lang('lang.edit')
                        @else
                            @lang('lang.add')
                        @endif
                        @lang('lang.apply_leave')
                    </h3>
                </div>
                @if(isset($apply_leave))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'parent-leave-update/'.$apply_leave->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                @else
                    @if(in_array(82, App\GlobalVariable::GlobarModuleLinks()))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'parent-leave-store',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    @endif
                @endif
                <div class="white-box">
                    <div class="add-visitor">
                        <div class="row no-gutters input-right-icon">
                            @if(session()->has('message-success'))
                            <div class="alert alert-success">
                                {{ session()->get('message-success') }}
                            </div>
                            @elseif(session()->has('message-danger'))
                            <div class="alert alert-danger">
                                {{ session()->get('message-danger') }}
                            </div>
                            @endif
                            <div class="col">
                                <div class="input-effect">
                                    <input class="primary-input date form-control{{ $errors->has('apply_date') ? ' is-invalid' : '' }}" id="apply_date" type="text"
                                        name="apply_date" value="{{isset($apply_leave)? date('m/d/Y', strtotime($apply_leave->apply_date)) : date('m/d/Y')}}">
                                    <label>@lang('lang.apply_date')<span>*</span> </label>
                                    <span class="focus-border"></span>
                                     @if ($errors->has('apply_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('apply_date') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="col-auto">
                                <button class="" type="button">
                                    <i class="ti-calendar" id="apply_date_icon"></i>
                                </button>
                            </div>
                           
                        </div>
                        <input type="hidden" name="id" value="{{isset($apply_leave)? $apply_leave->id: ''}}">
                        <div class="row mt-25">
                            <div class="col-lg-12">
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}" name="student_id">
                                    <option data-display="@lang('lang.student') *" value="">@lang('lang.student') *</option>
                                    @foreach($user->parent->myChildrens() as $item)
                                        <option value="{{$item->user_id}}" {{isset($apply_leave)? ($apply_leave->staff_id == $item->user_id? 'selected':''):''}}>{{$item->full_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('student_id'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('student_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-25">
                            <div class="col-lg-12">
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('leave_type') ? ' is-invalid' : '' }}" name="leave_type">
                                    <option data-display="@lang('lang.leave_type') *" value="">@lang('lang.leave_type') *</option>
                                    @foreach($leave_types as $leave_type)
                                        <option value="{{$leave_type->id}}" {{isset($apply_leave)? ($apply_leave->leave_define_id == $leave_type->id? 'selected':''):''}}>{{$leave_type->leaveType->type}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('leave_type'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('leave_type') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row no-gutters input-right-icon mt-25">
                            <div class="col">
                                <div class="input-effect">
                                    <input class="primary-input date form-control{{ $errors->has('leave_from') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                         name="leave_from"  autocomplete="off" value="{{isset($apply_leave)? date('m/d/Y', strtotime($apply_leave->leave_from)):''}}">
                                    <label>@lang('lang.leave_from')<span>*</span> </label>
                                    <span class="focus-border"></span>
                                     @if ($errors->has('leave_from'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('leave_from') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="" type="button">
                                    <i class="ti-calendar" id="start-date-icon"></i>
                                </button>
                            </div>

                        </div>
                        <div class="row no-gutters input-right-icon mt-25">
                            <div class="col">
                                <div class="input-effect">
                                    <input class="primary-input date form-control{{ $errors->has('leave_to') ? ' is-invalid' : '' }}" id="leave_to" type="text" name="leave_to" value="{{isset($apply_leave)? date('m/d/Y', strtotime($apply_leave->leave_to)):''}}">
                                    <label>@lang('lang.leave_to')<span>*</span> </label>
                                    <span class="focus-border"></span>
                                     @if ($errors->has('leave_to'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('leave_to') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="" type="button">
                                    <i class="ti-calendar" id="to-date-icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-25">
                            <div class="col-lg-12">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="4" name="reason">{{isset($apply_leave)? $apply_leave->reason: ''}}</textarea>
                                     <label>@lang('lang.reason') <span>*</span> </label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row no-gutters input-right-icon mt-25">
                            <div class="col">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" 
                                    placeholder="{{isset($apply_leave->file) && $apply_leave->file != ""? showPicName($apply_leave->file):'Attach File'}}"
                                    disabled id="placeholderAttachFile">
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="primary-btn-small-input" type="button">
                                    <label class="primary-btn small fix-gr-bg" for="attach_file">@lang('lang.browse')</label>
                                    <input type="file" class="d-none" name="attach_file" id="attach_file">
                                </button>
                            </div>
                        </div>
                          @php 
                                  $tooltip = "";
                                  if(in_array(82, App\GlobalVariable::GlobarModuleLinks())){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{$tooltip}}">
                                    <span class="ti-check"></span>
                                    @if(isset($apply_leave))
                                        @lang('lang.update')
                                    @else
                                        @lang('lang.save')
                                    @endif
                                    @lang('lang.apply_leave')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-0">@lang('lang.leave') @lang('lang.list') </h3>
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
                            <td colspan="7">
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
                            <th>@lang('lang.student')</th>
                            <th>@lang('lang.type')</th>
                            <th>@lang('lang.from')</th>
                            <th>@lang('lang.to')</th>
                            <th>@lang('lang.apply_date')</th>
                            <th>@lang('lang.Status')</th>
                            <th>@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($apply_leaves as $apply_leave)
                        <tr>
                            <td>{{ $apply_leave->student->full_name}}</td>
                            <td>
                                @if($apply_leave->leaveDefine != "" && $apply_leave->leaveDefine->leaveType !="")
                                    {{$apply_leave->leaveDefine->leaveType->type}}
                                @endif
                            </td>
                            <td  data-sort="{{strtotime($apply_leave->leave_from)}}" >
                             {{$apply_leave->leave_from != ""? App\SmGeneralSettings::DateConvater($apply_leave->leave_from):''}}

                            </td>
                            <td  data-sort="{{strtotime($apply_leave->leave_to)}}" >
                               {{$apply_leave->leave_to != ""? App\SmGeneralSettings::DateConvater($apply_leave->leave_to):''}}

                            </td>
                            <td  data-sort="{{strtotime($apply_leave->apply_date)}}" >
                              {{$apply_leave->apply_date != ""? App\SmGeneralSettings::DateConvater($apply_leave->apply_date):''}}

                            </td>
                            <td>
                            @if($apply_leave->approve_status == 'P')
                            <button class="primary-btn small tr-bg">@lang('lang.pending')</button>@endif
                            @if($apply_leave->approve_status == 'A')
                            <button class="primary-btn small tr-bg">@lang('lang.approved')</button>
                            @endif
                            @if($apply_leave->approve_status == 'C')
                            <button class="primary-btn small tr-bg">@lang('lang.cancelled')</button>
                            @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                        @lang('lang.select')
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        @if(in_array(95, App\GlobalVariable::GlobarModuleLinks()))

                                        <a data-modal-size="modal-lg" title="View Leave Details" class="dropdown-item modalLink" href="{{url('parent-view-leave-details-apply', $apply_leave->id)}}">@lang('lang.view')</a>

                                        @endif
                                        @if($apply_leave->approve_status == 'P')
                                        @if(in_array(83, App\GlobalVariable::GlobarModuleLinks()))

                                        <a class="dropdown-item" href="{{url('parent-leave-edit', [$apply_leave->id
                                            ])}}">@lang('lang.edit')</a> 

                                        @endif
                                        @if(in_array(96, App\GlobalVariable::GlobarModuleLinks()))

                                         <a class="dropdown-item" data-toggle="modal" data-target="#deleteApplyLeaveModal{{$apply_leave->id}}"
                                            href="#">@lang('lang.delete')</a>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade admin-query" id="deleteApplyLeaveModal{{$apply_leave->id}}" >
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">@lang('lang.delete') @lang('lang.apply_leave')</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="text-center">
                                            <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                        </div>

                                        <div class="mt-40 d-flex justify-content-between">
                                            <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                             {{ Form::open(['url' => 'parent-leave-delete/'.$apply_leave->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                            <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
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
