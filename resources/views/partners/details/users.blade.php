@extends('layouts.app')

@section('content')
                @php 
                    $access = session('all_user_access'); 
                    $canEdit = false;
                    if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                        if(strpos($access[strtolower($partner_info->partner_type_description)], 'payment') !== false){ 
                            $canEdit = true;
                        } 
                    } 
                @endphp

                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <li class="active"><a href="#user-lists" data-toggle="tab" aria-expanded="false">Company Users</a></li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="user-lists">     
                        <h3>Users</h3>
                        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                            @php
                                $access = session('all_user_access'); 
                                if (!isset($access['users'])){
                                    return redirect('/')->with('failed','You have no access to that page.')->send();
                                }
                            @endphp
                            @php
                                if(strpos($access['users'], 'add') !== false){ @endphp
                                <a href="{{ url("admin/users/create?id=".$id) }}" class="btn btn-success pull-right">Create User</a>
                            @php } @endphp
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12" style="margin-top:20px;">
                            <table id="users-table"  name="users-table"  class="table responsive table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Departments</th>
                                    <th>Email Address</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        </div>
                </div>
        </section>

        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
    <script type="text/javascript">
        
    $(document).ready(function () {

        $('#users-table').DataTable({
            serverSide: true,
            processing: true,
            ajax: '/partners/users/data/{{$id}}',
            columns: [{
                    data: 'id'
                },
                {
                    data: 'first_name'
                },
                {
                    data: 'last_name'
                },
                {
                    data: 'departments'
                },
                {
                    data: 'email'
                },
                {
                    data: 'country'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });  
    </script>

@endsection
