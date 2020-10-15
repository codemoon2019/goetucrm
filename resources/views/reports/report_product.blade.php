@extends('layouts.app')
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href=@cdn('/css/tickets/create.css') />
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Product Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">Product Report</li>
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
                            <input type="text" class="report-control form-control custom-input-8 weeklyDate"  @if(isset($type) && $type == "Weekly") value="{{$from}}" @else value="{{date_format((new \DateTime())->modify('-7 days'),"Y-m-d") }}" @endif id="txtWeeklyDateNewProduct" name="txtWeeklyDateNewProduct">
                        </div>

                        <div class="col-md-2 monthlyDiv dateDiv">
                            <label> Month: </label> 
                            <input type="text" class="report-control form-control custom-input-8 monthlyDate" @if(isset($type) && $type == "Monthly") value="{{$from}}" @else value="{{date_format(new DateTime(),"Y-m")}}" @endif id="txtMonthlyDate" name="txtMonthlyDate">
                        </div>

                        <div class="col-md-2 yearlyDiv dateDiv">
                            <label> Yearly: </label> 
                            <input type="text" class="report-control form-control custom-input-8 yearlyDate" @if(isset($type) && $type == "Yearly") value="{{$from}}" @else value="{{date_format(new DateTime(),"Y")}}" @endif id="txtYearlyDate" name="txtYearlyDate">
                        </div>

                        @if(isset($products))
                            @if($report_export)
                            <div class="col-md-2">
                                <label>&nbsp;</label>  
                                <button class="btn btn-flat btn-success btn-block" id="exportReport">Export</button>
                            </div>
                            @endif
                        @endif


                    </div>
                    <div class="row" style="padding-left: 100px">
                        <div class="col-md-4 form-group form-group-merchant" style="margin-top: 10px">
                          <label>Merchant</label>
                          <select class="js-example-basic-single form-control report-control" name="merchant" id="merchant" data-placeholder="Select Merchant" data-allow-clear="true">
                            <option value="-1" data-image="/images/agent.png" @if(isset($id) && $id == -1) selected @endif>&nbsp;&nbsp;All Merchants</option>
                              @foreach ($merchants->sortBy('partner_company.company_name') as $merchant)
                                <option value="{{ $merchant->id }}" data-image="/images/agent.png" @if(isset($id) && $id == $merchant->id) selected @endif>
                                  &nbsp;{{ $merchant->partner_company->company_name }}
                                </option>
                              @endforeach
                          </select>
                          <p id="form-error-merchant" class="form-error hidden"></p>
                        </div>

                    </div>
                </div>
                <div class="clear"></div>
            </div>


            @if(isset($products))
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">Product Report</h3>
                        </div>
                        <table class="table-striped table-bordered" style="width: 1200px;margin:0 auto;">
                            <thead class="text-center">
                            <tr>
                                <th>Product</th>
                                <th>Merchant Name</th>
                                <th>Product Detail</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $p)
                                    <tr>
                                        <td><b>{{$p->name}}</b></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align:right"><b>{{number_format($p->total,2,".",",")}}</b></td>
                                    </tr>
                                    @foreach($p->merchants as $m)
                                      <tr>
                                          <td></td>
                                          <td>{{$m->company_name}}</td>
                                          <td></td>
                                          <td style="text-align:right"><b>{{number_format($m->total,2,".",",")}}</b</td>
                                      </tr>              
                                      @foreach($m->details as $d)
                                          <tr>
                                              <td></td>
                                              <td></td>
                                              <td>{{$d->product}}</td>
                                              <td style="text-align:right">{{number_format($d->amount,2,".",",")}}</td>
                                          </tr>                                    
                                      @endforeach
                                    @endforeach
                                @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><b>GRAND TOTAL</b></td>
                                        <td style="text-align:right"><b>{{number_format($grandTotal,2,".",",")}}</b></td>
                                    </tr>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/reports/product_report.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/reports/dates.js" . "?v=" . config("app.version") }}"></script>
    <script>

        $('.report-control').change(function () {
          if($('#init').val() == 1){
            generateReport('/billing/report_product/'+$('#merchant').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/false');
          }
          $('#init').val(1);
        });

        $('#exportReport').click(function () {
            generateReport('/billing/report_product/'+$('#merchant').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/true');
        });



    </script>
@endsection