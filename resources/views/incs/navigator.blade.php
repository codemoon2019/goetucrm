<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar users panel -->
        <div class="user-panel">
            <div class="image">
                <img src="{{ asset("images/user_img/goetu-profile.png") . "?v=" . config("app.version") }}" class="img-responsive" alt="User Image" style="border-radius: 100%; width: 120px;">
            </div>
            <ul>
                <li>Welcome, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</li>
                <li>{{session('company_name')}} / {{session('user_type_display')!="" ? session('user_type_display') : Auth::user()->username}} </li>
                @if(session('user_type_display')!="")
                    <li>{{Auth::user()->username}} </li>
                @endif
            </ul>
            {{--<ul class="user-panel-btn">--}}

                {{--<li>--}}
                {{--@php --}}
                    {{--$is_merchant=0;--}}
                    {{--$user_types = explode(',',Auth::user()->user_type_id);--}}
                    {{--if(in_array(8, $user_types)) $is_merchant=1;--}}
                {{--@endphp--}}
                {{--@if(Auth::user()->reference_id > 0 && $is_merchant==0)--}}
                {{--<a href="{{ url('partners/details/profile/'.Auth::user()->reference_id.'/profileCompanyInfo') }}">--}}
                {{--@elseif(Auth::user()->reference_id > 0 && $is_merchant==1)--}}
                {{--<a href="{{ url('merchants/details/'.Auth::user()->reference_id.'/profile') }}">--}}
                {{--@else--}}
                {{--<a href="{{ url('admin/users/'.Auth::user()->id.'/edit') }}">--}}
                {{--@endif--}}
                {{--Profile--}}

                {{--</a></li>--}}
                {{--<li><a href="{{ url('extras/changePassword/') }}">Change Password</a></li>--}}
                {{--<li><a href="{{ url("/logout") }}" onclick="chatOffline();">Sign Out</a></li>--}}
            {{--</ul>--}}
            <div class="sidebar-form user-quick-btns">
                <a href="{{ url('extras/notification') }}">
            <span class="fa-stack fa-lg">
                @php
                    $new_message_count = App\Models\Notification::get_new_messages_count();
                @endphp
                @if($new_message_count > 0)
                <label id="notif-count-notifs" class="label-danger notif-count-extras">{{$new_message_count}}</label>
                @endif
                <i class="fa fa-circle fa-stack-2x icon-background1"></i>
                <i class="fa fa-bell @if($new_message_count > 0) faa-tada animated @endif fa-stack-1x"></i>
            </span>
                </a>
                <a href="{{ url('extras/chatCenter') }}">
            <span class="fa-stack fa-lg">
                <!-- <label id="notif-count-msgs" class="notif-count-extras">0</label> -->
                <i class="fa fa-circle fa-stack-2x icon-background2"></i>
                <i class="fa fa-comments fa-stack-1x"></i>
            </span>
                </a>
                <a href="{{ url('extras/friendRequest') }}">
            <span class="fa-stack fa-lg">
                @php
                    $new_friend_request = App\Models\ChatFriendRequest::get_new_friend_requests_count();
                @endphp
                @if($new_friend_request > 0)
                <label id="notif-count-contacts" class=" label-danger notif-count-extras">{{$new_friend_request}}</label>
                @endif
                <i class="fa fa-circle fa-stack-2x icon-background3"></i>
                {{-- <img class="quick-img @if($new_friend_request > 0)  faa-tada animated @endif" src="{{ asset('images/contact-request.png') }}" title="Contact Request"> --}}
                <i class="fa fa-user-plus @if($new_friend_request > 0)  faa-tada animated @endif fa-stack-1x"></i>
            </span>
                </a>
            </div>
        </div>

        @include('incs.menu')
    </section>
    <!-- /.sidebar -->
</aside>