@extends('layouts.app')

@section('content')

                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Tickets</h3>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control"  id="txtTicketFilter"  name="txtTicketFilter" >
                                        @if(count($ticket_filters)>0)
                                            @foreach($ticket_filters as $ticket_filter)
                                            <option value="{{$ticket_filter->code}}">{{$ticket_filter->description}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="txtPartnerID" name="txtPartnerID" value="{{$id}}">
                            <table id="tblTicketList" class="table datatables table-striped">
                                <thead>
                                    <th>Title</th>
                                    <th>From</th>
                                    <th>Merchant</th>
                                    <th>Product</th>
                                    <th>Department</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                </thead>
                                <tbody>
                                    
                                </tbody>
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
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
@endsection