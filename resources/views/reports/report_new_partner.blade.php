@extends('layouts.app')
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href=@cdn('/css/tickets/create.css') />

    <style>

        .card2 {
          background: #fff;
          border-radius: 2px;
          display: inline-block !important;
          /*height: 100px;*/
          margin: 1rem;
          position: relative;
          width: 200px;
        }
        .recent-amount
        {
            font-family: 'Oswald', sans-serif;
            text-align: center;
            padding-top: 20px;
        }

        .recent-date
        {
            font-family: 'Oswald', sans-serif;
            text-align: center;
        }
    </style>

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                New Partners Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">New Partners Report</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12 clear">
                    <div class="row" style="padding-left: 100px">
                        <div class="col-md-3">
                            <label> Option: </label> 
                            <select class="report-control form-control select2" id="txtDateType" name="txtDateType">
                                <option value="Daily" @if(isset($type) && $type == "Daily") selected @endif >Daily</option>
                                <option value="Weekly" @if(isset($type) && $type == "Weekly") selected @endif >Weekly</option>
                                <option value="Monthly" @if(isset($type) && $type == "Monthly") selected @endif >Monthly</option>
                                <option value="Yearly" @if(isset($type) && $type == "Yearly") selected @endif >Yearly</option>
                                <option value="Custom" @if(isset($type) && $type == "Custom") selected @endif >Custom</option>
                            </select>
                        </div>

                        <div class="col-md-2 dailyDiv dateDiv">
                            <label> Day: </label> 
                            <input type="text" class="report-control form-control custom-input-8 dayDiv datepick" @if(isset($type) && $type == "Daily")  value="{{$from}}" @else value="{{date_format(new DateTime(),"Y-m-d")}}" @endif id="txtDate" name="txtDate">
                        </div>

                        <div class="col-md-2 customDiv dateDiv">
                            <label> From: </label> 
                            <input type="text" class="report-control form-control custom-input-8 datepick" @if(isset($type) && $type == "Custom")  value="{{$from}}" @else value="{{date_format(new DateTime(),"Y-m-d")}}" @endif id="txtFromDate" name="txtFromDate">
                        </div>

                        <div class="col-md-2 customDiv dateDiv">
                            <label> To: </label> 
                            <input type="text" class="report-control form-control custom-input-8 datepick" @if(isset($type) && $type == "Custom")  value="{{$to}}" @else value="{{date_format(new DateTime(),"Y-m-d")}}" @endif id="txtToDate" name="txtToDate">
                        </div>

                        <div class="col-md-3 weeklyDiv dateDiv">
                            <label> Week: </label> 
                            <input type="text" class="report-control form-control custom-input-8 weeklyDate"  @if(isset($type) && $type == "Weekly") value="{{$from}}" @else value="{{date_format((new \DateTime())->modify('-7 days'),"Y-m-d") }}" @endif id="txtWeeklyDateNewPartner" name="txtWeeklyDateNewPartner">
                            <input type="hidden" id="weeklyFrom" value="{{$from OR date_format(new DateTime(),"Y-m-d")}}"> 
                            <input type="hidden" id="weeklyTo" value="{{$to OR date_format(new DateTime(),"Y-m-d")}}"> 
                        </div>

                        <div class="col-md-2 monthlyDiv dateDiv">
                            <label> Month: </label> 
                            <input type="text" class="report-control form-control custom-input-8 monthlyDate" @if(isset($type) && $type == "Monthly") value="{{$from}}" @else value="{{date_format(new DateTime(),"Y-m")}}" @endif id="txtMonthlyDate" name="txtMonthlyDate">
                        </div>

                        <div class="col-md-2 yearlyDiv dateDiv">
                            <label> Yearly: </label> 
                            <input type="text" class="report-control form-control custom-input-8 yearlyDate" @if(isset($type) && $type == "Yearly") value="{{$from}}" @else value="{{date_format(new DateTime(),"Y")}}" @endif id="txtYearlyDate" name="txtYearlyDate">
                        </div>
                    </div>

                    <div class="row" style="padding-left: 100px">
                        <div class="col-md-4 form-group form-group-partner" style="margin-top: 10px">
                          <label>Partner</label>
                          <select class="js-example-basic-single form-control report-control" name="partner" id="partner" data-placeholder="Select Partner" data-allow-clear="true">
                            <option value="-1" data-image="/images/agent.png" @if(isset($partnerId) && $partnerId == -1) selected @endif>&nbsp;&nbsp;All Partners</option>
                              @foreach ($partnerTypes->sortBy('name') as $pt)
                                <optgroup label="{{ $pt->name }}">
                                @foreach ($pt->list->sortBy('name') as $pl)
                                    <option value="{{ $pl->id }}" data-image="/images/agent.png" @if(isset($partnerId) && $partnerId == $pl->id) selected @endif>
                                      &nbsp;{{ $pl->partner_company->company_name }}
                                    </option>
                                @endforeach
                              @endforeach
                          </select>
                          <p id="form-error-partner" class="form-error hidden"></p>
                        </div>

                    </div>

                </div>
                <div class="clear"></div>
            </div>

            @if(isset($data))
            <div class="col-lg-10 col-xs-12" style="padding-left: 100px">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title title-header">New Partners</h3>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="partner-count-graph"></div>
                    </div>

                </div>
            </div>

         
            <div class="row" style="padding-left: 100px">
                <div class="col-md-12">
                    <div class="table-container">
                        <table class="table-striped table-bordered" style="width: 1050px;font-size: 12px">
                            <thead class="text-center">
                            <tr>
                                <th>Product</th>
                                <th>Selected Date Sales</th>
                                <th>Overall Sales</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($data['productSales'] as $sales)
                                    <tr>
                                        <td style="text-align: left"><b><a href="javascript:void(0);" onclick="updateGraph({{$sales->id}})">{{$sales->name}}</a></b></td>
                                        <td style="text-align: right">{{$sales->totalFiltered}}</td>
                                        <td style="text-align: right">{{$sales->total}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
           


             <div class="row" style="padding-left: 100px;display: none;" id="productGraphRow" >
                    <div class="col-md-10">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title title-header">Product Sales</h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div id="productSalesGraph"></div>
                            </div>

                        </div>
                    </div>
            </div>

            @endif
            <input type="hidden" value="{{$init or 0}}" id="init">
            <input type="hidden" id="prodId">
            <input type="hidden" id="exportUrl">

        </section>
    </div>



    <div class="modal fade" id="np-modal" role="dialog">
        <div class="modal-dialog" role="document" style="max-width: 1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Partners</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
          
                    @if(isset($report_export) && $report_export)
                    <div class="col-md-2">
                        <label>&nbsp;</label>  
                        <button class="btn btn-flat btn-success btn-block exportTable" >Export</button>
                    </div>
                    @endif
                    <div class="row" id="leadProspectRow" style="display: none;">
                      <div class="col-md-12">
                          <div class="table-container">
                              <table class="table-striped table-bordered datatable" style="width: 1200px;margin:0 auto; font-size: 12px" id="leadProspectTable">
                                  <thead class="text-center">
                                  <tr>
                                      <th>Source</th>
                                      <th>ID</th>
                                      <th>Company Name</th>
                                      <th>Contact</th>
                                      <th>Business Phone #</th>
                                      <th>Mobile #</th>
                                      <th>Business Address</th>
                                      <th>Date Created</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                    </div>
                    <div class="row" id="merchantRow" style="display: none;">
                    <div class="col-md-12">
                        <div class="table-container">
                            <table class="table-striped table-bordered datatable" style="width: 1200px;margin:0 auto; font-size: 12px" id="merchantTable">
                                <thead class="text-center">
                                <tr>
                                    <th>Status</th>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Contact</th>
                                    <th>Business Phone #</th>
                                    <th>Mobile #</th>
                                    <th>Business Address</th>
                                    <th>Date Created</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section("script")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/reports/product_report.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/reports/dates.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/reports/partner_report.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/reports/partner_report_charts.js" . "?v=" . config("app.version") }}"></script>
    <script>
        $('.datatable').dataTable();

        function displayTable(name){
          if(name == 'Leads'){
            showLeadCount();
          }
          if(name == 'Prospect'){
            showProspectCount();
          }
          if(name == 'Merchant Boarding'){
            showBoardingCount();
          }
          if(name == 'Merchant Boarded'){
            showBoardedCount();
          }
          if(name == 'Merchant Cancelled'){
            showCancelledCount();
          }
          if(name == 'Merchant Terminated'){
            showTerminatedCount();
          }
          $('#np-modal').modal('show');
        }

        function displayTableTotal(name){
          if(name == 'Leads'){
            showLeadTotal();
          }
          if(name == 'Prospect'){
            showProspectTotal();
          }
          if(name == 'Merchant Boarding'){
            showBoardingTotal();
          }
          if(name == 'Merchant Boarded'){
            showBoardedTotal();
          }
          if(name == 'Merchant Cancelled'){
            showCancelledTotal();
          }
          if(name == 'Merchant Terminated'){
            showTerminatedTotal();
          }
          $('#np-modal').modal('show');
        }


        function showLeadTotal(){
            $('#leadProspectRow').show();
            $('#merchantRow').hide();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/leads/A','leadProspectTable',1);
        }


        function showLeadCount(){
            $('#leadProspectRow').show();
            $('#merchantRow').hide();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/leads/A','leadProspectTable',0);
        }

        function showProspectTotal(){
            $('#leadProspectRow').show();
            $('#merchantRow').hide();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/prospects/A','leadProspectTable',1);
        }

        function showProspectCount(){
            $('#leadProspectRow').show();
            $('#merchantRow').hide();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/prospects/A','leadProspectTable',0);
        }

        function showBoardedTotal(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/A','merchantTable',1);
        }

        function showBoardedCount(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/A','merchantTable',0);
        }

        function showBoardingTotal(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/P','merchantTable',1);
        }

        function showBoardingCount(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/P','merchantTable',0);
        }

        function showCancelledTotal(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/C','merchantTable',1);
        }

        function showCancelledCount(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/C','merchantTable',0);
        }

        function showTerminatedTotal(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/T','merchantTable',1);
        }

        function showTerminatedCount(){
            $('#leadProspectRow').hide();
            $('#merchantRow').show();
            getReportData('/billing/report_new_partner_data/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/merchants/T','merchantTable',0);
        }

        $('.report-control').change(function () {
            if($('#init').val() == 1){
                generateReport('/billing/report_new_partner/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/false'); 
            }
            $('#init').val(1);
        });

        $('.exportTable').click(function () {
            exportReportData();
        });

        function updateGraph(id){
            $('#prodId').val(id);
            $('#productGraphRow').show();
            $('#change-id').trigger('click');
        }
    </script>
@endsection