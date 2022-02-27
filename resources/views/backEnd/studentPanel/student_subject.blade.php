@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.subject')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{route('student_subject')}}">@lang('lang.subject')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
       
        <div class="row">

            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('lang.subject_list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th>@lang('lang.dashboard')@lang('lang.subject')</th>
                                    <th>@lang('lang.dashboard')@lang('lang.teacher')</th>
                                    <th>@lang('lang.dashboard')@lang('lang.subject_type')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($assignSubjects as $assignSubject)
                                <tr>
                                    <td>{{@$assignSubject->subject!=""?@$assignSubject->subject->subject_name:""}}</td>
                                    <td>{{@$assignSubject->teacher!=""?@$assignSubject->teacher->full_name:""}}</td>
                                    <td>
                                        @if(!empty(@$assignSubject->subject))
                                        {{@$assignSubject->subject->subject_type == "T"? 'Theory': 'Practical'}}
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
    </div>
</section>
@endsection
