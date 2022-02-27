@if(userPermission(1157) && menuStatus(1157))
    <li data-position="{{menuPosition(1157)}}" class="sortable_li">
        <a href="#subMenuParentMyChildrenFees" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <span class="flaticon-reading"></span>
            @lang('fees::feesModule.fees')
        </a>
        <ul class="collapse list-unstyled" id="subMenuParentMyChildrenFees">
            @foreach($childrens as $children)
                <li>
                    <a href="{{route('fees.student-fees-list', [$children->id])}}">{{$children->full_name}}</a>
                </li>
            @endforeach
        </ul>
    </li>
@endif