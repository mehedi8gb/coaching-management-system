<!DOCTYPE html>
<html>
<head>
    <title>@lang('lang.fees_group') @lang('lang.details')</title>
    <style>
       
        .school-table-style {
            padding: 10px 0px!important;
        }
        .school-table-style tr th {
            font-size: 7px!important;
            text-align: left!important;
        }
        .school-table-style tr td {
            font-size: 8px!important;
            text-align: left!important;
            padding: 10px 0px!important;
        }
        .logo-image {
            width: 10%;
        }
    </style>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/bootstrap.css" />
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/style.css" />
</head>
<body>
@php  $setting = App\SmGeneralSettings::find(1);  if(!empty($setting->currency_symbol)){ $currency = $setting->currency_symbol; }else{ $currency = '$'; }   @endphp 
   
 
    <table style="width: 100%;">
        <tr>
             
            <td style="width: 30%"> 
                <img src="{{url($setting->logo)}}" alt="{{url($setting->logo)}}"> 
            </td> 
            <td  style="width: 70%">  
                <h3>{{$setting->school_name}}</h3>
                <h4>{{$setting->address}}</h4>
            </td> 
        </tr> 
    </table>
    <hr>
    <table class="school-table school-table-style" cellspacing="0" width="100%">
        <tr>
            <td>@lang('lang.student_name')</td>
            <td>{{$student->full_name}}</td>
            <td>@lang('lang.roll_number')</td>
            <td>{{$student->roll_no}}</td>
        </tr>
        <tr>
            <td> @lang('lang.father_name')</td>
            <td>{{$student->parents->fathers_name}}</td>
            <td>@lang('lang.class')</td>
            <td>{{$student->className->class_name}}</td>
        </tr>
        <tr>
            <td> @lang('lang.section')</td>
            <td>{{$student->section->section_name}}</td>
            <td>@lang('lang.admission_no')</td>
            <td>{{$student->admission_no}}</td>
        </tr>
    </table>
        <h4 class="text-center mt-1"><span>@lang('lang.fees_details')</span></h4>
    
	<table class="school-table school-table-style" cellspacing="0" width="100%">
        <thead>
            <tr align="center">
                <th>@lang('lang.fees_group')</th>
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
                $grand_total = 0;
                $total_fine = 0;
                $total_discount = 0;
                $total_paid = 0;
                $total_grand_paid = 0;
                $total_balance = 0;
          
                    if($fees_assigned->feesGroupMaster->fees_group_id != 1 && $fees_assigned->feesGroupMaster->fees_group_id != 2){
                        $grand_total += $fees_assigned->feesGroupMaster->amount;
                    }else{
                        if($fees_assigned->feesGroupMaster->fees_group_id == 1){
                            $grand_total += $student->route->far;
                        }else{
                            $grand_total += $student->room->cost_per_bed;
                        }
                    }
                    
                @endphp

                @php
                    $discount_amount = App\SmFeesAssign::discountSum($fees_assigned->student_id, $fees_assigned->feesGroupMaster->feesTypes->id, 'discount_amount');
                    $total_discount += $discount_amount;
                    $student_id = $fees_assigned->student_id;
                @endphp
                @php
                    $paid = App\SmFeesAssign::discountSum($fees_assigned->student_id, $fees_assigned->feesGroupMaster->feesTypes->id, 'amount');
                    $total_grand_paid += $paid;
                @endphp
                @php
                    $fine = App\SmFeesAssign::discountSum($fees_assigned->student_id, $fees_assigned->feesGroupMaster->feesTypes->id, 'fine');
                    $total_fine += $fine;
                @endphp
                 
                @php
                    $total_paid = $discount_amount + $paid;
                @endphp
            <tr align="center">
                <td>{{$fees_assigned->feesGroupMaster !=""?$fees_assigned->feesGroupMaster->feesGroups->name:""}}</td>
                <td>{{$fees_assigned->feesGroupMaster !=""?$fees_assigned->feesGroupMaster->feesTypes->name:""}}</td>
                <td>
                    @if($fees_assigned->feesGroupMaster !="")
                        
                        {{$fees_assigned->feesGroupMaster->date != ""? App\SmGeneralSettings::DateConvater($fees_assigned->feesGroupMaster->date):''}}

                    @endif
                </td>
                <td>
                    @if($fees_assigned->feesGroupMaster->fees_group_id != 1 && $fees_assigned->feesGroupMaster->fees_group_id != 2)
                        @if($fees_assigned->feesGroupMaster->amount == $total_paid)
                        <span class="text-success">@lang('lang.paid')</span>
                        @elseif($total_paid != 0)
                        <span class="text-warning">@lang('lang.partial')</span>
                        @elseif($total_paid == 0)
                        <span class="text-danger">@lang('lang.unpaid')</span>
                        @endif
                    @else
                        @if($fees_assigned->feesGroupMaster->fees_group_id == 1)
                            @if($student->route->far == $total_paid)
                            <span class="text-success">@lang('lang.paid')</span>
                        @elseif($total_paid != 0)
                        <span class="text-warning">@lang('lang.partial')</span>
                        @elseif($total_paid == 0)
                        <span class="text-danger">@lang('lang.unpaid')</span>
                            @endif
                        @elseif($fees_assigned->feesGroupMaster->fees_group_id == 2)
                            @if($student->room->cost_per_bed == $total_paid)
                            <span class="text-success">@lang('lang.paid')</span>
                        @elseif($total_paid != 0)
                        <span class="text-warning">@lang('lang.partial')</span>
                        @elseif($total_paid == 0)
                        <span class="text-danger">@lang('lang.unpaid')</span>
                            @endif
                        @endif    
                    @endif    
                </td>
                <td>
                    @php
                        if($fees_assigned->feesGroupMaster->fees_group_id != 1 && $fees_assigned->feesGroupMaster->fees_group_id != 2){
                            echo $fees_assigned->feesGroupMaster->amount;
                        }else{
                            if($fees_assigned->feesGroupMaster->fees_group_id == 1){
                                echo $student->route->far;
                            }else{
                                echo $student->room->cost_per_bed;
                            }
                        }
                        
                    @endphp
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td> {{$discount_amount}} </td>
                <td>{{$fine}}</td>
                <td>{{$paid}}</td>
                <td>
                    @php 

                        if($fees_assigned->feesGroupMaster->fees_group_id != 1 && $fees_assigned->feesGroupMaster->fees_group_id != 2){
                            $rest_amount = $fees_assigned->feesGroupMaster->amount - $total_paid;
                        }else{
                            if($fees_assigned->feesGroupMaster->fees_group_id == 1){
                               $rest_amount = $student->route->far - $total_paid;
                            }else{
                               $rest_amount = $student->room->cost_per_bed - $total_paid;
                            }
                        }

                        $total_balance +=  $rest_amount;
                        echo $rest_amount;
                    @endphp
                </td>
            </tr>
                @php 
                    $payments = App\SmFeesAssign::feesPayment($fees_assigned->feesGroupMaster->feesTypes->id, $fees_assigned->student_id);
                    $i = 0;
                @endphp

                @foreach($payments as $payment)
                <tr align="center">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><img src="{{asset('public/backEnd/img/table-arrow.png')}}"></td>
                    <td>{{$payment->fees_type_id.'/'.$payment->id}}</td>
                    <td>
                    @if($payment->payment_mode == "C")
                            {{'Cash'}}
                    @elseif($payment->payment_mode == "Cq")
                        {{'Cheque'}}
                    @else
                        {{'DD'}}
                    @endif 
                    </td>
                    <td>
                        
                        {{$payment->payment_date != ""? App\SmGeneralSettings::DateConvater($payment->payment_date):''}}

                    </td>
                    <td>{{$payment->discount_amount}}</td>
                    <td>{{$payment->fine}}</td>
                    <td>{{$payment->amount}}</td>
                    <td></td>
                </tr>
                @endforeach
            
        </tbody>
    </table>

</body>
</html>
