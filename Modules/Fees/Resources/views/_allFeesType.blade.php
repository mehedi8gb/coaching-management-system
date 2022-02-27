@if (isset($feesGroups))
    @foreach ($feesGroups as $key=>$feesGroup)
        <tr>
            <td></td>
            <td>{{$feesGroup->name}} ({{$feesGroup->fessGroup->name}})</td>
            <input type="hidden" name="feesType[]" value="{{$feesGroup->id}}">
            <td>
                <div class="input-effect">
                    <input class="primary-input form-control amount{{ $errors->has('amount') ? ' is-invalid' : '' }}" type="text" name="amount[]" autocomplete="off" value="{{old('amount')}}">
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
                    <input class="primary-input form-control weaver{{ $errors->has('weaver') ? ' is-invalid' : '' }}" type="text" name="weaver[]" autocomplete="off" value="{{old('weaver')}}">
                    <span class="focus-border"></span>
                    @if ($errors->has('weaver'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('weaver') }}</strong>
                    </span>
                    @endif
                </div>
            </td>
            <td class="subTotal"></td>
            <input type="hidden" name="sub_total[]" class="inputSubTotal">
            <td>
                <input class="primary-input form-control paidAmount{{ $errors->has('paid_amount') ? ' is-invalid' : '' }}" type="text" name="paid_amount[]" autocomplete="off" disabled>
            </td>
            <td>
                <button class="primary-btn icon-only fix-gr-bg" data-toggle="modal" data-target="#addNotesModal{{$feesGroup->id}}" type="button"
                    data-tooltip="tooltip" data-placement="top" title="@lang('common.add_note')">
                    <span class="ti-pencil-alt"></span>
                </button>
                <button class="primary-btn icon-only fix-gr-bg" type="button" data-tooltip="tooltip" title="@lang('common.delete')" id="deleteField">
                    <span class="ti-trash"></span>
                </button>
                {{-- Notes Modal Start --}}
                <div class="modal fade admin-query" id="addNotesModal{{$feesGroup->id}}">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">@lang('common.add_note')</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">
                                <div class="input-effect">
                                    <input class="primary-input form-control has-content" type="text" name="note[]" autocomplete="off">
                                    <label>@lang('common.note')</label>
                                    <span class="focus-border"></span>
                                </div>
                                </br>
                                <div class="mt-40 d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                                    <button type="button" class="primary-btn fix-gr-bg" data-dismiss="modal">@lang('common.save')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Notes Modal End --}}
                <input type="hidden" class="fees" value="grp{{$feesGroup->fessGroup->id}}">
                <input type="hidden" class="fees" value="typ{{$feesGroup->id}}">
            </td>
        </tr>
    @endforeach
@endif

@if (isset($feesType))
    <tr>
        <td></td>
        <td>{{$feesType->name}}</td>
        <input type="hidden" name="feesType[]" value="{{$feesType->id}}">
        <td>
            <div class="input-effect">
                <input class="primary-input form-control amount{{ $errors->has('amount') ? ' is-invalid' : '' }}" type="text" name="amount[]" autocomplete="off" value="{{old('amount')}}">
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
                <input class="primary-input form-control weaver{{ $errors->has('weaver') ? ' is-invalid' : '' }}" type="text" name="weaver[]" autocomplete="off" value="{{old('weaver')}}">
                <span class="focus-border"></span>
                @if ($errors->has('weaver'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('weaver') }}</strong>
                </span>
                @endif
            </div>
        </td>
        <td class="subTotal"></td>
        <input type="hidden" name="sub_total[]" class="inputSubTotal">
        <td>
            <input class="primary-input form-control paidAmount{{ $errors->has('paid_amount') ? ' is-invalid' : '' }}" type="text" name="paid_amount[]" autocomplete="off" disabled>
        </td>
        <td>
            <button class="primary-btn icon-only fix-gr-bg" data-toggle="modal" data-target="#addNotesModal{{$feesType->id}}" type="button"
                data-tooltip="tooltip" data-placement="top" title="@lang('common.add_note')">
                <span class="ti-pencil-alt"></span>
            </button>
            <button class="primary-btn icon-only fix-gr-bg" data-tooltip="tooltip" title="@lang('common.delete')" type="button" id="deleteField">
                <span class="ti-trash"></span>
            </button>
            {{-- Notes Modal Start --}}
            <div class="modal fade admin-query" id="addNotesModal{{$feesType->id}}">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">@lang('common.add_note')</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <div class="input-effect">
                                <input class="primary-input form-control has-content" type="text" name="note[]" autocomplete="off">
                                <label>@lang('common.note')</label>
                                <span class="focus-border"></span>
                            </div>
                            </br>
                            <div class="mt-40 d-flex justify-content-between">
                                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                                <button type="button" class="primary-btn fix-gr-bg" data-dismiss="modal">@lang('common.save')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Notes Modal End --}}
            <input type="hidden" class="fees" value="typ{{$feesType->id}}">
            <input type="hidden" class="fees" value="grp{{$feesType->fees_group_id}}">
        </td>
    </tr>
@endif
<script>
    $('[data-tooltip="tooltip"]').tooltip();
</script>
