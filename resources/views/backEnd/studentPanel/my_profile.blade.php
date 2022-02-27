@extends('backEnd.master')
@section('mainContent')

@php
    function showPicName($data){
        @$name = explode('/', @$data);
        if(!empty(@$name[4])){

        return $name[4];
        }else{
            return '';
        }
    }
@endphp

@php  @$setting = App\SmGeneralSettings::find(1);  if(!empty(@$setting->currency_symbol)){ @$currency = @$setting->currency_symbol; }else{ @$currency = '$'; }   @endphp 

<section class="student-details">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-3">
                <!-- Start Student Meta Information -->
                <div class="main-title">
                    <h3 class="mb-20">@lang('lang.student_profile') </h3>
                </div>
                <div class="student-meta-box">
                    <div class="student-meta-top"></div>
                    <img class="student-meta-img img-100" src="{{asset(@$student_detail->student_photo)}}" alt="">
                    <div class="white-box radius-t-y-0">
                        <div class="single-meta mt-10">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('lang.student_name')
                                </div>
                                <div class="value">
                                    {{@$student_detail->first_name.' '.@$student_detail->last_name}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('lang.admission_number')
                                </div>
                                <div class="value">
                                    {{@$student_detail->admission_no}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('lang.roll_number')
                                </div>
                                <div class="value">
                                     {{@$student_detail->roll_no}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    Class
                                </div>
                                <div class="value">
                                   {{@$student_detail->className != ""? @$student_detail->className->class_name:''}} ({{@$student_detail->session_id != ""? @$academic_year->year:''}})
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('lang.section')
                                </div>
                                <div class="value">
                                    {{@$student_detail->section != ""? @$student_detail->section->section_name:""}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('lang.gender') 
                                </div>
                                <div class="value">
                                    {{@$student_detail->gender!= ""? @$student_detail->gender->base_setup_name:""}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Student Meta Information -->

                <!-- Start Siblings Meta Information -->
                <div class="main-title mt-40">
                    <h3 class="mb-20">@lang('lang.sibling_snformation') </h3>
                </div>
                @foreach($siblings as $sibling)
                    @if(@$sibling->id != @$student_detail->id)
                    <div class="student-meta-box mb-20">
                        <div class="student-meta-top siblings-meta-top"></div>
                        <img class="student-meta-img img-100" src="{{asset(@$sibling->student_photo)}}" alt="">
                        <div class="white-box radius-t-y-0">
                            <div class="single-meta mt-10">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('lang.sibling_name')
                                    </div>
                                    <div class="value">
                                        {{@$sibling->full_name}}
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('lang.admission') @lang('lang.number') 
                                    </div>
                                    <div class="value">
                                        {{@$sibling->admission_no}}
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('lang.roll') @lang('lang.number') 
                                    </div>
                                    <div class="value">
                                        {{@$sibling->roll_no}}
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        Class
                                    </div>
                                    <div class="value">
                                       {{@$sibling->className !=""?@$sibling->className->class_name:""}}
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('lang.section') 
                                    </div>
                                    <div class="value">
                                        {{@$sibling->section !=""?@$sibling->section->section_name:""}}
                                    </div>
                                </div>
                            </div>
                            <div class="single-meta">
                                <div class="d-flex justify-content-between">
                                    <div class="name">
                                        @lang('lang.gender') 
                                    </div>
                                    <div class="value">
                                        {{@$sibling->gender !=""?@$sibling->gender->base_setup_name:""}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
                <!-- End Siblings Meta Information -->
            </div>

            <!-- Start Student Details -->
            <div class="col-lg-9">
                <ul class="nav nav-tabs" role="tablist">
                    @if(@in_array(12, App\GlobalVariable::GlobarModuleLinks()))
                        <li class="nav-item">
                            <a class="nav-link active" href="#studentProfile" role="tab" data-toggle="tab"> @lang('lang.profile') </a>
                        </li>
                    @endif
                    @if(@in_array(13, App\GlobalVariable::GlobarModuleLinks()))
                        <li class="nav-item">
                            <a class="nav-link" href="#studentFees" role="tab" data-toggle="tab">@lang('lang.fees')</a>
                        </li>
                    @endif
                    @if(@in_array(14, App\GlobalVariable::GlobarModuleLinks()))
                        <li class="nav-item">
                            <a class="nav-link" href="#studentExam" role="tab" data-toggle="tab">@lang('lang.exam')</a>
                        </li>
                    @endif
                    @if(@in_array(15, App\GlobalVariable::GlobarModuleLinks()))
                        <li class="nav-item">
                            <a class="nav-link" href="#studentDocuments" role="tab" data-toggle="tab">@lang('lang.documents')</a>
                        </li>
                    @endif
                    @if(@in_array(19, App\GlobalVariable::GlobarModuleLinks()))
                        <li class="nav-item">
                            <a class="nav-link" href="#studentTimeline" role="tab" data-toggle="tab">@lang('lang.timeline')</a>
                        </li>
                    @endif
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Start Profile Tab -->
                    <div role="tabpanel" class="tab-pane fade  show active" id="studentProfile">
                        <div class="white-box">
                            <h4 class="stu-sub-head">@lang('lang.Personal') @lang('lang.info')</h4>
                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.admission')  @lang('lang.date')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">                                                                                
                                        {{@$student_detail->admission_date != ""? App\SmGeneralSettings::DateConvater(@$student_detail->admission_date):''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                            @lang('lang.date_of_birth')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">                                                                                        
                                            {{@$student_detail->date_of_birth != ""? App\SmGeneralSettings::DateConvater(@$student_detail->date_of_birth):''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                            @lang('lang.type')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">
                                            {{@$student_detail->category != ""? @$student_detail->category->catgeory_name:""}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                            @lang('lang.religion') 
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">
                                            {{@$student_detail->religion != ""? @$student_detail->religion->base_setup_name:""}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                            @lang('lang.phone')  @lang('lang.number') 
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">
                                            {{@$student_detail->mobile}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                              @lang('lang.email') @lang('lang.address') 
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">
                                            {{@$student_detail->email}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                            @lang('lang.present')  @lang('lang.address')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">
                                           {{@$student_detail->current_address}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-6">
                                        <div class="">
                                            @lang('lang.permanent') @lang('lang.address')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-7">
                                        <div class="">
                                            {{@$student_detail->permanent_address}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Start Parent Part -->
                            <h4 class="stu-sub-head mt-40">@lang('lang.parent') / @lang('lang.guardian') @lang('lang.details')</h4>
                            <div class="d-flex">
                                <div class="mr-20 mt-20">
                                    <img class="student-meta-img img-100" src="{{@$student_detail->parents != ""? asset(@$student_detail->parents->fathers_photo):""}}" alt="">
                                </div>
                                <div class="w-100">
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.father_name')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""? @$student_detail->parents->fathers_name:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.occupation')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""? @$student_detail->parents->fathers_occupation:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.phone') @lang('lang.number')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""? @$student_detail->parents->fathers_mobile:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="mr-20 mt-20">
                                    <img class="student-meta-img img-100" src="{{@$student_detail->parents != ""? asset(@$student_detail->parents->mothers_photo):""}}" alt="">
                                </div>
                                <div class="w-100">
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.mother_name')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""? @$student_detail->parents->mothers_name:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                     @lang('lang.occupation')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->mothers_occupation:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                     @lang('lang.phone') @lang('lang.number')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->mothers_mobile:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="mr-20 mt-20">
                                    <img class="student-meta-img img-100" src="{{@$student_detail->parents != ""?asset(@$student_detail->parents->guardians_photo):""}}" alt="">
                                </div>
                                <div class="w-100">
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.guardian_name')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->guardians_mobile:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.email') @lang('lang.address')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->guardians_email:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.phone')  @lang('lang.number')
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->guardians_phone:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.relation_with_guardian') 
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->guardians_relation:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.occupation') 
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->guardians_occupation:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="">
                                                    @lang('lang.guardian_address') 
                                                </div>
                                            </div>

                                            <div class="col-lg-8 col-md-7">
                                                <div class="">
                                                    {{@$student_detail->parents != ""?@$student_detail->parents->guardians_address:""}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Parent Part -->

                            <!-- Start Transport Part -->
                            <h4 class="stu-sub-head mt-40">@lang('lang.transport_and_dormitory_details')</h4>
                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.route')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{@$student_detail->route != ""? @$student_detail->route->title: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.vehicle_number')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{@$student_detail->vehicle != ""? @$student_detail->vehicle->vehicle_no: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.driver_name')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{@$student_detail->vehicle != ""? @$driver->full_name: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.driver_phone_number')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{@$student_detail->vehicle != ""? @$driver->mobile: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.dormitory_name')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{@$student_detail->dormitory != ""? @$student_detail->dormitory->dormitory_name: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Transport Part -->

                            <!-- Start Other Information Part -->
                            <h4 class="stu-sub-head mt-40">@lang('lang.information') @lang('lang.other')</h4>
                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.blood_group')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                           {{@$student_detail->bloodGroup != ""? @$student_detail->bloodGroup->base_setup_name: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.height')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->height)? @$student_detail->height: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.weight')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->weight)? @$student_detail->weight: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.previous_school_details')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->previous_school_details)? @$student_detail->previous_school_details: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.national_identification_number')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->national_id_no)? @$student_detail->national_id_no: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.local_identification_number')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->local_id_no)? @$student_detail->local_id_no: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.bank_account_number')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->bank_account_no)? @$student_detail->bank_account_no: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.bank_name')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            {{isset($student_detail->bank_name)? @$student_detail->bank_name: ''}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-info">
                                <div class="row">
                                    <div class="col-lg-5 col-md-5">
                                        <div class="">
                                            @lang('lang.IFSC_Code')
                                        </div>
                                    </div>

                                    <div class="col-lg-7 col-md-6">
                                        <div class="">
                                            @lang('lang.UBC56987')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Other Information Part -->
                        </div>
                    </div>
                    <!-- End Profile Tab -->

                    <!-- Start Fees Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="studentFees">
                        <table class="display school-table school-table-style" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th> @lang('lang.fees_group')</th>
                                    <th>@lang('lang.fees_code')</th>
                                    <th>@lang('lang.due_date')</th>
                                    <th>@lang('lang.status')</th>
                                    <th>@lang('lang.amount') ({{$currency}})</th>
                                    <th>@lang('lang.payment_id')</th>
                                    <th>@lang('lang.mode')</th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.discount') ({{$currency}})</th>
                                    <th>@lang('lang.fine')({{$currency}})</th>
                                    <th>@lang('lang.paid') ({{$currency}})</th>
                                    <th>@lang('lang.balance')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    @$grand_total = 0;
                                    @$total_fine = 0;
                                    @$total_discount = 0;
                                    @$total_paid = 0;
                                    @$total_grand_paid = 0;
                                    @$total_balance = 0;
                                @endphp
                                @foreach($fees_assigneds as $fees_assigned)
                                    @php
                                        if(@$fees_assigned->feesGroupMaster->fees_group_id != 1 && @$fees_assigned->feesGroupMaster->fees_group_id != 2){
                                            @$grand_total += @$fees_assigned->feesGroupMaster->amount;
                                        }else{
                                            if(@$fees_assigned->feesGroupMaster->fees_group_id == 1){
                                                @$grand_total += @$student_detail->route !=""?@$student_detail->route->far:0;
                                            }else{
                                                @$grand_total += @$student_detail->room->cost_per_bed;
                                            }
                                        }
                                        
                                    @endphp

                                    @php
                                        @$discount_amount = App\SmFeesAssign::discountSum(@$fees_assigned->student_id, @$fees_assigned->feesGroupMaster->feesTypes->id, 'discount_amount');
                                        @$total_discount += @$discount_amount;
                                        @$student_id = @$fees_assigned->student_id;
                                    @endphp
                                    @php
                                        @$paid = App\SmFeesAssign::discountSum(@$fees_assigned->student_id, @$fees_assigned->feesGroupMaster->feesTypes->id, 'amount');
                                        @$total_grand_paid += @$paid;
                                    @endphp
                                    @php
                                        @$fine = App\SmFeesAssign::discountSum(@$fees_assigned->student_id, @$fees_assigned->feesGroupMaster->feesTypes->id, 'fine');
                                        @$total_fine += @$fine;
                                    @endphp
                                        
                                    @php
                                        @$total_paid = @$discount_amount + @$paid;
                                    @endphp
                                <tr>
                                    <td>{{@$fees_assigned->feesGroupMaster->feesGroups !=""?@$fees_assigned->feesGroupMaster->feesGroups->name:""}}</td>
                                    <td>{{@$fees_assigned->feesGroupMaster->feesTypes!=""?@$fees_assigned->feesGroupMaster->feesTypes->name:""}}</td>
                                    <td>
                                        @if(!empty(@$fees_assigned->feesGroupMaster))                                                                            
                                        {{@$fees_assigned->feesGroupMaster->date != ""? App\SmGeneralSettings::DateConvater(@$fees_assigned->feesGroupMaster->date):''}}
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$fees_assigned->feesGroupMaster->fees_group_id != 1 && @$fees_assigned->feesGroupMaster->fees_group_id != 2)
                                            @if(@$fees_assigned->feesGroupMaster->amount == @$total_paid)
                                            <button class="primary-btn small bg-success text-white border-0">Paid</button>
                                            @elseif(@$total_paid != 0)
                                            <button class="primary-btn small bg-warning text-white border-0">Partial</button>
                                            @elseif(@$total_paid == 0)
                                            <button class="primary-btn small bg-danger text-white border-0">Unpaid</button>
                                            @endif
                                        @else
                                            @if(@$fees_assigned->feesGroupMaster->fees_group_id == 1)
                                                @if(@$student_detail->route !=""?@$student_detail->route->far:0 == @$total_paid)
                                                <button class="primary-btn small bg-success text-white border-0">Paid</button>
                                                @elseif(@$total_paid != 0)
                                                <button class="primary-btn small bg-warning text-white border-0">Partial</button>
                                                @elseif(@$total_paid == 0)
                                                <button class="primary-btn small bg-danger text-white border-0">Unpaid</button>
                                                @endif
                                            @elseif(@$fees_assigned->feesGroupMaster->fees_group_id == 2)
                                                @if(@$student_detail->room->cost_per_bed == @$total_paid)
                                                <button class="primary-btn small bg-success text-white border-0">Paid</button>
                                                @elseif(@$total_paid != 0)
                                                <button class="primary-btn small bg-warning text-white border-0">Partial</button>
                                                @elseif(@$total_paid == 0)
                                                <button class="primary-btn small bg-danger text-white border-0">Unpaid</button>
                                                @endif
                                            @endif    
                                        @endif    
                                    </td>
                                    <td>
                                        @php
                                            if(@$fees_assigned->feesGroupMaster->fees_group_id != 1 && @$fees_assigned->feesGroupMaster->fees_group_id != 2){
                                                echo @$fees_assigned->feesGroupMaster->amount;
                                            }else{
                                                if(@$fees_assigned->feesGroupMaster->fees_group_id == 1){
                                                    echo @$student_detail->route !=""?@$student_detail->route->far:0;
                                                }else{
                                                    echo @$student_detail->room->cost_per_bed;
                                                }
                                            }
                                            
                                        @endphp
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td> {{@$discount_amount}} </td>
                                    <td>{{@$fine}}</td>
                                    <td>{{@$paid}}</td>
                                    <td>
                                        @php 

                                            if(@$fees_assigned->feesGroupMaster->fees_group_id != 1 && @$fees_assigned->feesGroupMaster->fees_group_id != 2){
                                                @$rest_amount = @$fees_assigned->feesGroupMaster->amount - @$total_paid;
                                            }else{
                                                if(@$fees_assigned->feesGroupMaster->fees_group_id == 1){
                                                    @$rest_amount = @$student_detail->route !=""?@$student_detail->route->far:0 - @$total_paid;
                                                }else{
                                                    @$rest_amount = @$student_detail->room->cost_per_bed - @$total_paid;
                                                }
                                            }

                                            @$total_balance +=  @$rest_amount;
                                            echo @$rest_amount;
                                        @endphp
                                    </td>
                                </tr>
                                    @php 
                                        @$payments = App\SmFeesAssign::feesPayment(@$fees_assigned->feesGroupMaster->feesTypes->id, @$fees_assigned->student_id);
                                        $i = 0;
                                    @endphp

                                    @foreach($payments as $payment)
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right"><img src="{{asset('public/backEnd/img/table-arrow.png')}}"></td>
                                        <td>
                                            @php
                                                @$created_by = App\User::find(@$payment->created_by);
                                            @endphp
                                            @if(@$created_by != "")
                                            <a href="#" data-toggle="tooltip" data-placement="right" title="{{'Collected By: '.@$created_by->full_name}}">{{@$payment->fees_type_id.'/'.@$payment->id}}</a></td>
                                            @endif
                                        <td>
                                        @if(@$payment->payment_mode == "C")
                                        {{'Cash'}}
                                @elseif(@$payment->payment_mode == "Cq")
                                    {{'Cheque'}}
                                @elseif('DD')
                                    {{'DD'}}
                                @elseif('PS')
                                    {{'Paystack'}}
                                @endif
                                        </td>
                                        <td>                                                                                
                                        {{@$payment->payment_date != ""? App\SmGeneralSettings::DateConvater(@$payment->payment_date):''}}
                                        </td>
                                        <td>{{@$payment->discount_amount}}</td>
                                        <td>{{@$payment->fine}}</td>
                                        <td>{{@$payment->amount}}</td>
                                        <td></td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>@lang('lang.grand_total') ({{@$currency}})</th>
                                    <th></th>
                                    <th>{{@$grand_total}}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>{{@$total_discount}}</th>
                                    <th>{{@$total_fine}}</th>
                                    <th>{{@$total_grand_paid}}</th>
                                    <th>{{@$total_balance}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- End Profile Tab -->
                    
                    <!-- Start Exam Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="studentExam">
                         @if(@$exams->count()<1)
                       <div class="white-box no-search no-paginate no-table-info mb-2">
                                <h3 class="text-center text-danger">@lang('lang.exam_not_found')</h3>
                       </div>
                       @endif
                        @foreach($exams as $exam)

                        <div class="white-box no-search no-paginate no-table-info mb-2">
                            <div class="main-title">
                                <h3 class="mb-0">{{@$exam->exam != ""? @$exam->exam->name:''}}</h3>
                            </div>
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.subject')</th>
                                        <th>@lang('lang.full_marks')</th>
                                        <th>@lang('lang.passing_marks')</th>
                                        <th>@lang('lang.obtained_marks')</th>
                                        <th>@lang('lang.results')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        @$marks = App\SmStudent::marks(@$exam->exam_id, @$student_detail->id);
                                        @$grand_total = 0;
                                        @$grand_total_marks = 0;
                                        @$result = 0;

                                    @endphp
                                    @foreach($marks as $mark)
                                        @php
                                            @$subject_marks = App\SmStudent::fullMarks(@$exam->id, @$mark->subject_id);
                                            @$result_subject = 0;
                                            @$grand_total_marks += @$subject_marks->full_mark;
                                            if(@$mark->abs == 0){
                                                @$grand_total += @$mark->marks;
                                                if(@$mark->marks < @$subject_marks->pass_mark){
                                                   @$result_subject++;
                                                   @$result++;
                                                }

                                            }else{
                                                @$result_subject++;
                                                @$result++;
                                            }
                                        @endphp
                                    <tr>
                                        <td>{{@$mark->subject !=""?@$mark->subject->subject_name:""}}</td>
                                        <td>{{@$subject_marks->full_mark}}</td>
                                        <td>{{@$subject_marks->pass_mark}}</td>
                                        <td>{{@$mark->marks}}</td>
                                        <td>
                                            @if(@$result_subject == 0)
                                                <button class="primary-btn small bg-success text-white border-0">@lang('lang.pass')</button>
                                            @else
                                                <button class="primary-btn small bg-danger text-white border-0">@lang('lang.fail')</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>@lang('lang.grand_total'): {{@$grand_total}}/{{@$grand_total_marks}}</th>
                                        <th></th>
                                        <th>@lang('lang.grade'): 
                                            @php
                                                if(@$result == 0 && @$grand_total_marks != 0){

                                                    @$percent = @$grand_total/@$grand_total_marks*100;
                                                    foreach($grades as $grade){
                                                       if(@$percent >= @$grade->percent_from && @$percent <= @$grade->percent_upto){
                                                           echo @$grade->grade_name;
                                                       }
                                                    }
                                                }else{
                                                    echo "F";
                                                }
                                            @endphp
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                         @endforeach
                    </div>
                    <!-- End Exam Tab -->
                  
                    <!-- Start Documents Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="studentDocuments">
                        <div class="white-box">
                            <div class="text-right mb-20">
                                @if(@in_array(16, App\GlobalVariable::GlobarModuleLinks()))
                                    <button type="button" data-toggle="modal" data-target="#add_document_madal" class="primary-btn tr-bg text-uppercase bord-rad">
                                        @lang('lang.upload_document')
                                        <span class="pl ti-upload"></span>
                                    </button>
                                @endif
                            </div>
                            <table id="" class="table simple-table table-responsive school-table"
                                cellspacing="0">
                                <thead class="d-block">
                                    <tr class="d-flex">
                                        <th class="col-3">@lang('lang.document_title')</th>
                                        <th class="col-6">@lang('lang.name')</th>
                                        <th class="col-3">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody class="d-block">
                                    @if($student_detail->document_file_1 != "")
                                    <tr class="d-flex">
                                        <td class="col-3">title</td>
                                        <td class="col-6">dgdsg</td>
                                        <td class="col-3">
                                            @if(@in_array(17, App\GlobalVariable::GlobarModuleLinks()))
                                                <button class="primary-btn tr-bg text-uppercase bord-rad">
                                                    Download
                                                    <span class="pl ti-download"></span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($student_detail->document_file_2 != "")
                                    <tr class="d-flex">
                                        <td class="col-3">title</td>
                                        <td class="col-6">dgdsg</td>
                                        <td class="col-3">
                                            @if(@in_array(17, App\GlobalVariable::GlobarModuleLinks()))
                                                <button class="primary-btn tr-bg text-uppercase bord-rad">
                                                    Download
                                                    <span class="pl ti-download"></span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($student_detail->document_file_3 != "")
                                    <tr class="d-flex">
                                        <td class="col-3">title</td>
                                        <td class="col-6">dgdsg</td>
                                        <td class="col-3">
                                            @if(@in_array(17, App\GlobalVariable::GlobarModuleLinks()))
                                                <button class="primary-btn tr-bg text-uppercase bord-rad">
                                                    Download
                                                    <span class="pl ti-download"></span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($student_detail->document_file_4 != "")
                                    <tr class="d-flex">
                                        <td class="col-3">title</td>
                                        <td class="col-6">dgdsg</td>
                                        <td class="col-3">
                                            @if(@in_array(17, App\GlobalVariable::GlobarModuleLinks()))
                                                <button class="primary-btn tr-bg text-uppercase bord-rad">
                                                    Download
                                                    <span class="pl ti-download"></span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @foreach($documents as $document)
                                    <tr class="d-flex">
                                        <td class="col-3">{{@$document->title}}</td>
                                        <td class="col-6">{{showPicName(@$document->file)}}</td>
                                        <td class="col-3">
                                            @if(@in_array(17, App\GlobalVariable::GlobarModuleLinks()))
                                                <a class="primary-btn tr-bg text-uppercase bord-rad" href="{{url('student-download-document/'.showPicName(@$document->file))}}">
                                                    Download<span class="pl ti-download"></span>
                                                </a>
                                            @endif
                                            @if(@$document->type=='stu')
                                                @if(@in_array(18, App\GlobalVariable::GlobarModuleLinks()))
                                                    <a class="primary-btn icon-only bg-danger text-light" data-toggle="modal" data-target="#deleteDocumentModal{{@$document->id}}"  href="#">
                                                        <span class="ti-trash"></span>
                                                    </a>
                                                @endif
                                            @else
                                                <a></a>
                                            @endif
                                            
                                           
                                        </td>
                                    </tr>
                                    <div class="modal fade admin-query" id="deleteDocumentModal{{@$document->id}}" >
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Delete</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>Are you sure to detete this item?</h4>
                                                    </div>

                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>
                                                        <a class="primary-btn fix-gr-bg" href="{{route('delete_document', [@$document->id])}}">
                                                        Delete</a>
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
                    <!-- End Documents Tab -->
                    <!-- Add Document modal form start-->
                    <div class="modal fade admin-query" id="add_document_madal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Upload Document</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                   <div class="container-fluid">
                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student_upload_document',
                                                            'method' => 'POST', 'enctype' => 'multipart/form-data', 'name' => 'document_upload']) }}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <input type="hidden" name="student_id" value="{{@$student_detail->id}}">
                                                    <div class="row mt-25">
                                                        <div class="col-lg-12">
                                                            <div class="input-effect">
                                                                <input class="primary-input form-control{" type="text" name="title" value="" id="title">
                                                                <label>Title <span>*</span> </label>
                                                                <span class="focus-border"></span>
                                                                
                                                                <span class=" text-danger" role="alert" id="amount_error">
                                                                    
                                                                </span>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 mt-30">
                                                    <div class="row no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <input class="primary-input" type="text" id="placeholderPhoto" placeholder="Document"
                                                                    disabled>
                                                                <span class="focus-border"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="primary-btn small fix-gr-bg" for="photo">browse</label>
                                                                <input type="file" class="d-none" name="photo" id="photo">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- <div class="col-lg-12 text-center mt-40">
                                                    <button class="primary-btn fix-gr-bg" id="save_button_sibling" type="button">
                                                        <span class="ti-check"></span>
                                                        save information
                                                    </button>
                                                </div> -->
                                                <div class="col-lg-12 text-center mt-40">
                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>

                                                        <button class="primary-btn fix-gr-bg" type="submit">save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Add Document modal form end-->
                    <!-- delete document modal -->

                    <!-- delete document modal -->
                    <!-- Start Timeline Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="studentTimeline">
                        <div class="white-box">
                            @foreach($timelines as $timeline)
                            <div class="student-activities">
                                <div class="single-activity">
                                    <h4 class="title text-uppercase">                                                                            
                                    {{@$timeline->date != ""? App\SmGeneralSettings::DateConvater(@$timeline->date):''}}
                                    </h4>
                                    <div class="sub-activity-box d-flex">
                                        <h6 class="time text-uppercase">{{date('h:i A', strtotime(@$timeline->date))}}</h6>
                                        <div class="sub-activity">
                                            <h5 class="subtitle text-uppercase"> {{@$timeline->title}}</h5>
                                            <p>
                                                {{@$timeline->description}}
                                            </p>
                                        </div>

                                        <div class="close-activity">
                                            @if(@$timeline->file != "")
                                            <a href="{{url('download-timeline-doc/'.showPicName(@$timeline->file))}}" class="primary-btn tr-bg text-uppercase bord-rad">
                                                Download<span class="pl ti-download"></span>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- End Timeline Tab -->
                </div>
            </div>
            <!-- End Student Details -->
        </div>

            
    </div>
</section>

<!-- timeline form modal start-->
<div class="modal fade admin-query" id="add_timeline_madal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Timeline</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
               <div class="container-fluid">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student_timeline_store',
                                        'method' => 'POST', 'enctype' => 'multipart/form-data', 'name' => 'document_upload']) }}
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="student_id" value="{{@$student_detail->id}}">
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{" type="text" name="title" value="" id="title">
                                            <label>Title <span>*</span> </label>
                                            <span class="focus-border"></span>
                                            
                                            <span class=" text-danger" role="alert" id="amount_error">
                                                
                                            </span>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-30">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date form-control" id="startDate" type="text"
                                                 name="date">
                                                <label>Date</label>
                                                <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="start-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-30">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="3" name="description" id="Description"></textarea>
                                    <label>Description<span></span> </label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-30">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderFileFourName" placeholder="Document"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg" for="document_file_4">browse</label>
                                            <input type="file" class="d-none" name="document_file_4" id="document_file_4">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-30">
                                
                                <input type="checkbox" id="currentAddressCheck" class="common-checkbox" name="visible_to_student" value="1">
                                <label for="currentAddressCheck">Visible to this person</label>
                            </div>


                            <!-- <div class="col-lg-12 text-center mt-40">
                                <button class="primary-btn fix-gr-bg" id="save_button_sibling" type="button">
                                    <span class="ti-check"></span>
                                    save information
                                </button>
                            </div> -->
                            <div class="col-lg-12 text-center mt-40">
                                <div class="mt-40 d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>

                                    <button class="primary-btn fix-gr-bg" type="submit">save</button>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>
</div>
<!-- timeline form modal end-->




@endsection
