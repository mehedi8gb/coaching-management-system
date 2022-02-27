@extends('backEnd.master')
@section('mainContent')
<section class="mb-40">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.select') @lang('lang.criteria') </h3>
                </div>
            </div>
            <div class="offset-lg-6 col-lg-2 text-right">
                <a href="{{route('user_create')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <form>
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="row no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date" id="startDate" type="text"
                                                    placeholder="Start Date">
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
                                <div class="col-lg-3">
                                    <div class="row no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date" id="endDate" type="text" placeholder="End Date">
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
                                <div class="col-lg-3">
                                    <select class="niceSelect w-100 bb">
                                        <option data-display="Source">@lang('lang.source')</option>
                                        <option value="1">@lang('lang.front') @lang('lang.office')</option>
                                        <option value="2">@lang('lang.advertisement')</option>
                                        <option value="4">@lang('lang.online') @lang('lang.front') @lang('lang.site')</option>
                                        <option value="5">@lang('lang.google') @lang('lang.ads')</option>
                                        <option value="6">@lang('lang.admission') @lang('lang.campaign')</option>
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <select class="niceSelect w-100 bb">
                                        <option data-display="Status">@lang('lang.status')</option>
                                        <option value="1">@lang('lang.all')</option>
                                        <option value="2">@lang('lang.active')</option>
                                        <option value="3">@lang('lang.passive')</option>
                                        <option value="4">@lang('lang.dead')</option>
                                        <option value="5">@lang('lang.won')</option>
                                        <option value="6">@lang('lang.lost')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="adminssion-query">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-0">@lang('lang.user') @lang('lang.details')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value=""> @lang('lang.name')
                                    </label>
                                </div>
                            </th>
                            <th>@lang('lang.phone')</th>
                            <th>@lang('lang.source')</th>
                            <th>@lang('lang.query_date')</th>
                            <th>@lang('lang.last_follow_up_date')</th>
                            <th>@lang('lang.next_follow_up_date')</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="">
                                        @lang('lang.salmon') @lang('lang.shashimi')
                                    </label>
                                </div>
                            </td>
                            <td>+44633331234</td>
                            <td>Front Offic</td>
                            <td>31st Oct, 2018</td>
                            <td>23rd Oct, 2018</td>
                            <td>31st Oct, 2018</td>
                            <td>Active</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                        @lang('lang.edit')
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <button class="dropdown-item" type="button" data-toggle="modal" data-target="#editStudent">@lang('lang.edit')</button>
                                        <button class="dropdown-item" type="button">@lang('lang.delete')</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection