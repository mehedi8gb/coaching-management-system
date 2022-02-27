
@if(userPermission(1130) && menuStatus(1130))
    <li data-position="{{menuPosition(1130)}}" class="sortable_li">
        <a href="#subMenuFees" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <span class="flaticon-test"></span>
            @lang('fees.fees')
        </a>
        <ul class="collapse list-unstyled" id="subMenuFees">
            @if(userPermission(1131) && menuStatus(1131))
                <li data-position="{{menuPosition(1131)}}">
                    <a href="{{ route('fees.fees-group') }}">@lang('fees.fees_group')</a>
                </li>
            @endif

            @if(userPermission(1135) && menuStatus(1135))
                <li data-position="{{menuPosition(1135)}}">
                    <a href="{{ route('fees.fees-type') }}">@lang('fees.fees_type')</a>
                </li>
            @endif

            @if(userPermission(1139) && menuStatus(1139))
                <li data-position="{{menuPosition(1139)}}">
                    <a href="{{ route('fees.fees-invoice-list') }}">@lang('fees::feesModule.fees_invoice')</a>
                </li>
            @endif

            @if(userPermission(1148) && menuStatus(1148))
                <li data-position="{{menuPosition(1148)}}">
                    <a href="{{ route('fees.bank-payment') }}">@lang('fees.bank_payment')</a>
                </li>
            @endif

            @if(userPermission(1152) && menuStatus(1152))
                <li data-position="{{menuPosition(1152)}}">
                    <a href="{{ route('fees.fees-invoice-settings') }}">@lang('fees::feesModule.fees_invoice_settings')</a>
                </li>
            @endif

            @if(userPermission(1154) && menuStatus(1154))
                <li data-position="{{menuPosition(1154)}}">
                    <a href="{{ route('fees.due-fees') }}">@lang('fees::feesModule.fees_due')</a>
                </li>
            @endif
        </ul>
    </li>
@endif