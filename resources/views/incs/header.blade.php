<header class="main-header">
    <nav class="navbar navbar-static-top bg-primary">
        <ul class="nav">
            <li class="nav-item">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
            </li>
            <li class="nav-item quick-btn hide">
                @if(App\Models\Access::hasPageAccess('company','add',true) ||
                    App\Models\Access::hasPageAccess('iso','add',true) ||
                    App\Models\Access::hasPageAccess('sub iso','add',true) ||
                    App\Models\Access::hasPageAccess('agent','add',true) ||
                    App\Models\Access::hasPageAccess('sub agent','add',true)
                    )
                <a class="btn btn-default btn-header" href="{{ url("partners/create") }}" role="button" >
                    New Partner
                </a>
                @endif
            </li>

            <li class="nav-item quick-btn hide">
                @if (App\Models\Access::hasPageAccess('merchant', 'add', true))
                    <a class="btn btn-default btn-header" href="{{ url("merchants/create") }}" role="button" >
                        New Merchant
                    </a>
                @endif
            </li>

            <li class="nav-item quick-btn hide">
                @if(App\Models\Access::hasPageAccess('ticketing','add',true))
                <a href="{{ url("tickets/create") }}" class="btn btn-default btn-header" role="button" >
                    Create Ticket
                </a>
                @endif
            </li>
            <li class="nav-item">
                <input type="text" class="form-control" id="generalSearch" value = "" name="generalSearch" placeholder="Search...">
            </li>
            <li class="nav-item">
                <select type="text" class="form-control" id="generalSearchType">
                    <option value="partner">Partner DBA</option>
                    <option value="merchant">Merchant DBA</option>
                    <option value="invoice">Invoice No.</option>
                    <option value="order">Order No.</option>
                    <option value="contact">Contact Name</option>
                    <option value="domain">Domain</option>
                    <option value="mid">MID</option>
                    <option value="billingid">Billing ID</option>
                    <option value="task">Task Name</option>
                </select>
            </li>

        </ul>

        <ul class="nav pull-right">
            <li class="nav-item quick-btn hide" style="padding: 10px;">
                    <img src="{{ asset("images/suggest-button.png") }}" style="height: 50px" title="Have a great idea? We want to hear it!" onclick="showSuggestionBox()">
       
<!--                 <a href="javascript:void(0);" onclick="showSuggestionBox()" style="font-size: 12px">
                    <b>Have a great idea? <br> We want to hear it!</b>
                    <span class="fa-stack fa-lg">
                        <i src="{{ asset("images/user_img/goetu-profile.png") }}" title="Have a great idea? We want to hear it!"></i>
                    </span>
                </a> -->
            </li>  
            <li class="nav-item dropdown user user-menu">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="true" role="button" >
                    Welcome, {{ Auth::user()->first_name }} &nbsp;&nbsp;
                    <img src="{{ Auth::user()->image }}" class="img-responsive user-image-head" alt="User Image" style="border-radius: 100%; width: 30px;height: 30px;">
                </a>
                <ul class="dropdown-menu header-dropdown-menu">
                    <li>
                        @php
                            $is_merchant=0;
                            $user_types = explode(',',Auth::user()->user_type_id);
                            if(in_array(8, $user_types)) $is_merchant=1;
                        @endphp
                        @if(substr(Auth::user()->username, 0,1) == 'C')
                            <a href="{{ url('partners/details/profile/'.Auth::user()->reference_id.'/profileCompanyInfo') }}" class="nav-link">
                        @elseif(session('is_internal'))
                            <a href="{{ url('/user-profile') }}" class="nav-link">
                        @elseif((substr(Auth::user()->username, 0,1) == 'M') && $is_merchant==1)
                            <a href="{{ url('merchants/details/'.Auth::user()->reference_id.'/profile') }}" class="nav-link">
                        @elseif((substr(Auth::user()->username, 0,1) == 'B') && $is_merchant==1)
                            <a href="{{ url('merchants/branchDetails/'.Auth::user()->reference_id.'/profile') }}" class="nav-link">
                        @else
                            <a href="{{ url('partners/details/profile/'.Auth::user()->reference_id.'/profileCompanyInfo') }}" class="nav-link">
                        @endif
                            Profile
                        </a>
                    </li>
                    @if(session('is_internal') && Auth::user()->username != 'admin' && substr(Auth::user()->username, 0,1) != 'C')
                    <li><a href="#" onclick="showChangeCompany()" class="nav-link">Change Company</a></li>
                    @endif
                    <li><a href="{{ url('extras/changePassword/') }}" class="nav-link">Change Password</a></li>
                    <li><a href="{{ route('extras.user.settings.edit') }}" class="nav-link">Settings</a></li>

                    <!-- <li><a href="{{ url("/logout") }}" onclick="chatOffline();" class="nav-link">Sign Out</a></li> -->
                </ul>
            </li>
        </ul>
    </nav>
</header>