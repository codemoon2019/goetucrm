@extends('layouts.app')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<input type="hidden" name="partner_id" id="partner_id" value="{{ $partner_id }}">

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Prospect
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/prospects">Prospects</a></li>
                <li class="active">Interested Products</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                @if(!isset($partner_info) || count($partner_info) <= 0)
                <input type="hidden" id="parent_id" name="parent_id" value=""/>
                <input type="hidden" id="company_name" name="company_name" value=""/>
                <h3>Prospect Company</h3>
                <a href="{{ url("prospects/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Prospects</a>
                <div class="crearfix"></div>
                <small class="small-details">
                    Prospect ID <br/>
                    Business Address <br/>
                    Contact Phone <br/>
                    Email Address
                </small>
                @else
                <input type="hidden" id="parent_id" name="parent_id" value="{{ $partner_info[0]->parent_id }}"/>
                <input type="hidden" id="company_name" name="company_name" value="{{ $partner_info[0]->company_name }}"/>
                <h3>{{ $partner_info[0]->company_name }}</h3>
                <a href="{{ url("prospects/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Prospects</a>
                <div class="crearfix"></div>
                <small class="small-details">
                    {{ $partner_info[0]->partner_id_reference }} <br/>
                    {{ $partner_info[0]->address1 }}, {{ $partner_info[0]->city}} {{ $partner_info[0]->state }}, {{ $partner_info[0]->zip }}, {{ $partner_info[0]->country_name }} <br/>
                    {{ $calling_code }} {{ $partner_info[0]->phone1 }} <br/>
                    {{ $partner_info[0]->email }}
                </small>
                @endif
            </div>
            <div class="nav-tabs-custom">
                <ul class="tabs-rectangular">
                    <li><a href="{{ url('prospects/details/summary/'.$partner_id) }}">Summary</a></li>
                    @if($isInternal)
                    <li><a href="{{ url('prospects/details/profile/'.$partner_id) }}">Profile</a></li>
                    <li><a href="{{ url('prospects/details/contact/'.$partner_id) }}">Contact</a></li>
                    <li class="active"><a href="{{ url('prospects/details/interested/'.$partner_id) }}">Interested Products</a></li>
                    <li><a href="{{ url('prospects/details/applications/'.$partner_id) }}">Applications</a></li>
                    <li><a href="{{ url('prospects/details/appointment/'.$partner_id) }}">Appointment</a></li>
                    @endif
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>
                <div class="tab-content">
                    @if($isInternal)
                    <!-- <a href="#" class="btn btn-flat btn-github mb-plus-20">Add Product</a> -->
                     <button class="btn btn-primary" id="btnLoadInterestedProduct" name="btnLoadInterestedProduct">Add Product</button></br></br>
                    <table class="table datatable table-striped table-condense">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Product Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($interested_products))
                        @foreach($interested_products as $ip)
                            <tr id="ip_{{$ip->id}}">
                                <td>{{$ip->id}}</td>
                                <td>{{$ip->name}}</td>
                                <td>{{$ip->description}}</td>
                                <td><button type="button" class="btn btn-danger btn-sm" id="deleteProduct" onclick="deleteProduct({{ $ip->id }})">Remove</button></td>
                            </tr>
                        @endforeach
                        @endif
                            <!-- <tr>
                                <td>1</td>
                                <td>Go3 Gift and Rewards</td>
                                <td>Go2 Gift and Rewards</td>
                                <td>Edit | Remove</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ez2Eat</td>
                                <td>Online Ordering</td>
                                <td>Edit | Remove</td>
                            </tr> -->
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <div id="modalInterestedProductSelection" class="modal" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">SELECT PRODUCT</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
          </div>
          <form role="form" name="frmAddInterestedProducts" id="frmAddInterestedProductsProspects"> 
              <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <table id="tblListInterestedProduct" name="tblListInterestedProduct"  class="datatable table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Description</th>
                                                <!-- <th><input type="checkbox" id="allcb" name="allcb" /></th> -->
                                                <th></th>
                                            </tr>
                                        </thead>
                                          <tbody id="loadedProducts">
                                                @foreach($products as $p)
                                                    <tr>
                                                        <td>{{$p->name}}</td>
                                                        <td>{{$p->description}}</td>
                                                        <td><input class="btn btn-danger" type="checkbox" name="add_products[]" value="{{$p->id}}"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>                            
                                
                            </div>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                @if(!empty($products))<button type="button" id="btnAddInterestedProduct" name="btnAddInterestedProduct" class="btn btn-primary">Add Product</button>@endif
                <button type="button" id="btnCancelProductTag" name="btnCancelProductTag" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
          </form>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
</div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/jquery.maskedinput.js" . "?v=" . config("app.version") }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/prospects/list.js" . "?v=" . config("app.version") }}"></script>
@endsection
