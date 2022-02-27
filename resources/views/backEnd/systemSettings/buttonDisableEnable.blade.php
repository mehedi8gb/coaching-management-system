@extends('backEnd.master')
@section('mainContent')
    <style type="text/css">
        #selectStaffsDiv, .forStudentWrapper {
            display: none;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 55px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background: linear-gradient(90deg, #7c32ff 0%, #c738d8 51%, #7c32ff 100%);
        }

        input:focus + .slider {
            box-shadow: 0 0 1px linear-gradient(90deg, #7c32ff 0%, #c738d8 51%, #7c32ff 100%);
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
        .buttons_div_one{
        /* border: 4px solid #FFFFFF; */
        border-radius:12px;

        padding-top: 0px;
        padding-right: 5px;
        padding-bottom: 0px;
        margin-bottom: 4px;
        padding-left: 0px;
         }
        .buttons_div{
        border: 4px solid #19A0FB;
        border-radius:12px
        }
    </style>
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lang.button') @lang('lang.manage')</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('lang.system_settings')</a>
                    <a href="#">@lang('lang.button') @lang('lang.manage')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-6  buttons_div_one">
                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                    @else
                        @if(in_array(464, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-website-url', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                    @endif
                    <div class="white-box">
                            @if(session()->has('message-success'))
                                <div class="alert alert-success">
                                    @lang('lang.inserted_message')
                                </div>
                            @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    @lang('lang.error_message')
                                </div>
                            @endif
                            <div class="row p-0">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-left">

                                        <span style="font-size: 17px; padding-right: 15px;">@lang('lang.website')  </span>
                                        @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        
                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="No action For Demo "> 
                                            @php
                                                if(@$settings->website_btn == 0){
                                                        $permission_id=465;
                                                }else{
                                                        $permission_id=466;
                                                }
                                            @endphp
                                            @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                            <label class="switch">
                                                <input type="checkbox"
                                                    class="switch-website_btn_demo" {{@$settings->website_btn == 0? '':'checked'}}>
                                                <span class="slider round"></span>
                                            </label>
                                            @endif
                                            </span>

                                        
                                        @else
                                        @php
                                            if(@$settings->website_btn == 0){
                                                    $permission_id=465;
                                            }else{
                                                    $permission_id=466;
                                            }
                                        @endphp
                                            @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-website_btn" {{@$settings->website_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                        @endif
                                    
                                    </div>
                                </div>
                            
                                
                            </div>
                            <br>
                            <p> @lang('lang.csu')</p>
                            <div class="row mt-20">
                            
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center justify-content-center">

                                        @php
                                                if($settings->website_url==''){
                                                    $website_url=URL('home');
                                                }else{
                                                    $website_url=$settings->website_url;
                                                }
                                            @endphp
                                            <input type="text" class="form-control{{ $errors->has('website_url') ? ' is-invalid' : '' }}" name="website_url" value="{{@$website_url}}">
                                            @if ($errors->has('website_url'))
                                        
                                            <p class="text-danger">{{ $errors->first('website_url') }}</p>
                                            @endif
                                    </div>
                                </div>
                            
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-center">
                                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled For Demo "> 
                                        <button  style="pointer-events: none;" class="primary-btn fix-gr-bg" type="button" > @lang('lang.update') </button>
                                        </span>
                                    @else
                                        @if(in_array(464, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <button type="submit" class="primary-btn fix-gr-bg" id="search_promote">
                                            <span class=" pr-2"></span>Update</button>
                                        @endif
                                    @endif
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                    </div>
                
                </div>
                <div class="col-lg-6 p-0 buttons_div">
                
                    <div class="white-box">
                        
                            <div class="row">
                            
                            
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-left">
                                        
                                        <span style="font-size: 17px; padding-right: 15px;">@lang('lang.dashboard')  </span>

                                    
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="No action For Demo "> 
                                        @php
                                            if(@$settings->website_btn == 0){
                                                    $permission_id=465;
                                            }else{
                                                    $permission_id=466;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-website_btn_demo" {{@$settings->website_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                        </span>

                                    
                                    @else
                                    @php
                                        if(@$settings->dashboard_btn == 0){
                                                $permission_id=467;
                                        }else{
                                                $permission_id=468;
                                        }
                                    @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch_dashboard_btn" {{@$settings->dashboard_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-center">

                                        <span style="font-size: 17px; padding-right: 15px;">@lang('lang.style')  </span>

                                        
                                    
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="No action For Demo ">
                                        @php
                                            if(@$settings->website_btn == 0){
                                                    $permission_id=465;
                                            }else{
                                                    $permission_id=466;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 ) 
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-website_btn_demo" {{@$settings->website_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                        </span>

                                    
                                    @else
                                    @php
                                        if(@$settings->style_btn == 0){
                                                $permission_id=473;
                                        }else{
                                                $permission_id=474;
                                        }
                                    @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-style_btn" {{@$settings->style_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-20">
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-left">

                                        <span style="font-size: 17px; padding-right: 15px;">@lang('lang.report')   </span>

                                    
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="No action For Demo "> 
                                        @php
                                            if(@$settings->website_btn == 0){
                                                    $permission_id=465;
                                            }else{
                                                    $permission_id=466;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-website_btn_demo" {{@$settings->website_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                        </span>

                                    
                                    @else
                                        @php
                                            if(@$settings->report_btn == 0){
                                                    $permission_id=469;
                                            }else{
                                                    $permission_id=470;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch_report_btn" {{@$settings->report_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-center">

                                        <span style="font-size: 17px; padding-right: 15px;">@lang('lang.ltl_rtl')  </span>

                                    
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="No action For Demo ">
                                        @php
                                            if(@$settings->website_btn == 0){
                                                    $permission_id=465;
                                            }else{
                                                    $permission_id=466;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 ) 
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-website_btn_demo" {{@$settings->website_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif

                                        </span>

                                    
                                    @else
                                    @php
                                        if(@$settings->ltl_rtl_btn == 0){
                                                $permission_id=475;
                                        }else{
                                                $permission_id=476;
                                        }
                                    @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                            <label class="switch">
                                                <input type="checkbox"
                                                    class="switch_ltl_rtl_btn" {{@$settings->ltl_rtl_btn == 0? '':'checked'}}>
                                                <span class="slider round"></span>
                                            </label>
                                        @endif
                                        @endif
                                </div>
                            
                            </div>
                            <div class="row mt-20">
                                
                                <div class="col-lg-4">
                                    <div class="d-flex align-items-center justify-content-left">

                                        <span style="font-size: 17px; padding-right: 15px;">@lang('lang.language')   </span>

                                    
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
                                        
                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="No action For Demo ">
                                        @php
                                            if(@$settings->website_btn == 0){
                                                    $permission_id=465;
                                            }else{
                                                    $permission_id=466;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 ) 
                                        <label class="switch">
                                            <input type="checkbox"
                                                class="switch-website_btn_demo" {{@$settings->website_btn == 0? '':'checked'}}>
                                            <span class="slider round"></span>
                                        </label>
                                        @endif
                                        </span>

                                    
                                    @else
                                    <label class="switch">
                                        @php
                                            if(@$settings->lang_btn == 0){
                                                    $permission_id=471;
                                            }else{
                                                    $permission_id=472;
                                            }
                                        @endphp
                                        @if(in_array(@$permission_id, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                        <input type="checkbox"
                                            class="switch_lang_btn" {{@$settings->lang_btn == 0? '':'checked'}}>
                                        <span class="slider round"></span>
                                    </label>
                                        @endif
                                    @endif
                                </div>
                            </div>
                    </div>
                
                </div>
            </div>
        </div>
    </section>
@endsection
