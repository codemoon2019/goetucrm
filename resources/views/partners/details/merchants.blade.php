@extends('layouts.app')

@section('content')
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Merchants</h3>
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Mobile Phone</th>
                                    <th>Email</th>
                                    <th>Ordered Products</th>
                                </thead>
                                <tbody>
                                    @if(count($merchants) > 0)
                                        @foreach($merchants as $merchant)
                                            <tr>
                                            <td>{{$merchant->company_name}}</td>
                                            <td>{{$merchant->first_name}} {{$merchant->last_name}}</td>
                                            <td>+{{$merchant->country_code}} {{$merchant->mobile_number}}</td>
                                            <td>{{$merchant->email}}</td>
                                            <td>
                                                @if(count($merchants) > 0)
                                                    @foreach($merchant->products as $merchant_product)  
                                                        {{$merchant_product->name}} 
                                                    @endforeach
                                                @endif
                                            </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/list.js" . "?v=" . config("app.version") }}"></script>
@endsection