@if(userPermission(1156) && menuStatus(1156))
    <li data-position="{{menuPosition(1156)}}" class="sortable_li">
        <a href="{{route('fees.student-fees-list',[auth()->user()->student->id])}}">
            <span class="flaticon-wallet"></span>
            @lang('fees.fees')
        </a>
    </li>
@endif