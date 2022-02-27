@extends('backEnd.master')
@section('title') 
@lang('bulkprint::bulk.fees_invoice_bulk_print')
@endsection
@section('mainContent')
@php  $setting = App\SmGeneralSettings::where('school_id', Auth::user()->school_id)->first();  if(!empty($setting->currency_symbol)){ $currency = $setting->currency_symbol; }else{ $currency = '$'; }   @endphp 

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('bulkprint::bulk.fees_invoice_bulk_print')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('bulkprint::bulk.fees_invoice_bulk_print')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('common.select_criteria') </h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
               
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'fees-bulk-print-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                    <div class="row">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                        <div class="col-lg-6 mt-30-md">
                            <select class="w-100 niceSelect bb form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class">
                                <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *</option>
                                @foreach($classes as $class)
                                <option value="{{$class->id}}"  {{ isset($class_id)? ($class_id == $class->id? 'selected':''): (old("class") == $class->id ? "selected":"")}}>{{$class->class_name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('class'))
                            <span class="invalid-feedback invalid-select" role="alert">
                                <strong>{{ $errors->first('class') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="col-lg-6 mt-30-md" id="select_section_div">
                            <select class="w-100 niceSelect bb form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" id="select_section" name="section">
                                <option data-display="@lang('common.select_section') " value="">@lang('common.select_section') *</option>
                            </select>
                            <div class="pull-right loader loader_style" id="select_section_loader">
                                <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                            </div>
                            @if ($errors->has('section'))
                            <span class="invalid-feedback invalid-select" role="alert">
                                <strong>{{ $errors->first('section') }}</strong>
                            </span>
                            @endif
                        </div>
                       
                       
                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span>
                                @lang('common.search')
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        @if(isset($fees_payments))
        <div class="row mt-40">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('fees.fees_collection_details')</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table id="table_id_al" class="display school-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('fees.payment_id')</th>
                                    <th>@lang('common.date')</th>
                                    <th>@lang('common.name')</th>
                                    <th>@lang('common.class')</th>
                                    <th>@lang('fees.fees_type')</th>
                                    <th>@lang('fees.mode')</th>
                                    <th>@lang('fees.amount')</th>
                                    <th>@lang('fees.fine')</th>
                                    <th>@lang('exam.result')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grand_amount = 0;
                                    $grand_total = 0;
                                    $grand_discount = 0;
                                    $grand_fine = 0;
                                    $total = 0;
                                @endphp
                                @foreach($fees_payments as $students)
                                    @foreach($students as $fees_payment)
                                    @php $total = 0; @endphp
                                    <tr>
                                        <td>{{$fees_payment->fees_type_id.'/'.$fees_payment->id}}</td>
                                        <td  data-sort="{{strtotime($fees_payment->payment_date)}}" >
                                            {{$fees_payment->payment_date != ""? dateConvert($fees_payment->payment_date):''}}

                                        </td>
                                        <td>{{$fees_payment->studentInfo !=""?$fees_payment->studentInfo->full_name:""}}</td>
                                        <td>
                                            @if($fees_payment->studentInfo!="" && $fees_payment->studentInfo->class!="")
                                            {{$fees_payment->studentInfo->class->class_name}}
                                            @endif
                                        </td>
                                        <td>{{$fees_payment->feesType!=""?$fees_payment->feesType->name:""}}</td>
                                        <td>
                                            {{@$fees_payment->payment_mode}}
                                        </td>
                                        <td>
                                            @php
                                                $total =  $total + $fees_payment->amount;
                                                $grand_amount =  $grand_amount + $fees_payment->amount;
                                                echo generalSetting()->currency_symbol.$fees_payment->amount;
                                            @endphp
                                        </td>
                                        
                                        <td>
                                            @php
                                                $total =  $total + $fees_payment->fine;
                                                $grand_fine =  $grand_fine + $fees_payment->fine;
                                                echo generalSetting()->currency_symbol.$fees_payment->fine;
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                $grand_total =  $grand_total + $total;
                                                echo generalSetting()->currency_symbol.$total;
                                            @endphp
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>@lang('accounts.grand_total') </th>
                                <th>{{generalSetting()->currency_symbol}}{{$grand_amount}}</th>
                                <th>{{generalSetting()->currency_symbol}}{{$grand_fine}}</th>
                                <th>{{generalSetting()->currency_symbol}}{{$grand_total}}</th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
@push('script')
<script>
        $('input[name="date_range"]').daterangepicker({
            ranges: {
                {!! json_encode(__('calender.Today')) !!}: [moment(), moment()],
                {!! json_encode(__('calender.Yesterday')) !!}: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                {!! json_encode(__('calender.Last 7 Days')) !!}: [moment().subtract(6, 'days'), moment()],
                {!! json_encode(__('calender.Last 30 Days')) !!}: [moment().subtract(29, 'days'), moment()],
                {!! json_encode(__('calender.This Month')) !!}: [moment().startOf('month'), moment().endOf('month')],
                {!! json_encode(__('calender.Last Month')) !!}: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "locale": {
                "separator": {!! json_encode(__('calender.separator')) !!},
                "applyLabel": {!! json_encode(__('calender.applyLabel')) !!},
                "cancelLabel": {!! json_encode(__('calender.cancelLabel')) !!},
                "fromLabel": {!! json_encode(__('calender.fromLabel')) !!},
                "toLabel": {!! json_encode(__('calender.toLabel')) !!},
                "customRangeLabel": {!! json_encode(__('calender.customRangeLabel')) !!},
                "weekLabel": {!! json_encode(__('calender.weekLabel')) !!},
                "daysOfWeek": {!! json_encode(__('calender.daysMin')) !!},
                "monthNames": {!! json_encode(__('calender.months')) !!}
            },
            "startDate": moment().subtract(7, 'days'),
            "endDate": moment()
            }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@endpush
