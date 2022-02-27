@extends('backEnd.master')
    @section('title') 
        @if (isset($invoiceInfo))
            @lang('common.edit')
        @else
            @lang('common.add')
        @endif 
            @lang('fees.fees_invoice')
    @endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{url('Modules\Fees\Resources\assets\css\feesStyle.css')}}"/>
    @endpush
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@if (isset($invoiceInfo))
                    @lang('common.edit')
                @else
                    @lang('common.add')
                @endif 
                    @lang('fees.fees_invoice')</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="{{route('fees.fees-invoice-list')}}">@lang('fees.fees_invoice')</a>
                    <a href="#">
                        @if (isset($invoiceInfo))
                            @lang('common.edit')
                        @else
                            @lang('common.add')
                        @endif
                            @lang('fees.fees_invoice')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($invoiceInfo))
                {{ Form::open(['class' => 'form-horizontal', 'method' => 'POST', 'route' => 'fees.fees-invoice-update']) }}
                <input type="hidden" name="id" class="editValue" value="{{$invoiceInfo->id}}">
            @else
                {{ Form::open(['class' => 'form-horizontal', 'method' => 'POST', 'route' => 'fees.fees-invoice-store']) }}
            @endif
                <div class="row">
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h3 class="mb-30">
                                        @if (isset($invoiceInfo))
                                            @lang('common.edit')
                                        @else
                                            @lang('common.add')
                                        @endif
                                            @lang('fees.fees_invoice')
                                    </h3>
                                </div>
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="white-box">
                                    <div class="add-visitor">                              
                                        <div class="row">
                                            <div class="col-lg-12 d-flex">
                                                <div>@lang('fees.invoice')- &nbsp;</div>
                                                <div class="d-flex" id="showValue"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 mt-25">
                                                <select class="niceSelect w-100 bb form-control{{ $errors->has('class') ? ' is-invalid' : '' }}" name="class" id="selectClass">
                                                    <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *</option>
                                                        @foreach($classes as $class)
                                                            <option value="{{$class->id}}" {{isset($invoiceInfo)? ($invoiceInfo->class_id == $class->id?'selected':''):''}} >{{$class->class_name}}</option>
                                                        @endforeach
                                                </select>
                                                @if($errors->has('class'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ $errors->first('class') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-25">
                                            <div class="col-lg-12" id="selectStudentDiv">
                                                <select class="w-100 bb niceSelect form-control{{ $errors->has('student') ? ' is-invalid' : '' }}" id="selectStudent" name="student">
                                                <option data-display="@lang('common.select_student') *" value="">@lang('common.select_student')*</option>
                                                @if (isset($invoiceInfo))
                                                    @foreach ($students as $student)
                                                        <option value="{{$student->id}}" {{($student->id == $invoiceInfo->student_id)? 'selected':''}}>{{$student->full_name}} (@lang('student.roll')-{{$student->roll_no}})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="pull-right loader" id="selectStudentLoader" style="margin-top: -30px;padding-right: 21px;">
                                                <img src="{{asset('Modules/Fees/Resources/assets/img/pre-loader.gif')}}" alt="" style="width: 28px;height:28px;">
                                            </div>
                                            @if ($errors->has('student'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('student') }}</strong>
                                                </span>
                                            @endif
                                            </div>
                                        </div>

                                        <div class="row no-gutters input-right-icon mt-30">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <input class="primary-input date form-control{{ $errors->has('create_date') ? ' is-invalid' : '' }}" id="startDate" type="text" name="create_date" value="{{isset($invoiceInfo)? date('m/d/Y', strtotime($invoiceInfo->create_date)) : date('m/d/Y')}}">
                                                        <label>@lang('fees.create_date') <span>*</span></label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('create_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('create_date') }}</strong>
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

                                        <div class="row no-gutters input-right-icon mt-30">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <input class="primary-input date form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}" id="startDate" type="text" name="due_date" value="{{isset($invoiceInfo)? date('m/d/Y', strtotime($invoiceInfo->due_date)) : date('m/d/Y')}}">
                                                        <label>@lang('fees.due_date') <span>*</span></label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('due_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('due_date') }}</strong>
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

                                        <div class="row mt-25">
                                            <div class="col-lg-12">
                                                <select class="niceSelect w-100 bb form-control{{ $errors->has('payment_status') ? ' is-invalid' : '' }}" name="payment_status" id="paymentStatus">
                                                    <option data-display="@lang('fees.payment_status') *" value="">@lang('fees.payment_status') *</option>
                                                    <option value="not" {{isset($invoiceInfo)? ($invoiceInfo->payment_status == "not"?'selected':''):(old('payment_status') == 'not' ? 'selected' : '')}}>@lang('fees.not_paid')</option>
                                                    <option value="partial" {{isset($invoiceInfo)? ($invoiceInfo->payment_status == "partial"?'selected':''):(old('payment_status') == 'partial' ? 'selected' : '')}}>@lang('fees.partial_paid')</option>
                                                    <option value="full" {{isset($invoiceInfo)? ($invoiceInfo->payment_status == "full"?'selected':''):(old('payment_status') == 'full' ? 'selected' : '')}}>@lang('fees.full_paid')</option>
                                                </select>
                                                @if($errors->has('payment_status'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ $errors->first('payment_status') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-25 d-none" id="paymentMethod">
                                            <div class="col-lg-12">
                                                <select class="niceSelect w-100 bb form-control{{ $errors->has('payment_method') ? ' is-invalid' : '' }}" name="payment_method" id="paymentMethodName">
                                                    <option data-display="@lang('fees.payment_method')*" value="">@lang('fees.payment_method')*</option>
                                                    @foreach ($paymentMethods as $paymentMethod)
                                                        <option value="{{$paymentMethod->method}}" {{isset($invoiceInfo)? ($invoiceInfo->payment_method == $paymentMethod->method?'selected':''):(old('payment_method') == $paymentMethod->method ? 'selected' : '')}}>{{$paymentMethod->method}}</option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('payment_method'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ $errors->first('payment_method') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-25 d-none" id="bankPayment">
                                            <div class="col-lg-12">
                                                <select class="niceSelect w-100 bb form-control{{ $errors->has('bank') ? ' is-invalid' : '' }}" name="bank">
                                                    <option data-display="@lang('fees.select_bank')*" value="">@lang('fees.select_bank')*</option>
                                                    @foreach ($bankAccounts as $bankAccount)
                                                        <option value="{{$bankAccount->id}}" {{isset($invoiceInfo)? ($invoiceInfo->bank_id == $bankAccount->id?'selected':''):(old('bank') == $bankAccount->id ? 'selected' : '')}}>{{$bankAccount->bank_name}} ({{$bankAccount->account_number}})</option>
                                                    @endforeach
                                                </select>
                                                @if($errors->has('bank'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ $errors->first('bank') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @php 
                                        $tooltip = "";
                                        if(userPermission(119)){
                                                $tooltip = "";
                                            }else{
                                                $tooltip = "You have no permission to add";
                                            }
                                        @endphp

                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg submit fmInvoice" data-tooltip="tooltip" title="{{$tooltip}}">
                                                    <span class="ti-check"></span>
                                                    @if (isset($invoiceInfo))
                                                        @lang('common.update')
                                                    @else
                                                        @lang('common.save')
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-0">@lang('fees.fees_type_list')</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-30">
                            <div class="col-lg-12">
                                <div class="white-box pb-0 fees_invoice_type_div">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}" id="selectFeesType" name="fees_type">
                                                <option data-display="@lang('fees.fees_type') *" value="">@lang('fees.fees_type') *</option>
                                                <option value="" disabled>@lang('fees.fees_group')</option>
                                                @foreach ($feesGroups as $feesGroup)
                                                    <option value="grp{{$feesGroup->id}}">{{$feesGroup->name}}</option>
                                                @endforeach
                                                <option value="" disabled>@lang('fees.fees_type')</option>
                                                @foreach ($feesTypes as $feesType)
                                                    <option value="typ{{$feesType->id}}">{{$feesType->name}}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('fees_type'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('fees_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-20">
                                        <div class="col-lg-12 justify-content-end d-flex">
                                            <div class="text-right">
                                                <input type="checkbox" id="cloneAmount" class="common-radio permission-checkAll">
                                                <label for="cloneAmount">@lang('fees::feesModule.clone_amount')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" class="weaverType" value="amount">
                                <div class="big-table">
                                    <table class="display school-table school-table-style fees_invoice_type_table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>@lang('fees.fees_type')</th>
                                                <th>@lang('accounts.amount')</th>
                                                <th>@lang('exam.waiver')</th>
                                                <th>@lang('fees.sub_total')</th>
                                                <th>@lang('fees.paid_amount')</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody class="allFeesTypes">
                                            @if (isset($invoiceInfo))
                                                @foreach ($invoiceDetails as $key=>$invoiceDetail)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{$invoiceDetail->feesType->name}}</td>
                                                        <input type="hidden" name="feesType[]" value="{{$invoiceDetail->fees_type}}">
                                                        <td>
                                                            <div class="input-effect">
                                                                <input class="primary-input form-control amount{{ $errors->has('amount') ? ' is-invalid' : '' }}" type="text" name="amount[]" autocomplete="off" value="{{isset($invoiceDetail)? $invoiceDetail->amount: old('amount')}}">
                                                                <span class="focus-border"></span>
                                                                @if ($errors->has('amount'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('amount') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-effect">
                                                                <input class="primary-input form-control weaver{{ $errors->has('weaver') ? ' is-invalid' : '' }}" type="text" name="weaver[]" autocomplete="off" value="{{isset($invoiceDetail)? $invoiceDetail->weaver: old('weaver')}}">
                                                                <span class="focus-border"></span>
                                                                @if ($errors->has('weaver'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('weaver') }}</strong>
                                                                </span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="subTotal">{{isset($invoiceDetail)? $invoiceDetail->sub_total: ""}}</td>
                                                        <input type="hidden" name="sub_total[]" class="inputSubTotal" value="{{isset($invoiceDetail)? $invoiceDetail->sub_total: ""}}">
                                                        <td>
                                                            <input class="primary-input form-control paidAmount{{ $errors->has('paid_amount') ? ' is-invalid' : '' }}" type="text" name="paid_amount[]" autocomplete="off" disabled value="{{isset($invoiceDetail)? $invoiceDetail->paid_amount: old('paid_amount')}}">
                                                        </td>
                                                        <td>
                                                            <button class="primary-btn icon-only fix-gr-bg" data-toggle="modal" data-tooltip="tooltip" data-target="#addNotesModal{{$invoiceDetail->fees_type}}" type="button"
                                                                title="@lang('common.edit_note')">
                                                                <span class="ti-pencil-alt"></span>
                                                            </button>
                                                            <button class="primary-btn icon-only fix-gr-bg" type="button" data-tooltip="tooltip" title="@lang('common.delete')" id="deleteField">
                                                                <span class="ti-trash"></span>
                                                            </button>
                                                            {{-- Notes Modal Start --}}
                                                            <div class="modal fade admin-query" id="addNotesModal{{$invoiceDetail->fees_type}}">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">@lang('common.edit_note')</h4>
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <div class="input-effect">
                                                                                <input class="primary-input form-control has-content" type="text" name="note[]" autocomplete="off" value="{{isset($invoiceDetail)? $invoiceDetail->note: ""}}">
                                                                                <label>@lang('common.note')</label>
                                                                                <span class="focus-border"></span>
                                                                            </div>
                                                                            </br>
                                                                            <div class="mt-40 d-flex justify-content-between">
                                                                                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                                                                                <button type="button" class="primary-btn fix-gr-bg" data-dismiss="modal">@lang('common.update')</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- Notes Modal End --}}
                                                            <input type="hidden" class="fees" value="grp{{$invoiceDetail->feesType->fees_group_id}}">
                                                            <input type="hidden" class="fees" value="typ{{$invoiceDetail->fees_type}}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>@lang('exam.result')</td>
                                                <td></td>
                                                <td class="showTotalAmount">0.00</td>
                                                <td class="showTotalWeaver">0.00</td>
                                                <td class="showSubTotalDiscount">0.00</td>
                                                <td class="showPaidAmount">0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </section>
@endsection
@push('script')
    <script type="text/javascript" src="{{url('Modules\Fees\Resources\assets\js\app.js')}}"></script>
    <script>
        selectPosition({!! feesInvoiceSettings()->invoice_positions !!});
    </script>
@endpush
