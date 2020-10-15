<div class="col-md-12" style="margin-top:20px;">
    <table id="merchantList" class="table responsive table-striped table-bordered">
        <thead>
        <th class="sorting_asc" tabindex="0" aria-sort="ascending">
            Company
        </th>
        </thead>
        <tbody>
        @foreach($ownerData['companies'] as $c)
            <tr>
                <td>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="box box-info" style="margin:0 !important;">
                                <div class="box-header with-border">
                                    <h3 class="box-title title-header">
                                        <div class="pull-left image">
                                            <img src="{{ URL::to('/'.$c->partner_company->logo_path) }}"
                                                 onerror="this.src='{{ URL::to('/images/logo.png') }}'"
                                                 alt="{{ URL::to('/images/logo.png') }}" width="40"
                                                 height="32">
                                        </div>
                                        {{$c->partner_company->company_name}}
                                    </h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool"
                                                data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-box-tool"
                                                data-widget="remove"><i
                                                    class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="box box-widget widget-user-2">
                                                <div class="box-footer no-padding">
                                                    <ul class="nav nav-stacked" style="display:inline">
                                                        <li style="padding: 2px;">
                                                            <span>Address: {{$c->partner_company->address1}} {{$c->partner_company->city}}</span>
                                                        </li>
                                                        <li style="padding: 2px;">
                                                            <span>Email: {{$c->partner_company->email}}</span>
                                                        </li>
                                                        <li style="padding: 2px;">
                                                            <span>Phone: {{$c->partner_company->country_code}}{{$c->partner_company->phone1}}</span>
                                                        </li>
                                                        <li style="padding: 2px;">
                                                            <span>Mobile: {{$c->partner_company->mobile}}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box"
                                                 style="min-height: 120px !important;">
                                        <span class="info-box-icon bg-red"
                                              style="min-height: 120px !important;"><i
                                                    class="fa fa-shopping-cart"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">Sales</span>
                                                    <span class="info-box-number">$ {{number_format($c->company_sales_increase($c->partner_company->id),2,".",",")}}</span>

                                                    <div class="progress">
                                                        <div class="progress-bar"
                                                             style="width: {{$c->percentage_sales_width($c->partner_company->id)}}%; background-color: red;"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                {{number_format($c->percentage_sales($c->partner_company->id),2,".",",")}}% Increase in 60 Days
                                            </span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                        <span class="info-box-icon bg-aqua"
                                              style="height: 120px !important"><i
                                                    class="fa fa-envelope-o"></i></span>
                                                <div class="info-box-content">
                                                    <ul>
                                                        <li>
                                                            <a href="{{ url('/tickets/adminInternal?companyCode='.$c->id.'&filterCode=') }}">All
                                                                <span
                                                                        class="pull-right badge bg-blue">{{ $c->ticketHeaders()->whereStatus(null)->count() }}</span></a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('/tickets/adminInternal?companyCode='.$c->id.'&filterCode=AN') }}">New
                                                                <span
                                                                        class="pull-right badge bg-aqua">{{ $c->ticketHeaders()->whereStatus('N')->count() }}</span></a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('/tickets/adminInternal?companyCode='.$c->id.'&filterCode=AI') }}">In
                                                                Progress <span
                                                                        class="pull-right badge bg-green">{{ $c->ticketHeaders()->whereStatus('I')->count() }}</span></a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('/tickets/adminInternal?companyCode='.$c->id.'&filterCode=AP') }}">Pending
                                                                <span
                                                                        class="pull-right badge bg-red">{{ $c->ticketHeaders()->whereStatus('P')->count() }}</span></a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('/tickets/adminInternal?companyCode='.$c->id.'&filterCode=AS') }}">Solved
                                                                <span
                                                                        class="pull-right badge bg-orange">{{ $c->ticketHeaders()->whereStatus('P')->count() }}</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer no-padding">
                                    <div class="view_more" id="{{$c->id}}" data-companyId = "{{$c->id}}">


                                    </div>

                                </div>
                                <!-- box footer -->
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @include('incs.advanceSearch')
</div>