@if(userPermission(900) && menuStatus(900))
    <li  data-position="{{menuPosition(900)}}" class="sortable_li">
        <a href="#subMenuChat" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <span class="flaticon-test"></span>
            @lang('chat::chat.chat')
        </a>
        <ul class="collapse list-unstyled" id="subMenuChat">
            @if(userPermission(901) && menuStatus(901))
                <li  data-position="{{menuPosition(901)}}" >
                    <a href="{{ route('chat.index') }}">@lang('chat::chat.chat_box')</a>
                </li>
            @endif

            @if(userPermission(903) && menuStatus(903))
                <li data-position="{{menuPosition(903)}}" >
                    <a href="{{ route('chat.invitation') }}">@lang('chat::chat.invitation')</a>
                </li>
            @endif

            @if(userPermission(904) && menuStatus(904))
                <li data-position="{{menuPosition(904)}}" >
                    <a href="{{ route('chat.blocked.users') }}">@lang('chat::chat.blocked_user')</a>
                </li>
            @endif

            @if(userPermission(905) && menuStatus(905))
                <li data-position="{{menuPosition(905)}}" >
                    <a href="{{ route('chat.settings') }}">@lang('chat::chat.settings')</a>
                </li>
            @endif
        </ul>
    </li>
@endif