
@extends('backEnd.master')
@section('title')
    @lang('fees.bank_payment')
@endsection
@section('mainContent')
    @push('css')
        <style>
            table.dataTable.row-border tbody th, table.dataTable.row-border tbody td, table.dataTable.display tbody th, table.dataTable.display tbody td {
                border-bottom: 1px solid rgba(130, 139, 178, 0.15) !important;
            }
        </style>
    @endpush
    <section class="sms-breadcrumb mb-40 white-box up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.bank_payment')</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees_collection')</a>
                    <a href="#">@lang('fees.bank_payment')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="main-title mt_0_sm mt_0_md">
                        <h3 class="mb-30">@lang('common.select_criteria') </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'fees.search-bank-payment', 'method' => 'post']) }}
                        <div class="row">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="col-lg-3 col-md-3 sm_mb_20 sm2_mb_20">
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('class') ? ' is-invalid' : '' }}" id="select_class" name="class">
                                    <option data-display="@lang('common.select_class')" value="">@lang('common.select_class')</option>
                                    @foreach($classes as $class)
                                        <option value="{{$class->id}}" {{isset($class_id)? ($class_id == $class->id? 'selected': ''):'' }}>{{$class->class_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('class'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('class') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-3 col-md-3" id="select_section_div">
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" id="select_section" name="section">
                                    <option data-display="@lang('common.select_section')" value="">@lang('common.select_section')</option>
                                    @if (isset($section_id))
                                        @foreach($sections as $section)
                                            <option value="{{$section->id}}" {{isset($section_id)? ($section_id == $section->id? 'selected': ''):'' }}>{{$section->section_name}}</option>
                                        @endforeach
                                    @endif
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
                            <div class="col-lg-3 col-md-3 mt-30-md">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date form-control{{ $errors->has('payment_date') ? ' is-invalid' : '' }} {{isset($date)? 'read-only-input': ''}}" id="startDate" type="text"
                                                   name="payment_date" autocomplete="off" value="{{isset($date)? $date: ''}}">
                                            <label for="startDate">@lang('fees.payment_date')</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('payment_date'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('payment_date') }}</strong>
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
                            <div class="col-lg-3 col-md-3 sm_mb_20 sm2_mb_20">
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('approve_status') ? ' is-invalid' : '' }}" name="approve_status">
                                    <option data-display="@lang('common.status')" value="">@lang('common.status')</option>
                                    <option value="pending" {{isset($approve_status)? ($approve_status == 'pending'? 'selected': ''):'' }}>@lang('common.pending')</option>
                                    <option value="approve" {{isset($approve_status)? ($approve_status == 'approve'? 'selected': ''):'' }}>@lang('common.approved')</option>
                                    <option value="reject" {{isset($approve_status)? ($approve_status == 'reject'? 'selected': ''):'' }}>@lang('common.reject')</option>
                                </select>
                                @if ($errors->has('approve_status'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('approve_status') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-12 mt-20 text-right">
                                @if(userPermission(1149) )
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>
                                        @lang('common.search')
                                    </button>
                                @endif
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>


            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">  @lang('fees.bank_payment_list')</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table " cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>@lang('student.student_name')</th>
                                    <th>@lang('fees::feesModule.view_transcation')</th>
                                    <th>@lang('common.date')</th>
                                    <th>@lang('fees::feesModule.amount')</th>
                                    <th>@lang('common.note')</th>
                                    <th>@lang('common.file')</th>
                                    <th>@lang('common.status')</th>
                                    <th>@lang('common.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( isset($feesPayments))

                                    @foreach($feesPayments as $bank_payment)
                                        @php
                                            $paid_amount = $bank_payment->PaidAmount;
                                        @endphp
                                        <tr>
                                            <td>{{@$bank_payment->feeStudentInfo->full_name}}</td>

                                            <td>
                                                <a class="text-color" data-toggle="modal" data-target="#showTranscation{{$bank_payment->id}}"  href="#">@lang('common.details')</a>
                                                <div class="modal fade admin-query" id="showTranscation{{$bank_payment->id}}">
                                                    <div class="modal-dialog modal-dialog-centered large-modal">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('fees::feesModule.payment_method') : {{$bank_payment->payment_method}}</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body p-0 mt-30">
                                                                <div class="container student-certificate">
                                                                    <div class="row justify-content-center">
                                                                        <div class="col-lg-12 text-center">
                                                                            <table class="display school-table school-table-style shadow-done" cellspacing="0" width="100%">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>@lang('fees::feesModule.fees_type')</th>
                                                                                    <th>@lang('fees::feesModule.paid_amount')</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($bank_payment->transcationDetails as $details)
                                                                                            <tr>
                                                                                                <td>{{@$details->transcationFeesType->name}}</td>
                                                                                                <td>{{@$details->paid_amount}}</td>
                                                                                            </tr>
                                                                                    @endforeach
                                                                                    @if(@$bank_payment->add_wallet_money > 0)
                                                                                        <tr>
                                                                                            <td><strong>@lang('fees::feesModule.wallet_money')</strong></td>
                                                                                            <td><strong>{{$bank_payment->add_wallet_money}}</strong></td>
                                                                                        </tr>
                                                                                    @endif
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>{{dateConvert($bank_payment->created_at)}}</td>
                                            <td>{{$paid_amount + $bank_payment->add_wallet_money}}</td>
                                            <td>
                                                <a class="text-color" data-toggle="modal" data-target="#showNote{{$bank_payment->id}}"  href="#">@lang('common.note')</a>
                                            </td>
                                            <td>
                                                @if (!empty($bank_payment->file))
                                                    <a class="text-color" data-toggle="modal" data-target="#bankPaymentFile{{$bank_payment->id}}"  href="#">@lang('common.file')</a>
                                                @endif
                                            </td>
                                            <td>
                                                @if($bank_payment->paid_status=='pending')
                                                    <button class="primary-btn small bg-warning text-white border-0">@lang('common.pending')</button>
                                                @elseif($bank_payment->paid_status=='approve')
                                                    <button class="primary-btn small bg-success text-white border-0  tr-bg">@lang('common.approved')</button>
                                                @else
                                                    <button class="primary-btn small bg-danger text-white border-0">@lang('common.reject')</button>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                        @lang('common.select')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if($bank_payment->paid_status=='pending')
                                                            @if(userPermission(1150) )
                                                                <a onclick="enableId({{$bank_payment->id}});" class="dropdown-item" href="#" data-toggle="modal" data-target="#approvePayment" data-id="{{$bank_payment->id}}">
                                                                    @lang('common.approve')
                                                                </a>
                                                            @endif
                                                            @if(userPermission(1151) )
                                                                <a onclick="rejectPayment({{$bank_payment->id}});" class="dropdown-item" href="#" data-toggle="modal" data-id="{{$bank_payment->id}}">
                                                                    @lang('accounts.reject')
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>


                                        <div class="modal fade admin-query" id="showNote{{$bank_payment->id}}">
                                            <div class="modal-dialog modal-dialog-centered large-modal">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('fees.note')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body p-0 mt-30">
                                                        <div class="container student-certificate">
                                                            <div class="row justify-content-center">
                                                                <div class="col-lg-12 text-center">
                                                                    <p>{{$bank_payment->payment_note}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade admin-query" id="bankPaymentFile{{$bank_payment->id}}">
                                            <div class="modal-dialog modal-dialog-centered large-modal">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('fees.file')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body p-0 mt-30">
                                                        <div class="container student-certificate">
                                                            <div class="row justify-content-center">
                                                                <div class="col-lg-12 text-center">

                                                                    @php
                                                                        $pdf = $bank_payment->file ? explode('.', $bank_payment->file) : [];
                                                                        $for_pdf =  $pdf[1]?? null;
                                                                    @endphp
                                                                    @if (@$for_pdf=="pdf")
                                                                        <div class="mb-5">
                                                                            <a href="{{asset($bank_payment->file)}}" download>@lang('common.download') <span class="pl ti-download"></span></a>
                                                                        </div>
                                                                    @else
                                                                        <div class="mb-5">
                                                                            <img class="img-fluid" src="{{asset($bank_payment->file)}}">
                                                                            </br>
                                                                            <a href="{{asset($bank_payment->file)}}" download>@lang('common.download') <span class="pl ti-download"></span></a>
                                                                        </div>
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach

                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade admin-query" id="approvePayment" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('fees::feesModule.approve_payment')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang('fees.are_you_sure_to_approve')</h4>
                    </div>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        {{ Form::open(['route' => 'fees.approve-bank-payment', 'method' => 'POST']) }}
                        <input type="hidden" name="transcation_id" value="{{@$bank_payment->id}}">
                        <button class="primary-btn fix-gr-bg" type="submit">@lang('common.approve')</button>
                        {{ Form::close() }}
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- modal start here  -->

    <div class="modal fade admin-query" id="rejectPaymentModal" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('fees::feesModule.bank_payment_reject') </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang('fees::feesModule.are_you_sure_to_reject')</h4>
                    </div>
                    {{ Form::open(['route' => 'fees.reject-bank-payment', 'method' => 'POST']) }}
                    <div class="form-group">
                        <input type="hidden" name="transcation_id" value="{{@$bank_payment->id}}">
                        <label><strong>@lang('fees::feesModule.reject_note')</strong></label>
                        <textarea name="payment_reject_reason" class="form-control" rows="6"></textarea>
                    </div>

                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.close')</button>
                        <button class="primary-btn fix-gr-bg" type="submit">@lang('common.submit')</button>
                    </div>
                    {{ Form::close() }}

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="showReasonModal" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('lang.reject_note')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>@lang('lang.reject_note')</strong></label>
                        <textarea readonly class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn fix-gr-bg" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@if(! isset($feesPayments))
@section('script')
    @include('backEnd.partials.server_side_datatable')
    <script>
        //
        // DataTables initialisation
        //
        $(document).ready(function() {
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": $.fn.dataTable.pipeline( {
                    url: "{{url('bank-payment-slip-ajax')}}",
                    data: {
                        academic_year: $('#academic_id').val(),
                        class: $('#class').val(),
                        section: $('#section').val(),
                        roll_no: $('#roll').val(),
                        name: $('#name').val()
                    },
                    pages: "{{generalSetting()->ss_page_load}}" // number of pages to cache

                } ),
                columns: [
                    {data: 'student_info.full_name', name: 'student_name'},
                    {data: 'fees_type.name', name: 'fees_type'},
                    {data: 'date', name: 'date'},
                    {data: 'amount', name: 'amount'},
                    {data: 'note', name: 'note'},
                    {data: 'slip', name: 'slip'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action',orderable: false, searchable: true},

                ],
                bLengthChange: false,
                bDestroy: true,
                language: {
                    search: "<i class='ti-search'></i>",
                    searchPlaceholder: window.jsLang('quick_search'),
                    paginate: {
                        next: "<i class='ti-arrow-right'></i>",
                        previous: "<i class='ti-arrow-left'></i>",
                    },
                },
                dom: "Bfrtip",
                buttons: [{
                    extend: "copyHtml5",
                    text: '<i class="fa fa-files-o"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: window.jsLang('copy_table'),
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    },
                },
                    {
                        extend: "excelHtml5",
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: window.jsLang('export_to_excel'),
                        title: $("#logo_title").val(),
                        margin: [10, 10, 10, 0],
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "csvHtml5",
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: window.jsLang('export_to_csv'),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        title: $("#logo_title").val(),
                        titleAttr: window.jsLang('export_to_pdf'),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                        orientation: "landscape",
                        pageSize: "A4",
                        margin: [0, 0, 0, 12],
                        alignment: "center",
                        header: true,
                        customize: function(doc) {
                            doc.content[1].margin = [100, 0, 100, 0]; //left, top, right, bottom
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: "center",
                                image: "data:image/png;base64," + $("#logo_img").val(),
                            });
                        },
                    },
                    {
                        extend: "print",
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: window.jsLang('print'),
                        title: $("#logo_title").val(),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "colvis",
                        text: '<i class="fa fa-columns"></i>',
                        postfixButtons: ["colvisRestore"],
                    },
                ],
                columnDefs: [{
                    visible: false,
                }, ],
                responsive: true,
            });
        } );
    </script>
@endsection
@endif
@push('script')
    <script>
        function rejectPayment(id){
            var modal = $('#rejectPaymentModal');
            modal.find('#showId').val(id)
            modal.modal('show');

        }
        function viewReason(id){
            var reason = $('.reason'+ id).data('reason');
            var modal = $('#showReasonModal');
            modal.find('textarea').val(reason)
            modal.modal('show');
        }
    </script>
@endpush