
@extends('backEnd.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('public/backEnd/')}}/css/croppie.css">
@endsection
@section('mainContent')
<section class="sms-breadcrumb up_breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('common.profile_update') </h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('student_list')}}">@lang('common.student_list')</a>
                <a href="#">@lang('common.profile_update') </a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row mb-30">
            <div class="col-lg-6">
                <div class="main-title">
                    <h3>@lang('common.profile_update') </h3>
                </div>
            </div>
        </div>
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'my-children-update',
                        'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'student_form']) }}
        <div class="row">
            <div class="col-lg-12"> 
                <div class="white-box">
                    <div class="">
                        <div class="row mb-4">
                            <div class="col-lg-12 text-center">

                                @if($errors->any())
                                    @foreach ($errors->all() as $error)
                                    @if($error == "The email address has already been taken.")
                                        <div class="error text-danger ">{{ 'The email address has already been taken, You can find out in student list or disabled student list' }}</div>
                                    @endif 
                                    @endforeach
                                @endif

                                @if ($errors->any())
                                     <div class="error text-danger ">{{ 'Something went wrong, please try again' }}</div>
                                @endif
                            </div>
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4 class="stu-sub-head">@lang('common.personal_info')</h4>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}"> 
                        <input type="hidden" name="id" id="id" value="{{$student->id}}"> 

                        <!-- <input type="hidden" name="parent_id" id="parent_id" value="{{$student->parent_id}}">  -->
 
                        <div class="row mb-20">
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" type="text" name="first_name" value="{{$student->first_name}}">
                                    <label>@lang('admin.first_name') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('first_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" type="text" name="last_name" value="{{$student->last_name}}">
                                    <label>@lang('admin.last_name')</label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('last_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('gender') ? ' is-invalid' : '' }}" name="gender">
                                        <option data-display="@lang('common.gender') *" value="">@lang('common.gender') *</option>
                                        @foreach($genders as $gender)
                                            @if(isset($student->gender_id))
                                                <option value="{{$gender->id}}" {{$student->gender_id == $gender->id? 'selected': ''}}>{{$gender->base_setup_name}}</option>
                                            @else
                                                <option value="{{$gender->id}}">{{$gender->base_setup_name}}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('gender'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date form-control{{ $errors->has('date_of_birth') ? ' is-invalid' : '' }}" id="startDate" type="text" name="date_of_birth" value="{{date('m/d/Y', strtotime($student->date_of_birth))}}" autocomplete="off">
                                            <span class="focus-border"></span>
                                            <label>@lang('common.date_of_birth') <span>*</span></label>
                                            @if ($errors->has('date_of_birth'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('date_of_birth') }}</strong>
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
                            </div>
                        </div>


                        <div class="row mb-20">
                             <div class="col-lg-2">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('blood_group') ? ' is-invalid' : '' }}" name="blood_group">
                                        <option data-display="@lang('common.blood_group')" value="">@lang('common.blood_group')</option>
                                        @foreach($blood_groups as $blood_group)
                                        @if(isset($student->bloodgroup_id))
                                            <option value="{{$blood_group->id}}" {{$blood_group->id == $student->bloodgroup_id? 'selected': ''}}>{{$blood_group->base_setup_name}}</option>
                                        @else
                                            <option value="{{$blood_group->id}}">{{$blood_group->base_setup_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('blood_group'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('blood_group') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('religion') ? ' is-invalid' : '' }}" name="religion">
                                        <option data-display="@lang('student.religion')" value="">@lang('student.religion')</option>
                                        @foreach($religions as $religion)
                                            <option value="{{$religion->id}}" {{$student->religion_id != ""? ($student->religion_id == $religion->id? 'selected':''):''}}>{{$religion->base_setup_name}}</option>
                                        }
                                        @endforeach

                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('religion'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('religion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-lg-2">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="caste" value="{{$student->caste}}">
                                    <label>@lang('student.caste')</label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('email_address') ? ' is-invalid' : '' }}" type="text" name="email_address" value="{{$student->email}}">
                                    <label>@lang('common.email_address') <span></span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('email_address'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email_address') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('phone_number') ? ' is-invalid' : '' }}" type="text" name="phone_number" value="{{$student->mobile}}">
                                    <label>@lang('common.phone_number')</label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('phone_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row mb-20">
                            <div class="col-lg-2">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date" id="endDate" type="text" name="admission_date" value="{{$student->admission_date != ""? date('m/d/Y', strtotime($student->admission_date)): date('m/d/Y')}}" autocomplete="off">
                                                <label>@lang('student.admission_date')</label>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="end-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                           
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('student_category_id') ? ' is-invalid' : '' }}" name="student_category_id">
                                        <option data-display="@lang('student.category')" value="">@lang('student.category')</option>
                                        @foreach($categories as $category)
                                        @if(isset($student->student_category_id))
                                        <option value="{{$category->id}}" {{$student->student_category_id == $category->id? 'selected': ''}}>{{$category->category_name}}</option>
                                        @else
                                        <option value="{{$category->id}}">{{$category->category_name}}</option>
                                        @endif
                                        @endforeach

                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('student_category_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('student_category_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-effect">
                                    <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('student_group_id') ? ' is-invalid' : '' }}" name="student_group_id">
                                        <option data-display="@lang('student.group')" value="">@lang('student.group')</option>
                                        @foreach($groups as $group)
                                        @if(isset($student->student_group_id))
                                        <option value="{{$group->id}}" {{$student->student_group_id == $group->id? 'selected': ''}}>{{$group->group}}</option>
                                        @else
                                        <option value="{{$group->id}}">{{$group->group}}</option>
                                        @endif
                                        @endforeach

                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('student_group_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('student_group_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="height" value="{{$student->height}}">
                                    <label>@lang('student.height_in') <span></span> </label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="weight" value="{{$student->weight}}">
                                    <label>@lang('student.weight_kg')  <span></span> </label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                        </div>

                        <div class="row mb-20">
                            
                            <div class="col-lg-3">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderPhoto" placeholder="{{$student->student_photo != ""? getFilePath3($student->student_photo):'Student Photo'}}"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg" for="photo">@lang('common.browse')</label>
                                            <input type="file" class="d-none" name="photo" id="photo">
                                        </button>
                                    </div>
                                </div>
                            </div>
                             
                        </div>  
 



                        <div class="parent_details" id="parent_details">
                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <div class="main-title">
                                        <h4 class="stu-sub-head">@lang('student.parents_and_guardian_info')</h4>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-20">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('fathers_name') ? ' is-invalid' : '' }}" type="text" name="fathers_name" id="fathers_name" value="{{$student->parents->fathers_name}}">
                                        <label>@lang('student.father_name') <span></span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('fathers_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('fathers_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control" type="text" placeholder="" name="fathers_occupation" id="fathers_occupation" value="{{$student->parents->fathers_occupation}}">
                                        <label>@lang('student.occupation')</label>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                                 <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('fathers_phone') ? ' is-invalid' : '' }}" type="text" name="fathers_phone" id="fathers_phone"  value="{{$student->parents->fathers_mobile}}">
                                        <label>@lang('student.father_phone') <span>*</span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('fathers_phone'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('fathers_phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input" type="text" id="placeholderFathersName" placeholder="{{isset($student->parents->fathers_photo) && $student->parents->fathers_photo != ""? getFilePath3($student->parents->fathers_photo):'Photo'}}"
                                                    disabled>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="primary-btn-small-input" type="button">
                                                <label class="primary-btn small fix-gr-bg" for="fathers_photo">@lang('common.browse')</label>
                                                <input type="file" class="d-none" name="fathers_photo" id="fathers_photo">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-20">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('mothers_name') ? ' is-invalid' : '' }}" type="text" name="mothers_name" id="mothers_name"   value="{{$student->parents->mothers_name}}">
                                        <label>@lang('student.mother_name') <span></span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('mothers_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('mothers_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input" type="text" name="mothers_occupation" id="mothers_occupation" value="{{$student->parents->mothers_occupation}}">
                                        <label>@lang('student.occupation')</label>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                                 <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('mothers_phone') ? ' is-invalid' : '' }}" type="text" name="mothers_phone" id="mothers_phone" value="{{$student->parents->mothers_mobile}}">
                                        <label>@lang('student.mother_phone') <span></span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('mothers_phone'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('mothers_phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input" type="text" id="placeholderMothersName" placeholder="{{isset($student->parents->mothers_photo) && $student->parents->mothers_photo != ""? getFilePath3($student->parents->mothers_photo):'Photo'}}"
                                                    disabled>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="primary-btn-small-input" type="button">
                                                <label class="primary-btn small fix-gr-bg" for="mothers_photo">@lang('common.browse')</label>
                                                <input type="file" class="d-none" name="mothers_photo" id="mothers_photo">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <div class="row mb-40">
                                <div class="col-lg-12 d-flex">
                                    <p class="text-uppercase fw-500 mb-10">@lang('student.relation_with_guardian') *</p>
                                    <div class="d-flex radio-btn-flex ml-40">
                                        <div class="mr-30">
                                            <input type="radio" name="relationButton" id="relationFather" value="F" class="common-radio relationButton" {{$student->parents->relation == "F"? 'checked':''}}>
                                            <label for="relationFather">@lang('student.father')</label>
                                        </div>
                                        <div class="mr-30">
                                            <input type="radio" name="relationButton" id="relationMother" value="M" class="common-radio relationButton" {{$student->parents->relation == "M"? 'checked':''}}>
                                            <label for="relationMother">@lang('student.mother')</label>
                                        </div>
                                        <div>
                                            <input type="radio" name="relationButton" id="relationOther" value="O" class="common-radio relationButton" {{$student->parents->relation == "O"? 'checked':''}}>
                                            <label for="relationOther">@lang('student.Other')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        
                            <div class="row mb-20">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('guardians_name') ? ' is-invalid' : '' }}" type="text" name="guardians_name" id="guardians_name" value="{{$student->parents->guardians_name}}">
                                        <label>@lang('student.guardian_name')  <span></span> </label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('guardians_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('guardians_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    if($student->parents->guardians_relation=='F'){
                                            $show_relation="Father";
                                    }
                                    if($student->parents->guardians_relation=='M'){
                                            $relashow_relationtion="Mother";
                                    }
                                    if($student->parents->guardians_relation=='O'){
                                            $show_relation="Other";
                                    }
                                @endphp
                                
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input read-only-input" type="text" placeholder="Relation" name="relation" id="relation" value="{{$student->parents !=""?@$student->parents->guardians_relation:""}}" readonly>
                                        <label>@lang('student.relation_with_guardian') </label>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('guardians_email') ? ' is-invalid' : '' }}" type="text" name="guardians_email" id="guardians_email" value="{{$student->parents->guardians_email}}">
                                        <label>@lang('student.guardian_email') <span>*</span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('guardians_email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('guardians_email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input" type="text" id="placeholderGuardiansName" placeholder="{{isset($student->parents->guardians_photo) && $student->parents->guardians_photo != ""? getFilePath3($student->parents->guardians_photo):'Photo'}}"
                                                    disabled>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="primary-btn-small-input" type="button">
                                                <label class="primary-btn small fix-gr-bg" for="guardians_photo">@lang('common.browse')</label>
                                                <input type="file" class="d-none" name="guardians_photo" id="guardians_photo">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-20">
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('guardians_phone') ? ' is-invalid' : '' }}" type="text" name="guardians_phone" id="guardians_phone" value="{{$student->parents->guardians_mobile}}">
                                        <label>@lang('student.guardian_phone') <span>*</span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('guardians_phone'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('guardians_phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-effect">
                                        <input class="primary-input" type="text" name="guardians_occupation" id="guardians_occupation" value="{{$student->parents->guardians_occupation}}">
                                        <label>@lang('student.guardian_occupation')</label>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                            </div>
                             <div class="row mt-35">
                                <div class="col-lg-6">
                                    <div class="input-effect">
                                        <textarea class="primary-input form-control" cols="0" rows="4" name="guardians_address" id="guardians_address">{{$student->parents->guardians_address}}</textarea>
                                        <label>@lang('student.guardian_address') <span></span> </label>
                                        <span class="focus-border textarea"></span>
                                       @if ($errors->has('guardians_address'))
                                        <span class="danger text-danger">
                                            <strong>{{ $errors->first('guardians_address') }}</strong>
                                        </span>
                                        @endif 
                                    </div>
                                </div>
                            </div>
                        </div>





                       <div class="row mt-40">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4 class="stu-sub-head">@lang('student.student_address_info')</h4>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-30 mt-30">
                            <div class="col-lg-6">

                                <div class="input-effect mt-20">
                                    <textarea class="primary-input form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}" cols="0" rows="3" name="current_address" id="current_address">{{$student->current_address}}</textarea>
                                    <label>@lang('student.current_address') <span></span> </label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">

                                <div class="input-effect mt-20">
                                    <textarea class="primary-input form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}" cols="0" rows="3" name="permanent_address" id="permanent_address">{{$student->permanent_address}}</textarea>
                                    <label>@lang('student.permanent_address') <span></span> </label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-40 mb-4">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4 class="stu-sub-head">@lang('student.Other_info')</h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-20">
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('national_id_number') ? ' is-invalid' : '' }}" type="text" name="national_id_number" value="{{$student->national_id_no}}">
                                    <label>@lang('student.national_id_number') <span></span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('national_id_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('national_id_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" name="local_id_number" value="{{$student->local_id_no}}">
                                    <label>@lang('student.local_Id_Number') <span></span> </label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" name="bank_account_number" value="{{$student->bank_account_no}}">
                                    <label>@lang('student.bank_account_number') <span></span> </label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control" type="text" name="bank_name" value="{{$student->bank_name}}">
                                     <label>@lang('student.bank_name') <span></span> </label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-20 mt-40">
                            <div class="col-lg-6">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="4" name="previous_school_details">{{$student->previous_school_details}}</textarea>
                                    <label>@lang('student.previous_school_details')</label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="4" name="additional_notes">{{$student->aditional_notes}}</textarea>
                                     <label>@lang('student.additional_notes')</label>
                                    <span class="focus-border textarea"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect mt-50">
                                    <input class="primary-input form-control" type="text" name="ifsc_code" value="{{old('ifsc_code')}}{{$student->ifsc_code}}">
                                    <label>@lang('student.ifsc_code')</label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-40 mb-4">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4 class="stu-sub-head">@lang('student.document_info')</h4>
                                </div>
                            </div>
                        </div>
                        
                         <div class="row mb-20">
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="document_title_1" value="{{$student->document_title_1}}">
                                    <label>@lang('student.document_01_title')</label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="document_title_2" value="{{$student->document_title_2}}">
                                    <label>@lang('student.document_02_title')</label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="document_title_3" value="{{$student->document_title_3}}">
                                    <label>@lang('student.document_03_title')</label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input" type="text" name="document_title_4" value="{{$student->document_title_4}}">
                                    <label>@lang('student.document_04_title')</label>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>
                         <div class="row mb-20">
                             <div class="col-lg-3">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="{{$student->document_file_1 != ""? showDocument($student->document_file_1):'01'}}"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg" for="document_file_1">@lang('common.browse')</label>
                                            <input type="file" class="d-none" name="document_file_1" id="document_file_1">
                                        </button>
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-3">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderFileTwoName" placeholder="{{isset($student->document_file_2) && $student->document_file_2 != ""? showDocument($student->document_file_2):'02'}}"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg" for="document_file_2">@lang('common.browse')</label>
                                            <input type="file" class="d-none" name="document_file_2" id="document_file_2">
                                        </button>
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-3">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderFileThreeName" placeholder="{{isset($student->document_file_3) && $student->document_file_3 != ""? showDocument($student->document_file_3):'03'}}"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg" for="document_file_3">@lang('common.browse')</label>
                                            <input type="file" class="d-none" name="document_file_3" id="document_file_3">
                                        </button>
                                    </div>
                                </div>
                            </div>
                             <div class="col-lg-3">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text" id="placeholderFileFourName" placeholder="{{isset($student->document_file_4) && $student->document_file_4 != ""? showDocument($student->document_file_4):'04'}}"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg" for="document_file_4">@lang('common.browse')</label>
                                            <input type="file" class="d-none" name="document_file_4" id="document_file_4">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div class="row mt-5">
                            <div class="col-lg-12 text-center">
                                <button class="primary-btn fix-gr-bg">
                                    <span class="ti-check"></span>
                                    @lang('student.update_student')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</section>


<div class="modal fade admin-query" id="removeSiblingModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('student.remove')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang('student.are_you')</h4>
                    </div>

                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        <button type="button" class="primary-btn fix-gr-bg" data-dismiss="modal" id="yesRemoveSibling">@lang('common.delete')</button>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>


 {{-- student photo --}}
 <input type="text" id="STurl" value="{{ route('student_update_pic',$student->id)}}" hidden>
 <div class="modal" id="LogoPic">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Crop Image And Upload</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div id="resize"></div>
                <button class="btn rotate float-lef" data-deg="90" > 
                <i class="ti-back-right"></i></button>
                <button class="btn rotate float-right" data-deg="-90" > 
                <i class="ti-back-left"></i></button>
                <hr>                
                <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="upload_logo">Crop</a>
            </div>
        </div>
    </div>
</div>
{{-- end student photo --}}

 {{-- father photo --}}

 <div class="modal" id="FatherPic">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Crop Image And Upload</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div id="fa_resize"></div>
                <button class="btn rotate float-lef" data-deg="90" > 
                <i class="ti-back-right"></i></button>
                <button class="btn rotate float-right" data-deg="-90" > 
                <i class="ti-back-left"></i></button>
                <hr>                
                <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="FatherPic_logo">Crop</a>
            </div>
        </div>
    </div>
</div>
{{-- end father photo --}}
 {{-- mother photo --}}

 <div class="modal" id="MotherPic">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Crop Image And Upload</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div id="ma_resize"></div>
                <button class="btn rotate float-lef" data-deg="90" > 
                <i class="ti-back-right"></i></button>
                <button class="btn rotate float-right" data-deg="-90" > 
                <i class="ti-back-left"></i></button>
                <hr>                
                <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="Mother_logo">Crop</a>
            </div>
        </div>
    </div>
</div>
{{-- end mother photo --}}
 {{-- mother photo --}}

 <div class="modal" id="GurdianPic">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Crop Image And Upload</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div id="Gu_resize"></div>
                <button class="btn rotate float-lef" data-deg="90" > 
                <i class="ti-back-right"></i></button>
                <button class="btn rotate float-right" data-deg="-90" > 
                <i class="ti-back-left"></i></button>
                <hr>
                
                <a href="javascript:;" class="primary-btn fix-gr-bg pull-right" id="Gurdian_logo">Crop</a>
            </div>
        </div>
    </div>
</div>
{{-- end mother photo --}}

@endsection
@section('script')
<script src="{{asset('public/backEnd/')}}/js/croppie.js"></script>
<script src="{{asset('public/backEnd/')}}/js/st_addmision.js"></script>
@endsection