    

    @php
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $isAdmin = false;
        }else{
            $isAdmin = true;
        }


    @endphp

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{$partner_info->display_name}} 
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="/partners/management">Partners</a></li>
                <li class="active">{{$partner_info->display_name}}</li>
                <li class="active">{{$partner_info->company_name}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">

            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3>{{$partner_info->company_name}} @if($partner_info->status == 'A')<span class="badge badge-success">Active</span>@endif @if($partner_info->status == 'I')<span class="badge badge-danger">Inactive</span>@endif @if($partner_info->status == 'T')<span class="badge badge-danger">Terminated</span>@endif</h3> 
                <a href="/partners/management" class="btn btn-default pull-right" style="margin-top: -40px">Back to Partners</a>
            </div>
            <div class="nav-tabs-custom">

<ul class="tabs-rectangular">
    <li style="display:none" class="{{strpos(Request::url(),'dashboard')!== false ? "active" : "" }}"><a href="{{ url('partners/details/dashboard/') }}/{{$id}}">Dashboard</a></li>
    <li class="{{strpos(Request::url(),'profile')!== false ? "active" : "" }}"><a href="{{ url('partners/details/profile/') }}/{{$id}}/profileCompanyInfo">Profile</a></li>
    <li class="{{strpos(Request::url(),'products')!== false ? "active" : "" }}"><a href="{{ url('partners/details/'.$id.'/products') }}">Products</a></li>
    <!-- <li class="{{strpos(Request::url(),'commissions')!== false ? "active" : "" }}"><a href="{{ url('partners/details/'.$id.'/commissions') }}">Commissions</a></li> -->
    <li class="{{strpos(Request::url(),'agents')!== false ? "active" : "" }}"><a href="{{ url('partners/details/agents/') }}/{{$id}}">Level Access</a></li>
    @if($partner_info->display_name == 'Company')
    <li class="{{strpos(Request::url(),'users')!== false ? "active" : "" }}"><a href="{{ url('partners/details/users/') }}/{{$id}}">Users</a></li>
    @endif
    <li class="{{strpos(Request::url(),'merchants')!== false ? "active" : "" }}"><a href="{{ url('partners/details/cross_merchants/') }}/{{$id}}">Cross Selling Merchants</a></li>
    <!-- <li class="{{strpos(Request::url(),'viewTicket')!== false ? "active" : "" }}"><a href="{{ url('partners/details/viewTickets/') }}/{{$id}}">View Ticket</a></li> -->
    @if($isAdmin) <li class="{{strpos(Request::url(),'crossselling')!== false ? "active" : "" }}"><a href="{{ url('partners/details/crossselling/') }}/{{$id}}">Cross Selling Agents</a></li>@endif
    @if($isInternal)<li class="{{strpos(Request::url(),'billing')!== false ? "active" : "" }}"><a href="{{ url('partners/details/billing/') }}/{{$id}}">Billing</a></li>@endif
</ul>