@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Create Reply Template
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Ticket</a></li>
                <li class="active">Reply Templates</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <!-- <div class="nav-tabs-custom"> -->
                <!-- Tabs within a box -->
                <!-- <div class="tab-content no-padding"> -->
                    <div class="tab-pane" id="welcome-email">
                        <span class="pull-right"><a href="{{ url("tickets/reply-template-create") }}" class="btn btn-success">Create Reply Template</a></span>
                        <table id="wemail-table"  name="wemail-table"  class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="75%">Name</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                        </table>
<!--                     </div>
                </div> -->
        </section>
        <!-- /.content -->
    </div>
@endsection
@section("script")
    <script>
        $('#wemail-table').DataTable({
              serverSide: true,
              processing: true,
              ajax: '/tickets/reply-template-data',
              columns: [
                  {data: 'name'},
                  {data: 'action', name: 'action', orderable: false, searchable: false}
              ]
          });

    </script>
@endsection