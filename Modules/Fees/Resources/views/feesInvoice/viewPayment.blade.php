<div class="modal-header">
    <h4 class="modal-title">@lang('fees::feesModule.view_payment_of') - ({{$feesinvoice->invoice_id}})</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <table class="display school-table school-table-style shadow-none p-0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>@lang('common.sl')</th>
                <th>@lang('common.date')</th>
                <th>@lang('fees::feesModule.payment_method')</th>
                <th>@lang('fees::feesModule.paid_amount')</th>
                <th>@lang('fees::feesModule.waiver')</th>
                <th>@lang('fees.fine')</th>
                <th>@lang('fees.note')</th>
                <th>@lang('common.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feesTranscations as $key=>$feesTranscation)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{dateConvert($feesTranscation->created_at)}}</td>
                    <td>{{$feesTranscation->payment_method}}</td>
                    <td>{{$feesTranscation->paid_amount}}</td>
                    <td>{{$feesTranscation->weaver}}</td>
                    <td>{{$feesTranscation->fine}}</td>
                    <td>{{$feesTranscation->payment_note}}</td>
                    <td>
                        <a class="primary-btn icon-only fix-gr-bg" type="button" href="{{route('fees.single-payment-view',['id'=>$feesTranscation->id])}}" title="@lang('common.view')">
                            <span class="ti-eye"></span>
                        </a>
                        @if($feesTranscation->payment_method== "Cash" || $feesTranscation->payment_method == "Cheque" || $feesTranscation->payment_method == "Bank" || $feesTranscation->payment_method == "Wallet")
                            <a class="primary-btn icon-only fix-gr-bg" type="button" href="{{route('fees.delete-single-fees-transcation',$feesTranscation->id)}}" data-tooltip="tooltip" title="@lang('common.delete')">
                                <span class="ti-trash"></span>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>