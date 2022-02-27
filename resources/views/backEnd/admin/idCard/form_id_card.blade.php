<div class="white-box">
    <div class="add-visitor">
        <div class="row">
            <div class="col-lg-12">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                        type="text" name="title" autocomplete="off" value="{{isset($id_card)? $id_card->title: old('title')}}" id="title">
                    <label>@lang('admin.id_card_title') <span>*</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('title'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                    @endif
                </div>
                
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-12">
                <div class="input-effect">
                    <select class="niceSelect w-100 bb form-control{{ $errors->has('page_layout_style') ? ' is-invalid' : '' }}" name="page_layout_style" id="pageLayoutStyle">
                        <option value="horizontal" {{isset($id_card)? ($id_card->page_layout_style == "horizontal"? 'selected':''):''}}>@lang('admin.horizontal')</option>
                        <option value="vertical" {{isset($id_card)? ($id_card->page_layout_style == "vertical"? 'selected':''):''}}>@lang('admin.vertical')</option>
                    </select>
                        <span class="focus-border"></span>
                        @if ($errors->has('page_layout_style'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('page_layout_style') }}</strong>
                        </span>
                        @endif
                </div>
            </div>
        </div>

        <div class="d-flex mt-25">
            <div class="row flex-grow-1 d-flex justify-content-between input-right-icon">
                <div class="col">
                    <div class="input-effect">
                        <input class="primary-input form-control{{ $errors->has('background_img') ? ' is-invalid' : '' }}" type="text" id="backgroundImage" placeholder="{{isset($id_card)? ($id_card->background_img != ""? getFilePath3($id_card->background_img): trans('admin.background_image')):trans('admin.background_image')}}"
                            readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('background_img'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('background_img') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <button class="primary-btn-small-input cust-margin" type="button">
                        <label class="primary-btn small fix-gr-bg" for="document_file_5">@lang('common.browse')</label>
                        <input type="file" class="d-none" name="background_img" id="document_file_5" onchange="imageChangeWithBackFile(this)" value="{{isset($id_card)? ($id_card->background_img != ""? getFilePath3($id_card->background_img):''): ''}}">
                    </button>
                </div>
            </div>
            <button class="primary-btn icon-only fix-gr-bg" type="button" id="deleteBackImg">
                <span class="ti-trash"></span>
            </button>
        </div>

        <div class="row mt-25">
            <div class="col-lg-12">
                <div class="input-effect">
                    <select class="niceSelect w-100 bb form-control{{ $errors->has('applicable_user') ? ' is-invalid' : '' }}" name="applicable_user[]" id="applicableUser">
                        <option data-display="@lang('admin.applicable_user') *" value="">@lang('common.select')*</option>
                        @if(isset($id_card))
                            @php
                                $applicableUsers = json_decode($id_card->role_id);
                            @endphp
                                <option value="2" {{(in_array(2,$applicableUsers))? 'selected': ''}} selected>@lang('admin.student')</option>
                                <option value="3" {{(in_array(3,$applicableUsers))? 'selected': ''}}>@lang('admin.guardian')</option>
                                <option value="0" 
                                @if((!in_array(3,$applicableUsers)) && (!in_array(2,$applicableUsers)))
                                    {{"selected"}} 
                                @endif
                                >@lang('admin.staff')</option>
                        @else
                            <option value="2">@lang('admin.student')</option>
                            <option value="3">@lang('admin.guardian')</option>
                            <option value="0">@lang('admin.staff')</option>
                        @endif
                    </select>
                        <div class="text-danger" id="applicableUserError"></div>
                        <span class="focus-border"></span>
                        @if ($errors->has('applicable_user'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('applicable_user') }}</strong>
                        </span>
                        @endif
                </div>
            </div>
        </div>

        <div class="row mt-25 staffInfo 
            @if(isset($id_card))
                @if((!in_array(3,$applicableUsers)) && (!in_array(2,$applicableUsers)))
                    {{"d-block"}}
                @else
                    {{"d-none"}}
                @endif
            @else
                {{"d-none"}}
            @endif
            ">
            <div class="col-lg-12">
                <label>@lang('admin.role')*</label><br> 
                @foreach($roles as $role)
                    @if($role->id != 2 && $role->id != 3)
                        <div class="">
                            <input type="checkbox" id="role_{{@$role->id}}" class="common-checkbox" value="{{@$role->id}}" name="role[]"
                            {{isset($id_card)? (in_array($role->id,$applicableUsers)? 'checked':''):''}}>
                            <label for="role_{{@$role->id}}">{{@$role->name}}</label>
                        </div>
                    @endif
                @endforeach 
                @if($errors->has('section'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('section') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-6">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('pl_width') ? ' is-invalid' : '' }}" type="text" name="pl_width" value="{{isset($id_card)? $id_card->pl_width: old('pl_width')}}" id="plWidth" autocomplete="off">
                    <label>@lang('admin.page_layout_width') <span id="pWidth">(@lang('admin.default') 57 mm)</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('pl_width'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('pl_width') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('pl_height') ? ' is-invalid' : '' }}" type="text" name="pl_height" value="{{isset($id_card)? $id_card->pl_height: old('pl_height')}}" id="plHeight" autocomplete="off">
                    <label>@lang('admin.page_layout_height') <span id="pHeight">(@lang('admin.default') 89 mm)</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('pl_height'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('pl_height') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex mt-25">
            <div class="row flex-grow-1 d-flex justify-content-between input-right-icon">
                <div class="col">
                    <div class="input-effect">
                        <input class="primary-input form-control{{ $errors->has('profile_image') ? ' is-invalid' : '' }}" type="text" id="profileImage" placeholder="{{isset($id_card)? ($id_card->profile_image != ""? getFilePath3($id_card->profile_image): trans('admin.profile_image')):trans('admin.profile_image')}}"
                            readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('profile_image'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('profile_image') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <button class="primary-btn-small-input cust-margin" type="button">
                        <label class="primary-btn small fix-gr-bg" for="document_file_6">@lang('common.browse')</label>
                        <input type="file" class="d-none" name="profile_image" id="document_file_6" onchange="imageChangeWithFile(this,'.photo')" value="{{isset($id_card)? ($id_card->profile_image != ""? getFilePath3($id_card->profile_image):''): ''}}">
                    </button>
                </div>
            </div>
            <button class="primary-btn icon-only fix-gr-bg" type="button" id="deleteProImg">
                <span class="ti-trash"></span>
            </button>
        </div>

        <div class="row mt-25">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10"> @lang('admin.user_photo_style') </p>
            </div>
            <div class="col-lg-8">
                <div class="input-effect">
                    <select class="niceSelect w-100 bb form-control{{ $errors->has('user_photo_style') ? ' is-invalid' : '' }}" name="user_photo_style" id="userPhotoStyle">
                        <option data-display="@lang('admin.user_photo_style')" value="">@lang('common.select')</option>
                        <option value="squre" {{isset($id_card)? ($id_card->user_photo_style == "squre"? 'selected':''):''}}>@lang('admin.squre')</option>
                        <option value="round" {{isset($id_card)? ($id_card->user_photo_style == "round"? 'selected':''):''}}>@lang('admin.round')</option>
                    </select>
                    <div class="text-danger" id="applicableUserError"></div>
                    <span class="focus-border"></span>
                    @if ($errors->has('user_photo_style'))
                    <span class="invalid-feedback invalid-select" role="alert">
                        <strong>{{ $errors->first('user_photo_style') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-6">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('user_photo_width') ? ' is-invalid' : '' }}" type="text" id="userPhotoWidth" name="user_photo_width" value="{{isset($id_card)? $id_card->user_photo_width: old('user_photo_width')}}" autocomplete="off">
                    <label>@lang('admin.user_photo_size_width') <span id="profileWidth">(@lang('admin.default') 21 mm)</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('user_photo_width'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{$errors->first('user_photo_width')}}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('user_photo_height') ? ' is-invalid' : '' }}" type="text" id="userPhotoheight" name="user_photo_height" value="{{@$id_card->user_photo_height}}" autocomplete="off">
                    <label>@lang('admin.user_photo_size_height') <span id="profileHeight">(@lang('admin.default') 21 mm)</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('user_photo_height'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('user_photo_height') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-4">
                <span>@lang('admin.layout_spacing')</span>
            </div>
            <div class="col-lg-4">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('t_space') ? ' is-invalid' : '' }}" type="text" id="tSpace" name="t_space" value="{{isset($id_card)? $id_card->t_space: old('t_space')}}" autocomplete="off">
                    <label>@lang('admin.top_space')<span> (@lang('admin.default') 2.5 mm)</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('t_space'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('t_space') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('b_space') ? ' is-invalid' : '' }}" type="text" id="bSpace" name="b_space" value="{{isset($id_card)? $id_card->b_space: old('b_space')}}" autocomplete="off">
                    <label>@lang('admin.bottom_space') <span>(@lang('admin.default') 2.5 mm)</span></label>
                    <span class="focus-border"></span>
                    @if ($errors->has('b_space'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('b_space') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-4">

            </div>
            <div class="col-lg-4">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('l_space') ? ' is-invalid' : '' }}" type="text" id="lSpace" name="l_space" value="{{isset($id_card)? $id_card->l_space: old('l_space')}}" autocomplete="off">
                    <label>@lang('admin.left_space') (@lang('admin.default') 3 mm)</label>
                    <span class="focus-border"></span>
                    @if ($errors->has('l_space'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('l_space') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="input-effect">
                    <input class="primary-input form-control{{ $errors->has('r_space') ? ' is-invalid' : '' }}" type="text" id="rSpace" name="r_space" value="{{isset($id_card)? $id_card->r_space: old('r_space')}}" autocomplete="off">
                    <label>@lang('admin.right_space') (@lang('admin.default') 3 mm)</label>
                    <span class="focus-border"></span>
                    @if ($errors->has('r_space'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('r_space') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="d-flex mt-25">
            <div class="row flex-grow-1 d-flex justify-content-between input-right-icon">
                <div class="col">
                    <div class="input-effect">
                        <input class="primary-input form-control{{ $errors->has('logo') ? ' is-invalid' : '' }}" type="text" id="placeholderFileThreeName" placeholder="{{isset($id_card)? ($id_card->logo != ""? getFilePath3($id_card->logo): trans('admin.logo').' *'):trans('admin.logo') .' *'}}"
                            readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('logo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('logo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <button class="primary-btn-small-input cust-margin" type="button">
                        <label class="primary-btn small fix-gr-bg" for="document_file_3">@lang('common.browse')</label>
                        <input type="file" class="d-none" name="logo" id="document_file_3" onchange="logoImageChangeWithFile(this,'.logoImage')" value="{{isset($id_card)? ($id_card->logo != ""? getFilePath3($id_card->logo):''): ''}}">
                    </button>
                </div>
            </div>
            <button class="primary-btn icon-only fix-gr-bg" type="button" id="deleteLogoImg">
                <span class="ti-trash"></span>
            </button>
        </div>

        <div class="d-flex mt-25">
            <div class="row flex-grow-1 d-flex justify-content-between input-right-icon">
                <div class="col">
                    <div class="input-effect">
                        <input class="primary-input form-control{{ $errors->has('signature') ? ' is-invalid' : '' }}" type="text" id="placeholderFileFourName" placeholder="{{isset($id_card)? ($id_card->signature != ""? getFilePath3($id_card->signature):trans('admin.signature').' *'):trans('admin.signature'). ' *'}}"
                            readonly>
                        <span class="focus-border"></span>
                        @if ($errors->has('signature'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('signature') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-auto">
                    <button class="primary-btn-small-input cust-margin" type="button">
                        <label class="primary-btn small fix-gr-bg" for="document_file_4">@lang('common.browse')</label>
                        <input type="file" class="d-none" name="signature" id="document_file_4" onchange="signatureImageChangeWithFile(this,'.signPhoto')" value="{{isset($id_card)? ($id_card->signature != ""? getFilePath3($id_card->signature):''): ''}}">
                    </button>
                </div>
            </div>
            <button class="primary-btn icon-only fix-gr-bg" type="button" id="deleteSignImg">
                <span class="ti-trash"></span>
            </button>
        </div>

        <div class="row mt-25 admissionNo">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10 text">@lang('student.admission_no')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="admission_no" id="id_roll_yes" value="1" class="common-radio relationButton" onclick="idRoll('1')" {{isset($id_card)? ($id_card->admission_no == 1? 'checked': ''):'checked'}}>
                        <label for="id_roll_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="admission_no" id="id_roll_no" value="0" class="common-radio relationButton" onclick="idRoll('0')" {{isset($id_card)? ($id_card->admission_no == 0? 'checked': ''):''}}>
                        <label for="id_roll_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('common.name') </p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="student_name" id="student_name_yes" value="1" class="common-radio relationButton" onclick="studentName('1')" {{isset($id_card)? ($id_card->student_name == 1? 'checked': ''):'checked'}}>
                        <label for="student_name_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="student_name" id="student_name_no" value="0" class="common-radio relationButton" onclick="studentName('0')" {{isset($id_card)? ($id_card->student_name == 0? 'checked': ''):''}}>
                        <label for="student_name_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25 classHide">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('common.class') </p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="class" id="class_yes" value="1" class="common-radio relationButton" onclick="IDclass('1')" {{isset($id_card)? ($id_card->class == 1? 'checked': ''):'checked'}}>
                        <label for="class_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="class" id="class_no" value="0" class="common-radio relationButton" onclick="IDclass('0')" {{isset($id_card)? ($id_card->class == 0? 'checked': ''):''}}>
                        <label for="class_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25 fatherName">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('student.father_name')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="father_name" id="father_name_yes" value="1" class="common-radio relationButton" onclick="fatherName('1')" {{isset($id_card)? ($id_card->father_name == 1? 'checked': ''):'checked'}}>
                        <label for="father_name_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="father_name" id="father_name_no" value="0" class="common-radio relationButton" onclick="fatherName('0')" {{isset($id_card)? ($id_card->father_name == 0? 'checked': ''):''}}>
                        <label for="father_name_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25 motherName">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('student.mother_name')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="mother_name" id="mother_name_yes" value="1" class="common-radio relationButton" onclick="motherName('1')" {{isset($id_card)? ($id_card->mother_name == 1? 'checked': ''):'checked'}}>
                        <label for="mother_name_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="mother_name" id="mother_name_no" value="0" class="common-radio relationButton" onclick="motherName('0')" {{isset($id_card)? ($id_card->mother_name == 0? 'checked': ''):''}}>
                        <label for="mother_name_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('common.address')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="student_address" id="address_yes" value="1" class="common-radio relationButton" onclick="addRess('1')" {{isset($id_card)? ($id_card->student_address == 1? 'checked': ''):'checked'}}>
                        <label for="address_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="student_address" id="address_no" value="0" class="common-radio relationButton" onclick="addRess('0')" {{isset($id_card)? ($id_card->student_address == 0? 'checked': ''):''}}>
                        <label for="address_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25 mobile {{(@$id_card->phone_number ==0)? 'd-none':''}}">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('common.phone')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="phone_number" id="phone_yes" value="1" class="common-radio relationButton" onclick="phoneNumber('1')" {{isset($id_card)? ($id_card->phone_number == 1? 'checked': ''):'checked'}}>
                        <label for="phone_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="phone_number" id="phone_no" value="0" class="common-radio relationButton" onclick="phoneNumber('0')" {{isset($id_card)? ($id_card->phone_number == 0? 'checked': ''):''}}>
                        <label for="phone_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25 dateOfBirth">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('common.date_of_birth')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="dob" id="dob_yes" value="1" class="common-radio relationButton" onclick="dOB('1')"  {{isset($id_card)? ($id_card->dob == 1? 'checked': ''):'checked'}}>
                        <label for="dob_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="dob" id="dob_no" value="0" class="common-radio relationButton" onclick="dOB('0')" {{isset($id_card)? ($id_card->dob == 0? 'checked': ''):''}}>
                        <label for="dob_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-25 bloodGroup">
            <div class="col-lg-4 d-flex">
                <p class="text-uppercase fw-500 mb-10">@lang('student.blood_group')</p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex radio-btn-flex ml-40">
                    <div class="mr-30">
                        <input type="radio" name="blood" id="blood_yes" value="1" class="common-radio relationButton" onclick="bloodGroup('1')" {{isset($id_card)? ($id_card->blood == 1? 'checked': ''):'checked'}}>
                        <label for="blood_yes">@lang('admin.yes')</label>
                    </div>
                    <div class="mr-30">
                        <input type="radio" name="blood" id="blood_no" value="0" class="common-radio relationButton" onclick="bloodGroup('0')" {{isset($id_card)? ($id_card->blood == 0? 'checked': ''):''}}>
                        <label for="blood_no">@lang('admin.none')</label>
                    </div>
                </div>
            </div>
        </div>

        @php 
          $tooltip = "";
          if(userPermission(46)){
                $tooltip = "";
            }else{
                $tooltip = "You have no permission to add";
            }
        @endphp
        <div class="row mt-40">
            <div class="col-lg-12 text-center">
                <button class="primary-btn fix-gr-bg submit savaIdCard" type="submit" data-toggle="tooltip" title="{{$tooltip}}">
                    <span class="ti-check"></span>
                    @if(isset($id_card))
                        @lang('common.update')
                    @else
                        @lang('common.save')
                    @endif
                @lang('admin.id_card')
                </button>
            </div>
        </div>
    </div>
</div>