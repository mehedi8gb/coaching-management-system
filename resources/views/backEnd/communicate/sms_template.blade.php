@extends('backEnd.master')
@section('mainContent')
@php
    function showPicName($data){
        $name = explode('/', $data);
        return $name[3];
    }
@endphp
<section class="sms-breadcrumb mb-40 white-box up_breadcrumb">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.sms') @lang('lang.template')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.communicate')</a>
                <a href="#">@lang('lang.sms') @lang('lang.template')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($certificate))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('student-certificate')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif
        <div class="row">
           
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">
                                    @lang('lang.update')
                                @lang('lang.sms') @lang('lang.template')
                            </h3>
                        </div>
                          @if(in_array(50, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1)
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'infixbiometrics/sms-template/'.$template->id, 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                         @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        @if(session()->has('message-success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message-success') }}
                                        </div>
                                        @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                        @endif
                                        
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12 mb-20">

                                        <span class="text-primary">[student_name] [student_email] [student_password] [guardian_name] [guardian_email] [guardian_password] [admission_number] [school_name]</span>

                                    </div>
                                    <div class="col-lg-8">

                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('admission_pro') ? ' is-invalid' : '' }}" cols="0" rows="2" name="admission_pro" maxlength="500">{{isset($template)? $template->admission_pro: old('admission_pro')}}</textarea>
                                            <label>@lang('lang.student_approve_message') (@lang('lang.sms')) <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('admission_pro'))
                                                <span class="error text-danger"><strong>{{ $errors->first('admission_pro') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                                <div class="col-lg-5 d-flex">
                                                    <p class="text-uppercase fw-500 mb-10">@lang('lang.status')</p>
                                                </div>
                                                <div class="col-lg-7">
                                                    
                                                        <div class=" radio-btn-flex ml-20">
                                                            <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionF" value="1" class="common-radio relationButton"  {{@$setting->position == 1? 'checked': ''}}>
                                                                    <label for="positionF">@lang('lang.enable')</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionM" value="2" class="common-radio relationButton"  {{@$setting->position == 2? 'checked': ''}}>
                                                                    <label for="positionM">@lang('lang.disable')</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12 mb-20">

                                        <span class="text-primary">[student_name] [student_email] [student_password] [guardian_name] [guardian_email] [guardian_password] [admission_number] [school_name]</span>

                                    </div>
                                    <div class="col-lg-8">

                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('admission_pro') ? ' is-invalid' : '' }}" cols="0" rows="2" name="admission_pro" maxlength="500">{{isset($template)? $template->admission_pro: old('admission_pro')}}</textarea>
                                            <label>@lang('lang.student_registration_message') (@lang('lang.sms')) <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('admission_pro'))
                                                <span class="error text-danger"><strong>{{ $errors->first('admission_pro') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                                <div class="col-lg-5 d-flex">
                                                    <p class="text-uppercase fw-500 mb-10">@lang('lang.status')</p>
                                                </div>
                                                <div class="col-lg-7">
                                                    
                                                        <div class=" radio-btn-flex ml-20">
                                                            <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionF" value="1" class="common-radio relationButton"  {{@$setting->position == 1? 'checked': ''}}>
                                                                    <label for="positionF">@lang('lang.enable')</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionM" value="2" class="common-radio relationButton"  {{@$setting->position == 2? 'checked': ''}}>
                                                                    <label for="positionM">@lang('lang.disable')</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12 mb-20">

                                        <span class="text-primary">[student_name] [parent_name] [admission_number] [exam_term] [subject] [date] [period] [school_name]</span>

                                    </div>
                                    <div class="col-lg-8">

                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('admission_pro') ? ' is-invalid' : '' }}" cols="0" rows="2" name="admission_pro" maxlength="500">{{isset($template)? $template->admission_pro: old('admission_pro')}}</textarea>
                                            <label>@lang('lang.exam_schedule_message') (@lang('lang.sms')) <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('admission_pro'))
                                                <span class="error text-danger"><strong>{{ $errors->first('admission_pro') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                                <div class="col-lg-5 d-flex">
                                                    <p class="text-uppercase fw-500 mb-10">@lang('lang.status')</p>
                                                </div>
                                                <div class="col-lg-7">
                                                    
                                                        <div class=" radio-btn-flex ml-20">
                                                            <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionF" value="1" class="common-radio relationButton"  {{@$setting->position == 1? 'checked': ''}}>
                                                                    <label for="positionF">@lang('lang.enable')</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionM" value="2" class="common-radio relationButton"  {{@$setting->position == 2? 'checked': ''}}>
                                                                    <label for="positionM">@lang('lang.disable')</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="row mt-25">
                                    <div class="col-lg-12 mb-20">

                                        <span class="text-primary">[student_name] [parent_name] [dues_amount] [fees_name] [dues_date] [school_name]</span>

                                    </div>
                                    <div class="col-lg-8">

                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('admission_pro') ? ' is-invalid' : '' }}" cols="0" rows="2" name="admission_pro" maxlength="500">{{isset($template)? $template->admission_pro: old('admission_pro')}}</textarea>
                                            <label>@lang('lang.dues_fees_message') (@lang('lang.sms')) <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('admission_pro'))
                                                <span class="error text-danger"><strong>{{ $errors->first('admission_pro') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                                <div class="col-lg-5 d-flex">
                                                    <p class="text-uppercase fw-500 mb-10">@lang('lang.status')</p>
                                                </div>
                                                <div class="col-lg-7">
                                                    
                                                        <div class=" radio-btn-flex ml-20">
                                                            <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionF" value="1" class="common-radio relationButton"  {{@$setting->position == 1? 'checked': ''}}>
                                                                    <label for="positionF">@lang('lang.enable')</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="">
                                                                    <input type="radio" name="position" id="positionM" value="2" class="common-radio relationButton"  {{@$setting->position == 2? 'checked': ''}}>
                                                                    <label for="positionM">@lang('lang.disable')</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>


                                

                                {{-- <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('sms_text') ? ' is-invalid' : '' }}" cols="0" rows="4" name="sms_text" maxlength="500">{{isset($template)? $template->sms_text: old('sms_text')}}</textarea>
                                            <label>@lang('lang.body') (@lang('lang.sms')) <span></span></label>
                                            <span class="focus-border textarea"></span>

                                            @if($errors->has('sms_text'))
                                                <span class="error text-danger"><strong>{{ $errors->first('sms_text') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}

                               
	                           @php 
                                  $tooltip = "";
                                  if(in_array(50, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 ){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{$tooltip}}">
                                            <span class="ti-check"></span>
                                            
                                                @lang('lang.update')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</section>
@endsection
