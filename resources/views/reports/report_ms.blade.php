@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Monthly Sales Report
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/billing/report">Reports </a></li>
                <li class="active">Monthly Sales Report</li>
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
                            <label> Month: </label>
                            <select class="form-control report-control" name="month" id="month">
                                <option value="1" @if($month == 1) selected @endif>January</option>
                                <option value="2" @if($month == 2) selected @endif>February</option>
                                <option value="3" @if($month == 3) selected @endif>March</option>
                                <option value="4" @if($month == 4) selected @endif>April</option>
                                <option value="5" @if($month == 5) selected @endif>May</option>
                                <option value="6" @if($month == 6) selected @endif>June</option>
                                <option value="7" @if($month == 7) selected @endif>July</option>
                                <option value="8" @if($month == 8) selected @endif>August</option>
                                <option value="9" @if($month == 9) selected @endif>September</option>
                                <option value="10" @if($month == 10) selected @endif>October</option>
                                <option value="11" @if($month == 11) selected @endif>November</option>
                                <option value="12" @if($month == 12) selected @endif>December</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label> Year: </label>
                            <input name="year" id="year" value="{{$year}}" class="form-control report-control" type="text">
                        </div>

                    </div>
                </div>
                <div class="clear"></div>
                @if(isset($invoices))
                <div class="col-md-12">
                    <div class="table-container">
                        <div class="row-header text-center" style="border-bottom: none;">
                            <h3 class="title">Sales Report for {{$monthName}} {{$year}}</h3>
                        </div>
                        <table class="table-striped table-bordered sales-report">
                            <thead class="text-center">
                            <tr>
                                <th>Case #</th>
                                <th>Total Amount</th>
                                <th>Received Date</th>
                                <th>CID</th>
                                <th>Product</th>
                                <th>Date</th>
                                <th>DBA</th>
                                <th>Agent</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Note</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1 ?>
                            @foreach($invoices as $inv)
                            <tr>
                                <td></td>
                                <td style="text-align: right">$ {{$inv->total_due}}</td>
                                <td style="text-align: right">@if($inv->status == 'P'){{\Carbon\Carbon::parse($inv->updated_at)->format('m/d/Y')}}@endif</td>
                                <td style="text-align: center">{{$inv->partner->credit_card_reference_id}}</td>
                                <td style="text-align: left">{{$inv->reference}}</td>
                                <td style="text-align: right">{{\Carbon\Carbon::parse($inv->invoice_date)->format('m/d/Y')}}</td>
                                <td style="text-align: center">{{$inv->partner->partner_company->dba}}</td>
                                <td style="text-align: center">{{$inv->partner->partner_company->company_name}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{$inv->remarks}}</td>
                            </tr>
                                @foreach($inv->details as $detail)
                                <tr>
                                    <td style="text-align: right">{{$i++}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: left"> {{$detail->product->name}}</td>
                                    <td style="text-align: right"></td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: right">{{$detail->quantity}}</td>
                                    <td style="text-align: right">$ {{$detail->amount}}</td>
                                    <td style="text-align: center">{{isset($detail->product->payment_type->name) ? $detail->product->payment_type->name : "Amount"}}</td>
                                    <td></td>
                                </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-grey">
                                    <td colspan="13"></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Amount</td>
                                    <td style="text-align: right">$ {{number_format((float)$amount, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Setup Fees</td>
                                    <td style="text-align: right">$ {{number_format((float)$setupFee, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Monthly Fees</td>
                                    <td style="text-align: right">$ {{number_format((float)$monthlyFee, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Yearly Fees</td>
                                    <td style="text-align: right">$ {{number_format((float)$yearlyFee, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Prepaid</td>
                                    <td style="text-align: right">$ {{number_format((float)$prePaid, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>

                                <tr>
                                    <td colspan="9" class="text-right">Current</td>
                                    <td class="bg-yellow" style="text-align: right">$ {{number_format((float)$current, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Total Received</td>
                                    <td class="bg-green" style="text-align: right">$ {{number_format((float)$received, 2, '.', '')}}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <div class="col-md-12 mt-plus-20">
                <div class="row-header text-center" style="border-bottom: none;">
                    <h3 class="title">Agent Summary for {{$monthName}} {{$year}}</h3>
                </div>
                <table class="table table-striped table-bordered">
                    <thead class="text-center">
                    <tr>
                        <th></th>
                        <th>Agent</th>
                        <th>Product</th>
                        <th># of Cases</th>
                        <th>Total Cases</th>
                        <th>Total Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=1 ?>
                    @foreach($merchants as $m)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$m->partner_company->company_name}}</td>
                        <td>{!!$m->products!!}</td>
                        <td style="text-align: right">{!!$m->cases!!}</td>
                        <td style="text-align: right">{{$m->total_cases}}</td>
                        <td style="text-align: right">$ {{number_format((float)$m->total_amount, 2, '.', '')}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="text-right"><b>{{$totalCase}}</b></td>
                        <td class="text-right"><b>$ {{number_format((float)$totalAmount, 2, '.', '')}}</b></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    @foreach($totals as $t)
                    <tr><td colspan="4">Total {{$t->name}}</td><td class="text-right">{{$t->cases}}</td></tr>
                    @endforeach
                    <tr><td colspan="4">Total # of Cases</td><td class="text-right">{{$totalCase}}</td></tr>
                    </tfoot>
                </table>
            </div>
            @if($ms_export)
            <div class="col-md-2 mt-plus-20">
                <button class="btn btn-flat btn-success btn-block" id="exportReport">Export</button>
            </div>
            @endif
            @endif

            <input type="hidden" value="{{$init or 0}}" id="init">

            </div>
        </section>
    </div>
@endsection
@section("script")
    <script>
        var curYPos = 0;
        var curXPos = 0;
        var curDown = false;

        $('.table-container').on("mousemove", function (event) {
            if (curDown === true) {
                $('.table-container').scrollTop(parseInt($('.table-container').scrollTop() + (curYPos - event.pageY)));
                $('.table-container').scrollLeft(parseInt($('.table-container').scrollLeft() + (curXPos - event.pageX)));
            }
        });

        $('.table-container').on("mousedown", function (e) { curDown = true; curYPos = e.pageY; curXPos = e.pageX; e.preventDefault(); });
        $('.table-container').on("mouseup", function (e) { curDown = false; });

        // Stop dragging if mouse leaves the window (Not essential, can be removed without negative effects)
        $('.table-container').on("mouseout", function (e) { curDown = false; });

        $('#year').mask("9999");

        $('#generateReport').click(function () {
            if ($('#year').val().trim() == "") {
                alert("Year is required.");
                return false;
            }
            window.location = '/billing/report-ms-generate/'+$('#month').val()+'/'+$('#year').val();
        });
        $('#exportReport').click(function () {
            window.location = '/billing/report-ms-export/{{$month}}/{{$year}}';
        });

        $('.report-control').change(function () {
            if($('#init').val() == 1){
                window.location = '/billing/report-ms-generate/'+$('#month').val()+'/'+$('#year').val();
            }
            $('#init').val(1);
        });
        $('#month').trigger('change');

    </script>
@endsection