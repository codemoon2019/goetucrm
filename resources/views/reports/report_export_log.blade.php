@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Report Export Logs
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">Report Export Logs</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12 clear" >
                    <div class="row" >
                         <div class="col-md-4">
                         </div>
                        <div class="col-md-2">
                            <label> From: </label> 
                            <input type="text" class="form-control custom-input-8 fromDate" value=" {{$orig_from or date_format(new DateTime(),"Y-m-d")}} " id="txtFromDate" name="txtFromDate" onblur="loadReport()">

                        </div>

                        <div class="col-md-2">
                            <label> To: </label> 
                            <input type="text" class="form-control custom-input-8 toDate" value=" {{$orig_to or  date_format(new DateTime(),"Y-m-d")}} " id="txtToDate" name="txtToDate" onblur="loadReport()">
                        </div>

                    </div>
                </div>
                <div class="clear"></div>
            </div>


            @if(isset($logs))
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">Report Export Logs</h3>
                        </div>
                        <table class="table-striped table-bordered" style="width: 1200px;margin:0 auto;">
                            <thead class="text-center">
                            <tr>
                                <th>User</th>
                                <th>Report</th>
                                <th>Date Exported</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td style="text-align: center">{{$log->user->first_name}} {{$log->user->last_name}}</td>
                                        <td style="text-align: center">{{$log->report_name}}</td>
                                        <td style="text-align: center">{{$log->created_at}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

        $('#generateReport').click(function () {
            window.location = '/billing/export-log-generate-report/'+$('#txtFromDate').val()+'/'+$('#txtToDate').val();
        });
        function loadReport(){
            if(checkDates()){
                if($('#init').val() == 1){
                    window.location = '/billing/export-log-generate-report/'+$('#txtFromDate').val()+'/'+$('#txtToDate').val();
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