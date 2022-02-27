
<div class="container-fluid">
    <div class="student-details">
        <div class="student-meta-box">
            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-12 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0 text-center">@lang('lang.Route'): {{@$route->title}}</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="student-meta-box">
                            <div class="single-meta mt-20">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="value text-left">
                                            @lang('lang.vehicle')  @lang('lang.no'):
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="name">
                                            {{@$vehicle->vehicle_no}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="value text-left">
                                            @lang('lang.Vehicle') @lang('lang.model'):
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="name">
                                            {{@$vehicle->vehicle_model}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="value text-left">
                                            @lang('lang.made') 
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="name">
                                            {{@$vehicle->made_year}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (!empty(@$vehicle->driver_id))
                
            
            @php
                @$driver_info=App\SmStaff::where('id','=',@$vehicle->driver_id)->first();
            @endphp
            {{-- {{ dd($driver_info) }} --}}
            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="value text-left">
                            @lang('lang.driver') @lang('lang.name') 
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="name">
                            {{@$driver_info->full_name}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="value text-left">
                            @lang('lang.driver') @lang('lang.licence')   
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="name">
                            {{@$driver_info->driving_license}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="value text-left">
                            @lang('lang.driver') @lang('lang.contact')  
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="name">
                            {{@$vehicle->emergency_mobile}}
                        </div>
                    </div>
                </div>
            </div>
            @endif
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
       