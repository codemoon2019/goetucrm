@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Access Templates
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Access Templates</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">     
                        <h3>Access Templates</h3>
                        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                            @php
                                $access = session('all_user_access'); 
                                if (!isset($access['admin'])){
                                    return redirect('/')->with('failed','You have no access to that page.')->send();
                                }
                            @endphp
                            @php
                                if(strpos($access['admin'], 'access rights template') !== false){ @endphp
                                <a href="{{ url("admin/group-templates/create") }}" class="btn btn-success pull-right">Create Template</a>
                            @php } @endphp
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-md-12" style="margin-top:20px;">
                            <table id="template-table"  name="template-table"  class="table responsive table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th >Name</th>
                                    <th >Company</th>
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
    loadTemplate();
});

function loadTemplate(){
  $('#template-table').dataTable().fnDestroy();
    $('#template-table').DataTable({
         "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/group-templates/data',
        columns: [{
                data: 'name'
            },
            {
                data: 'company'
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

function deleteTemplate(id){
    if (confirm('Delete this Template?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/admin/group-templates/deleteTemplate',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    alert(data.msg);
                    loadTemplate();
                }else {
                    alert(data.msg);
                }
            }
        });
    }else {
        return false;
    }
}


    </script>
@endsection