@extends('layouts.app')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
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
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <li class="active"><a href="#payment-method" data-toggle="tab" aria-expanded="false">Payment Method</a></li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="payment-method">
                        @if(count($payment_methods) > 0)
                            @foreach($payment_methods as $payment_method)
                                <div class="row mb-plus-20">
                                    <div class="row-header">
                                        <h3 class="title">{{$payment_method['name']}}</h3>
                                    </div>
                                    <table class="table datatables table-striped">
                                        <thead>
                                        <tr>
                                            {{-- <th>Type</th> --}}
                                            @foreach($payment_method['header'] as $header)
                                                <th>{{$header}}</th>
                                            @endforeach
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($payment_method['details'] as $details)
                                        <tr>
                                            {{-- <td>{{$payment_method['name']}}</td> --}}
                                            @foreach($payment_method['body'] as $body)
                                                <td>{{$details->$body}}</td>
                                            @endforeach
                                            <td>
                                                @if($canEdit)
                                                <input type="button" onclick="editPayment({{$details->id}})" class="btn btn-success" value="Edit"/>
                                                <a href="/partners/details/payment_method/{{$details->id}}/{{$id}}/cancel" class="btn btn-danger" onclick="return confirm('Delete this payment?')">Delete </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @endif
                        <div class="row">
                            <div class="form-group">
                                <!-- <a href="#" id="paymentDialog" name="paymentDialog" class="btn btn-primary" data-toggle="modal" data-target="#goetu-billing">Create New Payment</a> -->
                                @if($canEdit)
                                <button type="button" class="btn btn-primary" onclick="createPayment();">Create New Payment</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <div class="modal fade" id="goetu-billing" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">GoETU Billing</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form id="frmPaymentMethod" name="frmPaymentMethod"  method="post" enctype="multipart/form-data" action="/partners/updatepaymentmethod/{{$id}}">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" id="paymentMethodId" name="paymentMethodId" value="-1"> 
                        <div class="form-group">
                            <label>Select Payment Type</label>
                            <select class="form-control" id="txtPaymentType" name="txtPaymentType"/>
                                @if(count($payment_types)>0)
                                    @foreach($payment_types as $payment_type)
                                        <option value="{{$payment_type->id}}">{{$payment_type->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div id="divACH">
                            <div class="form-group">
                                <label>Bank Name:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtBankName" id="txtBankName" value="{{old('txtBankName')}}" maxlength="50"/>
                            </div>
                            <div class="form-group">
                                <label>Routing Number:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtRoutingNumber" id="txtRoutingNumber" value="{{old('txtRoutingNumber')}}" onkeypress="return isNumberKey(event)" maxlength="50"/>
                            </div>
                            <div class="form-group">
                                <label>Bank Account Number:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtBankAccountNumber" id="txtBankAccountNumber" value="{{old('txtBankAccountNumber')}}" onkeypress="return isNumberKey(event)" maxlength="50"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="chkSetAsDefault" name="chkSetAsDefault"/>
                            <label>Set as Default Payment</label>
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary btn-save" id="btnSavePaymentType" name="btnSavePaymentType" value="Create Payment">
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
@endsection
