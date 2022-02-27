@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 up_breadcrumb white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.student_promote')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.student_information')</a>
                <a href="#">@lang('lang.student_promote')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.select_criteria') </h3>
                    </div>
                </div>
            </div>
            <div class="row">
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
                    <div class="white-box">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'student-current-search-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_promoteA']) }}
                            <div class="row">
                                <div class="col-lg-4">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('current_session') ? ' is-invalid' : '' }}" name="current_session" id="current_session">
                                        <option data-display="@lang('lang.select_academic_year') *" value="">@lang('lang.select_academic_year') *</option>
                                        @foreach($sessions as $session)
                                        <option value="{{$session->id}}" {{isset($current_session)? ($current_session == $session->id? 'selected':''):''}}>{{$session->year}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('current_session'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('current_session') }}</strong>
                                    </span>
                                    @endif                                  
                                </div>
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-4 mt-30-md">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('current_class') ? ' is-invalid' : '' }}" id="c_select_class" name="current_class">
                                        <option data-display="@lang('lang.select_current_class') *" value="">@lang('lang.select_current_class') *</option>
                                        @foreach($classes as $class)
                                        <option value="{{$class->id}}" {{isset($current_class)? ($current_class == $class->id? 'selected':''):''}}>{{$class->class_name}}</option>
                                        @endforeach
                                    </select>
                                     @if ($errors->has('current_class'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('current_class') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                                <div class="col-lg-4 mt-30-md" id="c_select_section_div">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" id="c_select_section" name="section">
                                        <option data-display="@lang('lang.select_section') *" value="">@lang('lang.select_section')</option>
                                    </select>
                                    @if ($errors->has('section'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('section') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                {{-- <div class="col-lg-2 mt-30-md">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('result') ? ' is-invalid' : '' }}" name="result">
                                        <option data-display="@lang('lang.result')" value="">@lang('lang.result')</option>
                                        <option value="P">Pass</option>
                                        <option value="F">Fail</option>
                                    </select>
                                    @if ($errors->has('result'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('result') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-2 mt-30-md">
                                    <select class="w-100 bb niceSelect form-control{{ $errors->has('exam') ? ' is-invalid' : '' }}" name="exam">
                                        <option data-display="@lang('lang.select_exam')" value="">@lang('lang.select_exam')</option>
                                        @foreach($exams as $exam)
                                            <option value="{{$exam->id}}" {{isset($exam_id)? ($exam_id == $exam->id? 'selected':''):''}}>{{$exam->title}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('exam'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('exam') }}</strong>
                                    </span>
                                    @endif
                                </div> --}}
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg" id="search_promote">
                                        <span class="ti-search pr-2"></span>
                                        @lang('lang.search')
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </section>


    @if(isset($students))
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-30">@lang('lang.promote_student_in_next_session')</h3>
                            </div>
                        </div>
                    </div>

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'student-promote-store-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'student_promote_submit']) }}
                    <input type="hidden" name="current_session" value="{{$current_session}}">
                    <input type="hidden" name="current_class" value="{{$current_class}}">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    @if(session()->has('message-danger-table') != "" || session()->has('message-success') != "")
                                    <tr>
                                        <td colspan="5">
                                            @if(session()->has('message-danger-table'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger-table') }}
                                            </div>
                                            @else
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>@lang('lang.admission') @lang('lang.no')</th>
                                        <th>@lang('lang.class')/@lang('lang.section')</th>
                                        <th>@lang('lang.name')</th>
                                        {{-- <th>@lang('lang.information')</th> --}}
                                        {{-- <th>@lang('lang.current') @lang('lang.result')</th> --}}
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach( @$students['students'] ? @$students['students']: $students  as $student)
                                    <tr>
                                        <td>{{$student->admission_no}}</td>
                                        <input type="hidden" name="id[]" value="{{$student->id}}">
                                        <td>{{$student->className !=""?$student->className->class_name:""}}</td>
                                        <td>{{@$student->studentinfo ? $student->studentinfo->first_name .' '.$student->studentinfo->last_name : $student->first_name .' '.$student->last_name}}</td>
                                     
                                        <td style="display:none">
                                            @if (@$students['students'])
                                                <div class="mr-30">
                                                    <input type="radio" name="result[{{$student->id}}]" id="radioP{{$student->id}}" class="common-radio" value="P" checked />
                                                    <label for="radioP{{$student->id}}">@lang('lang.pass') &nbsp;</label>
                                                </div>
                                            @else                                           
                                                <div>                
                                                    <input type="radio" name="result[{{$student->id}}]" id="radioP{{$student->id}}" class="common-radio" value="P" checked />
                                                    <label for="radioP{{$student->id}}">@lang('lang.pass') &nbsp;</label>
                                                </div>    
                                            
                                            @endif         
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5">
                                            <div class="row mt-30">
                                                <div class="col-lg-3">
                                                    <select class="niceSelect w-100 bb promote_session form-control{{ $errors->has('promote_session') ? ' is-invalid' : '' }}" name="promote_session" id="promote_session">
                                                        <option data-display="@lang('lang.select') @lang('lang.academic_year') *" value="">@lang('lang.select') @lang('lang.academic_year') *</option>
                                                        @foreach($Upsessions as $session)
                                                        @if (@$current_session != $session->id)
                                                          <option value="{{$session->id}}" {{( old("promote_session") == $session->id ? "selected":"")}}>{{$session->year}}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                    
                                                    <span class="text-danger d-none" role="alert" id="promote_session_error">
                                                        <strong>@lang('lang.the_session_is_required')</strong>
                                                    </span>
                                                </div>

                                              
                                                 <div class="col-lg-3 " id="select_class_div">
                                                    <select class="niceSelect w-100 bb"  name="promote_class" id="select_class">
                                                        <option data-display="@lang('lang.select_class')" value="">@lang('lang.select_class')</option>
                                                    </select>
                                                </div>

                                                 <div class="col-lg-3 " id="select_section_div">
                                                    <select class="niceSelect w-100 bb" id="select_section" name="promote_section">
                                                        <option data-display="@lang('lang.select_section')" value="">@lang('lang.select_section')</option>
                                                    </select>
                                                </div>
                                               
                                                @if(in_array(82, App\GlobalVariable::GlobarModuleLinks()) || Auth::user()->role_id == 1 )
                                                <div class="col-lg-3 text-center">
                                                    <button type="submit" class="primary-btn fix-gr-bg" id="student_promote_submit">
                                                        <span class="ti-check"></span>
                                                        @lang('lang.promote')
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    

                    {{ Form::close() }}
                </div>
            </div>
    </div>
</section>
@endif
<script>



</script>

@endsection
