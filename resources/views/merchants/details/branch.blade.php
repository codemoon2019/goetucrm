@extends('layouts.app')


@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
    .custom-fl-right {
        float: right;
    }
    .ta-right {
        text-align: right;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Merchant
            <!-- <small>Dito tayo magpapasok ng different pages</small> -->
        </h1>
        <ol class="breadcrumb">
            <li><a href="/">Dashboard</a></li>
            <li><a href="/merchants">Merchant</a></li>
            <li class="active">{{$merchant->partner_company->company_name}}</li>
        </ol>
        <div class="dotted-hr"></div>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
            <h3>
                <span>{{ $merchant->partner_company->company_name }}</span>
            </h3>
            <a href="/merchants" class="btn btn-default pull-right" style="margin-top: -40px">Back to Merchants</a>
        </div>
        <div class="nav-tabs-custom">
            @include("merchants.details.merchanttabs")
            <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                <li class="active"><a href="#branch-list" id="branchlist" data-toggle="tab" aria-expanded="true">Branch List</a></li>
            </ul>

            <div class="tab-content no-padding">
                <div class="tab-pane active" id="branch-list">
                    <div class="col-md-12" style="margin-top:20px;">
                        <table id="branch-tbl" name="branch-tbl" class="table responsive datatables table-condense p-0">
                            <thead>
                                <tr>
                                    <th>Branch ID</th>
                                    {{--  <th>Owner</th>         --}}
                                    <th>Branch Name</th>
                                    <th>Status</th>
                                    <th>MID</th>
                                    <th>CID</th>
                                    <th>Contact Person</th>
                                    <th>Mobile Number</th>
                                    <th>Email</th>
                                    <th>State</th>
                                    <th>URL</th>
                                </tr>
                            </thead>
                        </table>
                            
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>


    @endsection
    @section('script')
    <script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $('.datatables').dataTable();

        load_branches();

        function load_branches(){ 
            $.getJSON('/merchants/merchant_branch_data/' + {{$merchant->id}}, null, function(data) { 
             
                $('#branch-tbl').dataTable().fnDestroy();
                var oTable = $('#branch-tbl').dataTable({
                    "lengthMenu": [25, 50, 75, 100 ],
                    "bRetrieve": true
                });

                oTable.fnClearTable();
                if (data.length >0){
                    oTable.fnAddData(data);    
                }
                $('#branch-tbl').DataTable().columns.adjust().responsive.recalc();
            });
            
        }

    </script>
    @endsection