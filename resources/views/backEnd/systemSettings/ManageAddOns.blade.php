@extends('backEnd.master')
@section('mainContent')
<style type="text/css">
    #selectStaffsDiv, .forStudentWrapper{
        display: none;
    }
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
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
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
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
/* th,td{
    font-size: 9px !important;
    padding: 5px !important

} */
</style>

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.module') @lang('lang.manage')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.system_settings')</a>
                <a href="#">@lang('lang.module') @lang('lang.manage')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row"> 
            <div class="col-lg-12">
                <div class="row"> 
                    <div class="col-lg-10 col-xs-6 col-md-6 col-6 no-gutters ">
                        <div class="main-title sm_mb_20 sm2_mb_20 md_mb_20 mb-30 " >
                            <h3 class="mb-0"> @lang('lang.module') @lang('lang.manage')</h3>
                        </div>
                    </div> 
                    {{-- <div class="col-lg-2 col-xs-6 col-md-6 col-6 no-gutters ">
                        <a href="{{url('/ModuleRefresh')}}" class="primary-btn fix-gr-bg small pull-right"> <i class="ti-reload"> </i> Refresh</a>
                    </div> --}}
                </div>
                 
                <div class="row">
                    <div class="col-lg-12"> 
                        <table class="display school-table school-table-style" cellspacing="0" width="100%">
                            <thead>  
                                @if(session()->has('error') != "")
                                <tr>
                                    <th colspan="4">
                                        @if(session()->has('error'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('error') }}
                                            </div>
                                        @endif
                                    </th>
                                </tr>
                                @endif 
                                <tr>
                                    <th>@lang('lang.sl')</th>
                                    <th>@lang('lang.name')</th>
                                    <th>@lang('lang.status')</th>
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $count=1; @endphp 
                                @foreach($is_module_available as $row)
                                    @php  
                                    $is_module_available = 'Modules/' . $row->getName(). '/Providers/' .$row->getName(). 'ServiceProvider.php';
                                    if (! file_exists($is_module_available)){
                                        continue; 
                                    }
                                     @endphp
                                
                                <tr>
                                <td>{{@$count++}}</td>
                                <td>
                                    <a href="{{url(strtolower($row->getName()).'/about')}}">{{@$row->getName()}}  </a>
                                </td>
                                <td> 
                                    @if(@$row->isDisabled())
                                        <a  class="primary-btn small {{@$row->getName()}} bg-warning text-white border-0" href="#"  > @lang('lang.disable') </a>
                                    @else 
                                        <a  class="primary-btn small {{@$row->getName()}} bg-success text-white border-0" href="#"  > @lang('lang.enable') </a>
                                    @endif 
                                    </td>
                                    
                                    <td> 

                                        
                                          @if (file_exists($is_module_available)) 
                                        @php 
                                         

                                        $system_settings= App\SmGeneralSettings::find(1);
                                        $configName = $row->getName();
                                        $availableConfig=$system_settings->$configName;
                                          
                                        // dd($availableConfig);
                                        @endphp
                                        @if($availableConfig==0)
                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'ManageAddOnsValidation', 'method' => 'POST']) }}
                                        <input type="hidden" name="name" value="{{@$configName}}">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-effect">
                                                    <input class="primary-input form-control{{ @$errors->has('purchase_code') ? ' is-invalid' : '' }}" type="text" name="purchase_code" autocomplete="off" value="{{old('purchase_code')}}">
                                                    <label>@lang('lang.purchase') @lang('lang.code')  <span>*</span></label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('purchase_code'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ @$errors->first('purchase_code') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="col-lg-12 text-center">
                                                    @if(in_array(400, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                                        <button class="primary-btn fix-gr-bg" >
                                                            <span class="ti-check"></span> 
                                                                @lang('lang.verify') 
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                        @else 
                                            @if('RolePermission' != $row->getName() && 'TemplateSettings' != $row->getName() ) 
                                                <label class="switch">
                                                    <input type="checkbox" id="ch{{@$row->getName()}}" onclick="changeModule(`{{@$row->getName()}}`)" 
                                                    class="switch-input" {{@$row->isDisabled() == true? '':'checked'}}>
                                                    <span class="slider round"></span>
                                                </label>  
                                            @else
                                            <p class="primary-btn fix-gr-bg small">Defautl</p> 
                                            @endif
                                        @endif

                                        @else
                                        @php Module::find($row->getName())->disable(); @endphp 
                                        <a href="https://spondonit.com/contact/" class="primary-btn fix-gr-bg small">Buy Now </a>
                                        @endif
                                        
                                    </td>


                                </tr> 

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div>
        </div>
</section>

@endsection
