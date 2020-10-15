@extends('layouts.app')

@section('content')
                @php 
                    $access = session('all_user_access'); 
                    $canEdit = false;
                    if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                        if(strpos($access[strtolower($partner_info->partner_type_description)], 'payment') !== false){ 
                            $canEdit = true;
                        } 
                    } 
                @endphp

                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                @include("partners.details.profile.profiletabs")
                <div class="tab-content no-padding">
                     <div class="tab-pane active" id="payment-gateway">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Payment Gateway</h3>
                                @if($canEdit)
                                <div class="mini-drp-input pull-right mt-minus-40">
                                    <select class="form-control">
                                        <option>Create New</option>
                                    </select>
                                    <button type="button" onclick="createPaymentGateway();" class="btn btn-primary">GO</button>
                                </div>
                                @endif
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                    <th>Name</th>
                                    <th>Key</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach($payment_gateways as $payment_gateway)
                                    <tr>
                                    <td>{{$payment_gateway->name}}</td>
                                    <td>{{$payment_gateway->key}}</td>
                                    <td>@if($canEdit)<button type="button" onclick="editPaymentGateway({{$payment_gateway->id}});" class="btn btn-default btn-sm">Edit</button>@endif</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
        <div class="modal fade" id="editPaymentGateway" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Gateway</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <form id="frmPaymentGateway" name="frmPaymentGateway"  method="post" enctype="multipart/form-data" action="/partners/updatepaymentgateway/{{$id}}">
                        {{ csrf_field() }}
                    <input type="hidden" class="form-control" id="pgID" name="pgID">    
                    <div class="row">
                        <div class="col-sm-5 sm-col">
                            <div class="form-group">
                                <label>Name: <span class="required">*</span></label>
                                <input type="text" class="form-control" id="txtPGName" name="txtPGName" placeholder="Enter Name" maxlength="50">
                            </div>
                        </div>
                        <div class="col-sm-5 sm-col">
                            <div class="form-group">
                                <label>Key: <span class="required">*</span></label>
                                <input type="text" class="form-control" id="txtPGKey" name="txtPGKey" placeholder="Enter Key" maxlength="50">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-save" >Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
@endsection
