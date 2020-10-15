@extends('layouts.app')

@section('content')
    <body> 
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Companies 
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Admin</a></li>
                <li class="active">Company Settings</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                <h5>Select a company to view their information ...</h5>
            </div>
            <div class="clearfix"></div>
            
   
            <div class="col-md-12 no-padding">
                <table id="tblCompanySettings" class="table datatables table-condense  table-striped">
                    <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Mobile Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if(count($partners)>0)
                            @foreach($partners as $partner)
                                <tr>                   
                                    <td>{{$partner->company_name}}</td>
                                    <td>{{$partner->first_name}} {{$partner->last_name}}</td>
                                    <td>{{$partner->country_code}}{{$partner->phone1}}</td>
                                    <td>{{$partner->email}}</td>
                                    <td>
                                        <a href='{{ url("admin/company_settings/configuration_menu/$partner->partner_id") }}' class="btn btn-info btn-sm">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
       

        </section>
    </div>
    </body>
@endsection
@section('script')
    <script src="{{ config(" app.cdn ") . "/js/admin/companysettings.js" . "?v=" . config(" app.version ") }}"></script>
@endsection