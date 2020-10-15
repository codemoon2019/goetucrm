@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                ACH Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">ACH Report</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12 clear" >
                    <div class="row" >
                         <div class="col-md-2">
                         </div>
                        <div class="col-md-2">
                            <label> From: </label> 
                            <input type="text" class="form-control custom-input-8 fromDate" value=" {{$orig_from or date_format(new DateTime(),"Y-m-d")}} " id="txtFromDate" name="txtFromDate" onblur="checkDates()">

                        </div>

                        <div class="col-md-2">
                            <label> To: </label> 
                            <input type="text" class="form-control custom-input-8 toDate" value=" {{$orig_to or  date_format(new DateTime(),"Y-m-d")}} " id="txtToDate" name="txtToDate" onblur="checkDates()">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>  
                            <input id="generateReport" value="Generate Report" class="btn btn-primary form-control" type="button">
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>  
                            <input id="generateResidual" value="Generate Residual" class="btn btn-primary form-control" type="button">
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>


            @if(isset($newInvoices))
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">ACH Transaction Report</h3>
                        </div>
                        <table class="table-striped table-bordered" style="width: 1200px;margin:0 auto;">
                            <thead class="text-center">
                            <tr>
                                <th>CUSTOMER ID</th>
                                <th>NAME</th>
                                <th>EED</th>
                                <th>AMOUNT</th>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT NAME</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($newInvoices as $invoice)
                                    <tr>
                                        @foreach($invoice as $inv)
                                        <td style="text-align: center">{{$inv}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                @if($ach_export)
                <div class="col-md-2 mt-plus-20">
                    <button class="btn btn-flat btn-success btn-block" id="exportReport">Export</button>
                </div>
                @endif
            @endif


            @if(isset($residualInvoices))
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">ACH Residual Report</h3>
                        </div>
                        <table class="table-striped table-bordered" style="width: 2300px;margin:0 auto;">
                            <thead class="text-center">
                            <tr>
                                <th>COMPANY</th>
                                <th>NAME</th>
                                <th>CUSTOMER ID</th>
                                <th>EED</th>
                                <th>AMOUNT</th>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT NAME</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($residualInvoices as $invoice)
                                    <tr>
                                        @foreach($invoice as $inv)
                                        <td style="text-align: center">{{$inv}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                @if($ach_export)
                <div class="col-md-2 mt-plus-20">
                    <button class="btn btn-flat btn-success btn-block" id="exportResidual">Export</button>
                </div>
                @endif
            @endif



        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/reports/comm_reports.js" . "?v=" . config("app.version") }}"></script>
    <script>
        $('.fromDate').datetimepicker({ 'format': 'YYYY-MM-DD' });
        $('.toDate').datetimepicker({ 'format': 'YYYY-MM-DD' });

        $('#generateReport').click(function () {
            window.location = '/billing/ach-generate-report/'+$('#txtFromDate').val()+'/'+$('#txtToDate').val();
        });

        $('#generateResidual').click(function () {
            window.location = '/billing/ach-generate-residual/'+$('#txtFromDate').val()+'/'+$('#txtToDate').val();
        });

        $('#exportReport').click(function () {
            window.location = '/billing/ach-export-report/'+$('#txtFromDate').val()+'/'+$('#txtToDate').val();
        });

        $('#exportResidual').click(function () {
            window.location = '/billing/ach-export-residual/'+$('#txtFromDate').val()+'/'+$('#txtToDate').val();
        });       

        function checkDates(){
            var startDate = document.getElementById("txtFromDate").value;
            var endDate = document.getElementById("txtToDate").value;

            if ((Date.parse(endDate) < Date.parse(startDate))) {
              alert("End date should be greater than Start date");
              document.getElementById("txtToDate").value = document.getElementById("txtFromDate").value;
            }
        }

    </script>
@endsection