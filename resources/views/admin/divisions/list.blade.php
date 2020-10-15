@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Divisions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Divisions</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">     
                        <h3>Divisions</h3>
                        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                            @php
                                $access = session('all_user_access'); 
                                if (!isset($access['admin'])){
                                    return redirect('/')->with('failed','You have no access to that page.')->send();
                                }
                            @endphp
                            @php
                                if(strpos($access['admin'], 'divisions') !== false){ @endphp
                                <a href="{{ url("admin/divisions/create") }}" class="btn btn-success pull-right">Create Division</a>
                            @php } @endphp
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12" style="margin-top:20px;">
                            <table id="divisions-table"  name="divisions-table"  class="table responsive table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th >Location</th>
                                    <th >Company</th>
                                    <th>Person in charge</th>
                                    <th >Country</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
@section("script")
    <script>
        
    $( document ).ready(function() {

      loadDivisions();

    });

    function loadDivisions(){
        $('#divisions-table').dataTable().fnDestroy();
        $('#divisions-table').DataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            serverSide: true,
            processing: true,
            ajax: '/admin/divisions/data',
            columns: [{
                    data: 'name'
                },
                {
                    data: 'company'
                },
                {
                    data: 'user'
                },
                {
                    data: 'country'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    function deleteDivision(id){
        if (confirm('Delete this division?')) {
            var formData = {
                id: id
            };

            $.ajax({
                type:'GET',
                url:'/admin/divisionsDelete',
                data:formData,
                dataType:'json',
                success:function(data){
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $("p#msg-success").html(data.msg);
                            // window.location.href = window.location.href;
                        }
                        loadDivisions();
                    }else {
                        if ($(".alert.alert-danger").hasClass('hide')) {
                            $(".alert.alert-danger").removeClass('hide');
                            $("p#msg-danger").html(data.msg);
                        }
                    }
                }
            });
        }else {
            return false;
        }
    }

    </script>
@endsection