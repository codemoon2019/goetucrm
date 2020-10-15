@extends('layouts.app')

@section('content')
@include("partners.details.profile.partnertabs")

        <div class="content">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 col-xs-6">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>Merchant Purchases</b></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table datatable responsive table-condense table-striped table-bordered" id="merchant-purchase">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center;" rowspan="2">Merchant</th>
                                                <th colspan="3" style="text-align: center;background: #dd4b39">Unpaid</th>
                                                <th colspan="3" style="text-align: center;background: #00a65a">Paid</th>
                                            </tr>

                                            <tr>
                                                <th style="text-align: center;background: #dd4b39">This Month</th>
                                                <th style="text-align: center;background: #dd4b39">This Year</th>
                                                <th style="text-align: center;background: #dd4b39">Overall</th>
                                                <th style="text-align: center;background: #00a65a">This Month</th>
                                                <th style="text-align: center;background: #00a65a">This Year</th>
                                                <th style="text-align: center;background: #00a65a">Overall</th>
                                            </tr>
                                            </thead>
                                            <tbody style="text-align: center">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

@endsection
@section('script')
<script>
    $('#merchant-purchase').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        lengthChange: false,
        ajax: '/partners/details/dashboard/merchant-purchase/{{$id}}',
        columns: [
            {data: 'merchant'},
            {data: 'monthTotalU'},
            {data: 'yearTotalU'},
            {data: 'overallTotalU'},
            {data: 'monthTotal'},
            {data: 'yearTotal'},
            {data: 'overallTotal'}
        ]
    });
</script>
@endsection