@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                New Business Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">New Business Report</li>
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
                            <input type="text" class="report-control form-control custom-input-8 weeklyDate"  @if(isset($type) && $type == "Weekly") value="{{$from}}" @else value="{{date_format((new \DateTime())->modify('-7 days'),"Y-m-d") }}" @endif id="txtWeeklyDateNewBusiness" name="txtWeeklyDateNewBusiness">
                        </div>

                        <div class="col-md-2 monthlyDiv dateDiv">
                            <label> Month: </label> 
                            <input type="text" class="report-control form-control custom-input-8 monthlyDate" @if(isset($type) && $type == "Monthly") value="{{$from}}" @else value="{{date_format(new DateTime(),"Y-m")}}" @endif id="txtMonthlyDate" name="txtMonthlyDate">
                        </div>

                        <div class="col-md-2 yearlyDiv dateDiv">
                            <label> Yearly: </label> 
                            <input type="text" class="report-control form-control custom-input-8 yearlyDate" @if(isset($type) && $type == "Yearly") value="{{$from}}" @else value="{{date_format(new DateTime(),"Y")}}" @endif id="txtYearlyDate" name="txtYearlyDate">
                        </div>

                        @if(isset($partners))
                            @if($report_export)
                            <div class="col-md-2">
                                <label>&nbsp;</label>  
                                <button class="btn btn-flat btn-success btn-block" id="exportReport">Export</button>
                            </div>
                            @endif
                        @endif


                    </div>
                </div>
                <div class="clear"></div>
            </div>


            @if(isset($partners))
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">New Business Report</h3>
                        </div>
                        <table class="table-striped table-bordered" style="width: 1200px;margin:0 auto;">
                            <thead class="text-center">
                            <tr>
                                <th>Merchant Name</th>
                                <th>Contact Person</th>
                                <th>Mobile #</th>
                                <th>Email Address</th>
                                <th>Status</th>
                                <th>Date Created</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($partners as $partner)
                                    <tr>
                                        <td style="text-align: left">{{$partner->partner_company->company_name}}</td>
                                        <td style="text-align: left">{{$partner->partner_contact()->first_name}} {{$partner->partner_contact()->middle_name}} {{$partner->partner_contact()->last_name}}</td>
                                        <td style="text-align: left">{{$partner->partner_contact()->country_code}}{{$partner->partner_contact()->mobile_number}}</td>
                                        <td style="text-align: left">{{$partner->partner_company->email}}</td>
                                        @if($partner->status == 'A')<td style="text-align: left;color:green">Active</td>@endif
                                        @if($partner->status == 'I')<td style="text-align: left;color:red">Inactive</td>@endif
                                        @if($partner->status == 'T')<td style="text-align: left;color:red">Terminated</td>@endif
                                        @if($partner->status == 'V')<td style="text-align: left;color:red">Cancelled</td>@endif
                                        @if($partner->status == 'C')<td style="text-align: left;color:blue">For Approval</td>@endif
                                        @if($partner->status == 'P')<td style="text-align: left;color:orange">For Boarding</td>@endif

                                        <td style="text-align: left">{{date_format($partner->created_at,"Y/m/d H:i:s")}}</td>
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
    <script src="{{ config("app.cdn") . "/js/reports/dates.js" . "?v=" . config("app.version") }}"></script>
    <script>
        $('.report-control').change(function () {
            if($('#init').val() == 1){
                generateReport('/billing/report_new_business/'+$('#txtDateType').val()+'/{$from}/{$to}/false'); 
            }
            $('#init').val(1);
        });

        $('#exportReport').click(function () {
            generateReport('/billing/report_new_business/'+$('#txtDateType').val()+'/{$from}/{$to}/true');
        });

    </script>
@endsection