@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Commission Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">Commission Report</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12 clear">
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-2">
                            <label> From: </label> 
                            <input type="text" class="form-control custom-input-8 fromDate report-control" value=" {{ $orig_from or date_format(new DateTime(),"Y-m-d")}} " id="txtFromDate" name="txtFromDate" onblur="loadReport()">

                        </div>

                        <div class="col-md-2">
                            <label> To: </label> 
                            <input type="text" class="form-control custom-input-8 toDate report-control" value=" {{ $orig_to or date_format(new DateTime(),"Y-m-d")}} " id="txtToDate" name="txtToDate" onblur="loadReport()">
                        </div>

                    </div>
                </div>
                <div class="clear"></div>
            </div>


            @if(isset($commissions))
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">Commission Report</h3>
                        </div>
                        <table class="table-striped table-bordered" style="width: 1200px;margin:0 auto;">
                            <thead class="text-center">
                            <tr>
                                <th>Customer ID</th>
                                <th>Merchant</th>
                                <th>Agent ID</th>
                                <th>Agent</th>
                                <th>Sales</th>
                                <th>Without Markup</th>
                                <th>Commission %</th>
                                <th>Mark-up</th>
                                <th>Markup %</th>
                                <th>Total Commission</th>
                                <th>Company Total</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($commissions as $commission)
                                    <tr>
                                        @foreach($commission as $com)
                                        <td style="text-align: center">{{$com}}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                @if($com_export)
                <div class="col-md-2 mt-plus-20">
                    <button class="btn btn-flat btn-success btn-block" id="exportReport">Export</button>
                </div>
                @endif
            @endif

            <input type="hidden" value="{{$init or 0}}" id="init">
        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/reports/comm_reports.js" . "?v=" . config("app.version") }}"></script>
    <script>       

        $('.fromDate').datetimepicker({ 'format': 'YYYY-MM-DD' });
        $('.toDate').datetimepicker({ 'format': 'YYYY-MM-DD' });

        $('#exportReport').click(function () {
            window.location = '/billing/commission-export-report/'+$('#txtFromDate').val().trim()+'/'+$('#txtToDate').val().trim();
        }); 

        function loadReport(){
            if(checkDates()){
                if($('#init').val() == 1){
                    window.location = '/billing/commission-generate-report/'+$('#txtFromDate').val().trim() +'/'+$('#txtToDate').val().trim();
                }
                $('#init').val(1);
            }

        }


        loadReport();
        function checkDates(){
            var startDate = document.getElementById("txtFromDate").value;
            var endDate = document.getElementById("txtToDate").value;

            if ((Date.parse(endDate) < Date.parse(startDate))) {
              alert("End date should be greater than Start date");
              document.getElementById("txtToDate").value = document.getElementById("txtFromDate").value;
              return false;
            }

            return true;
        }


    </script>
@endsection