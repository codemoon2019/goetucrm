@extends('layouts.app')
<style type="text/css">
    
    table, th, td {
        font-size: 12px
    }

</style>
@section('content')
<input type="hidden" name="txtProductCatID" id="txtProductCatID" value="{{ $product_info->id }}" />
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                @if(!$viewOnly) Edit @else View @endif Products
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success alert-notif hide">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger alert-notif hide">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/products">Products</a></li>
                <li class="active">@if(!$viewOnly) Edit @else View @endif Products</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section id="crtMainPrdCat" class="content container-fluid">
            <div class="col-md-12">
                <div class="new-prd-header">
                    <h3 class="new-prd-title">Main Product</h3>
                </div>
            </div>
            <div class="col-md-12">
                <div class="main-prd-details">
                    <img id="imgUploadUI" style="margin-top: 10px; border:1px solid black;" src='{{ url("storage/{$product_info->display_picture}") }}' height="150px" width="150px" alt="" class="pull-right"> 
                    
                    <h3 class="title" id="newProductName"><label><strong>Product Name:</strong></label> {{ $product_info->name }}</h3>
                    @if(!$viewOnly)
                    <a href="#" class="icon-img" data-toggle="modal" @if(!$viewOnly) onclick="editMainProduct();" @endif><img src="/images/pencil.png"/ title="Edit Product Name"></a>
                    <!-- <a href="#" class="icon-img" data-toggle="modal" data-target="#template"><img src="/images/theme-ico.png"/></a> -->
                    @endif
                    <p id="newProductDescription"><label><strong>Description: </strong></label> {{ $product_info->description }}</p>
                    <div class="prd-details">
                        <label><strong>Product Code:</strong></label>
                        <p id="prdcode">{{ $product_info->code }}</p>
                    </div>
                    <div class="prd-details">
                        <label><strong>Product Type:</strong></label>
                        @foreach($product_type_info as $product_type_info)
                            @if($product_info->product_type_id == $product_type_info->id)
                                <p id="newProductType"> {{ $product_type_info->description }} </p>
                            @endif
                        @endforeach
                    </div>
                    <div class="prd-details">
                        <label><strong>Owner:</strong></label>
                        @foreach($company_info as $company_info)
                            @if($product_info->company_id == $company_info->id)
                            <p id="newProductOwner">{{ $company_info->company_name }}</p>
                            @endif
                        @endforeach
                    </div>
                    <div class="prd-details">
                        <label><strong>Single Category per Transaction:</strong></label>
                            @if($product_info->single_selection == 1)
                            <p id="singleSelect">Yes</p>
                            @else
                            <p id="singleSelect">No</p>
                            @endif
                    </div>

                    @if(!$viewOnly)
                    <div class="prd-details">
                        <br/>
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#createProductCategory">Create Category</a>
                     </div>
                    @endif
                </div>
            </div>
        </section>
        <section id="crtMainPrdCatView" class="content container-fluid">
            <div class="content">
                <input type="hidden" id="viewmode" value="{{ $viewOnly }}" >
                <input type="hidden" id="picURL" value="{{  url("storage/") }}" >
                <div class="box-group" id="accordionCategory">





                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="editMainProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Main Product</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @component('components.products.imageUpload', [
                        'name' => "editImageUpload",
                        'imageSource' => "storage/{$product_info->display_picture}",
                    ]) 
                        Product Image
                    @endcomponent

                    <div class="form-group">
                        <label for="productName">Product Name:</label>
                        <input type="text" class="form-control" name="txtEditProductName" id="txtEditProductName" value="{{ $product_info->name }}" placeholder="Enter Product Name"/>
                        <input type="hidden" class="form-control" name="txtEditProductID" id="txtEditProductID" value="{{ $product_info->id }}"/>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Product Description:</label>
                        <input type="text" class="form-control" name="txtEditProductDescription" id="txtEditProductDescription" value="{{ $product_info->description }}" placeholder="Enter Product Description"/>
                    </div>
                    <div class="form-group">
                        <label for="productType">Product Type:</label>
                        <select id="txtEditProductType" name="txtEditProductType" class="form-control">
                        @if(!isset($product_type_infos) || $product_type_infos->count() < 1)
                            <option value="-1"  data-prodtype="Goetu">Goetu</option>
                        @else
                            @foreach($product_type_infos as $product_type_info)
                                <option value="{{ $product_type_info->id }}" data-prodtype="{{ $product_type_info->description }}" @if($product_info->product_type_id == $product_type_info->id) selected @endif>{{ $product_type_info->description }}</option>
                            @endforeach
                        @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productOwner">Owner</label>
                        <select id="txtEditOwnerID" name="txtEditOwnerID" class="form-control">
                            <!-- <option>Go3 Solutions Inc.</option> -->
                        @if(!isset($company_infos) || $company_infos->count() < 1)
                            <option value="-1" data-owner="No Company">No Company</option>
                        @else
                            @foreach($company_infos as $company_info)
                                <option value="{{ $company_info->id }}" data-owner="{{ $company_info->company_name }}" @if($company_info->id == $product_info->company_id) selected @endif>{{ $company_info->company_name }}</option>
                            @endforeach
                        @endif
                        </select>
                    </div>

                    <div class="form-group" >
                        <label>Single Category per Transaction: </label>
                        <label class="switch switch-unpaid">
                            <input type="checkbox" id="togSingleSelection" @if($product_info->single_selection) checked @endif>
                            <div class="slider round">
                                <span class="on">On</span><span class="off">Off</span>
                            </div>
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save" id="updateMainProduct">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="template" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title alert">Alert!</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="info-only">You need to create sub products before you can create a template</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createProductCategory" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Product Category</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productName">Category Name:</label>
                        <input type="text" class="form-control" name="txtProductCatName" id="txtProductCatName" value="" placeholder="Enter Product Category Name"/>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Category Description:</label>
                        <input type="text" class="form-control" name="txtProductCatDescription" id="txtProductCatDescription" value="" placeholder="Enter Category Description"/>
                    </div>
                    <div class="form-group" >
                        <label>Single Product per Transaction: </label>
                        <label class="switch switch-unpaid">
                            <input type="checkbox" id="togCategorySingleSelection">
                            <div class="slider round">
                                <span class="on">On</span><span class="off">Off</span>
                            </div>
                        </label>
                    </div>
                    <div class="form-group" >
                        <label>Mandatory: </label>
                        <label class="switch switch-unpaid">
                            <input type="checkbox" id="togCategoryMandatory">
                            <div class="slider round">
                                <span class="on">On</span><span class="off">Off</span>
                            </div>
                        </label>
                    </div>
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <a href="#" id="saveCategory" class="btn btn-primary btn-save" data-dismiss="modal">Save Category</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductCategory" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product Category</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productName">Category Name:</label>
                        <input type="text" class="form-control" name="txtEditProductCatName" id="txtEditProductCatName" value="" placeholder="Enter Product Category Name"/>
                        <input type="hidden" class="form-control" name="txtEditProductCatID" id="txtEditProductCatID" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Category Description:</label>
                        <input type="text" class="form-control" name="txtEditProductCatDescription" id="txtEditProductCatDescription" value="" placeholder="Enter Category Description"/>
                    </div>

                    <div class="form-group" >
                        <label>Single Product per Transaction: </label>
                        <label class="switch switch-unpaid">
                            <input type="checkbox" id="togCategoryEditSingleSelection">
                            <div class="slider round">
                                <span class="on">On</span><span class="off">Off</span>
                            </div>
                        </label>
                    </div>
                    <div class="form-group" >
                        <label>Mandatory: </label>
                        <label class="switch switch-unpaid">
                            <input type="checkbox" id="togCategoryEditMandatory">
                            <div class="slider round">
                                <span class="on">On</span><span class="off">Off</span>
                            </div>
                        </label>
                    </div>


                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" id="updateProductCategory" class="btn btn-primary btn-save">Save Category</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createSubProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Sub Product</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        @component('components.products.imageUpload', [
                            'name' => 'addSubProduct'
                        ]) 
                            Sub Product Image
                        @endcomponent
                    </div>

                    <div class="form-group">
                        <label for="subProductName">Sub Product Name:</label>
                        <input type="text" class="form-control" name="txtSubProductName" id="txtSubProductName" value="" placeholder="Enter Sub Product"/>
                    </div>
                    <div class="form-group">
                        <label for="subProductDescription">Sub Product Description:</label>
                        <input type="text" class="form-control" name="txtSubProductDescription" id="txtSubProductDescription" value="" placeholder="Enter Product Description"/>
                    </div>
                    <div class="form-group">
                        <label for="subItemID">Item ID:</label>
                        <input type="text" class="form-control" name="txtItemID" id="txtItemID" value="" placeholder="Enter Item ID"/>
                    </div>

                    <div class="form-group">
                        <label for="subProductCost">Cost:</label>
                        <input type="text" class="form-control" name="txtSubProductCost" id="txtSubProductCost" value="" placeholder="Enter Cost"  onkeypress="validate_numeric_input(event);" />
                    </div>
                    <div class="form-group">
                        <label for="subProductGroup">Product Group:</label>
                        <input type="text" class="form-control" name="txtSubProductGroup" id="txtSubProductGroup" value="" data-prodgroup="" readonly="" />
                    </div>
                    <div class="form-group">
                        <label for="txtSubProductType">Product Type:</label>
                        <select name="txtSubProductType" id="txtSubProductType" class="form-control">
                            <option value="" selected="selected"><--- SELECT ---></option>
                            <option value="SERVICE">SERVICE</option>
                            <option value="INVENTORY">INVENTORY</option>
                            <option value="NON-INVENTORY">NON-INVENTORY</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="txtPaymentType">Payment Type:</label>
                        <select name="txtPaymentType" id="txtPaymentType" class="form-control">
                            <option value="-1" selected="selected">None</option>
                            @foreach($payment_type as $p)
                            <option value="{{$p->id}}">{{$p->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" hidden>
                        <label for="txtSubProductIdentifier">Field Identifier:</label>
                        <input type="text" class="form-control" name="txtSubProductIdentifier" id="txtSubProductIdentifier" value="" placeholder="Enter Field Identifier"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save" id="saveSubProduct">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editSubProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sub Product</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @component('components.products.imageUpload', [
                        'name' => 'editSubProduct'
                    ]) 
                        Sub Product Image
                    @endcomponent

                    <div class="form-group">
                        <label for="subProductName">Sub Product Name:</label>
                        <input type="hidden" class="form-control" name="txtEditSubProductID" id="txtEditSubProductID" value=""/>
                        <input type="text" class="form-control" name="txtEditSubProductName" id="txtEditSubProductName" value="" placeholder="Enter Sub Product"/>
                    </div>
                    <div class="form-group">
                        <label for="subProductDescription">Sub Product Description:</label>
                        <input type="text" class="form-control" name="txtEditSubProductDescription" id="txtEditSubProductDescription" value="" placeholder="Enter Product Description"/>
                    </div>
                    <div class="form-group">
                        <label for="subItemID">Item ID:</label>
                        <input type="text" class="form-control" name="txtEditItemID" id="txtEditItemID" value="" placeholder="Enter Item ID"/>
                    </div>

                    <div class="form-group">
                        <label for="subProductCost">Cost:</label>
                        <input type="text" class="form-control" name="txtEditSubProductCost" id="txtEditSubProductCost" value="" placeholder="Enter Cost"  onkeypress="validate_numeric_input(event);" />
                    </div>
                    <div class="form-group">
                        <label for="txtEditOwnerID">Product Group:</label>
                        <!-- <input type="text" class="form-control" name="txtEditSubProductGroup" id="txtEditSubProductGroup" value="" data-prodgroup="" readonly="" /> -->
                        <select id="txtEditOwnerID2" name="txtEditOwnerID2" class="form-control">
                            <option value="-1"><--- SELECT ---></option>        
                            @if(isset($product_category_info))
                                @foreach($product_category_info as $product_category_info)
                                    <option value="{{ $product_category_info->id }}" data-owner="{{ $product_category_info->name }}">{{ $product_category_info->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="txtEditSubProductType">Product Type:</label>
                        <select name="txtEditSubProductType" id="txtEditSubProductType" class="form-control">
                            <option value="" selected="selected"><--- SELECT ---></option>
                            <option value="SERVICE">SERVICE</option>
                            <option value="INVENTORY">INVENTORY</option>
                            <option value="NON-INVENTORY">NON-INVENTORY</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="txtEditPaymentType">Payment Type:</label>
                        <select name="txtEditPaymentType" id="txtEditPaymentType" class="form-control">
                            <option value="-1" selected="selected">None</option>
                            @foreach($payment_type as $p)
                            <option value="{{$p->id}}">{{$p->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label for="txtSubProductIdentifier">Field Identifier:</label>
                        <input type="text" class="form-control" name="txtSubProductIdentifier" id="txtSubProductIdentifier" value="" placeholder="Enter Field Identifier"/>
                    </div>
                    <div class="form-group" hidden>
                        <label><input type="checkbox" id="chkHideProduct" name="chkHideProduct"> Hide from Application List</label>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save" id="updateSubProduct">Save</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="subProductModule" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="module-title">Sub Product Additional Modules</h5>
                    <button type="button" class="closenew" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="frmSubProductModule" name="frmSubProductModule"  method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <input type="hidden" id="subID" name="subID">
                    <input type="hidden" id="moduleCount" name="moduleCount" value="0">
                    <input type="hidden" id="moduleDetails" name="moduleDetails">

                  <div class="modal-body">
                    <table class="table  table-striped" id="product-modules-table">
                        <thead>
                            <tr>
                                <th width="30%">Name</th>
                                <th width="20%">Type</th>
                                <!-- <th width="20%">Format</th> -->
                                <th width="20%">Value</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <button type="button" id="btnAddModule" name="btnAddModule" class="btn btn-success">Add Module</button>
                  </div>

                  <div class="modal-footer">
                      
                      <button type="button" id="btnSaveModules" name="btnSaveModules" class="btn btn-primary btn-save" onclick="saveProductModule();">Save</button>
                      <button type="button" id="btnCancelPLoad" name="btnCancelPLoad" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                  </div>

            </div>
        </div>
    </div>    
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/products/edit.js" . "?v=" . config("app.version") }}"></script>

    <script type="text/javascript">
        
        function addSubProductModule(sub_product_id,name){
            $('#module-title').html(name + ' - Additional Modules');
            $('#subID').val(sub_product_id);
            $("#product-modules-table tbody tr").remove(); 
            $.getJSON('/products/get_sub_modules/' + sub_product_id, null, function (data) {
                var counter = 0;
                for (var i = 0; i < data['data'].length; i++) {
                    counter++;
                    var newTextBoxDiv = $(document.createElement('tr')).attr("id", 'lineNo' + counter);
                    newTextBoxDiv.after().html('<td class="linecount"><input class="form-control" style="height: 36px;" id="smName'+ counter +'" value=""></td>' +
                    '<td><select class="form-control smType" id="smType'+ counter +'" data-id="'+ counter +'"><option value="amount">Amount</option><option value="percentage">Percentage</option><option value="text">Text</option><option value="checkbox">Check Box</option></select></td>' +
                    '<td><input class="form-control" style="height: 36px;" id="smValue'+ counter +'" value=""><select class="form-control" style="height: 36px;display:none;" id="smChkValue'+ counter +'" ><option value="yes">Yes</option><option value="no">No</option></select></td>' +
                    '<td><a href="javascript:void(0)" class="btn-circle btn-circle-delete" onclick ="deleteLine('+ counter +')" id="deleteLine' + counter  + '"><i class="fa fa-minus"></i</a></td>');

                    newTextBoxDiv.appendTo("#product-modules-table");
                    $('#smName'+counter).val(data['data'][i]['name']);
                    $('#smType'+counter).val(data['data'][i]['type']);
                    if(data['data'][i]['type'] == "checkbox"){
                        $('#smChkValue'+counter).val(data['data'][i]['value']);
                        $('#smChkValue'+counter).show();
                        $('#smValue'+counter).hide();
                    }else{
                        $('#smValue'+counter).val(data['data'][i]['value']);
                        $('#smChkValue'+counter).hide();
                        $('#smValue'+counter).show();
                    }
                    
                    $('#moduleCount').val(counter);
                }
                $('#subProductModule').modal('show');
            });
            return false;
        }


        $(document).on('click', '#btnAddModule', function (e) {
            e.preventDefault();
            var counter = $('#moduleCount').val();
            counter++;
            var newTextBoxDiv = $(document.createElement('tr'))
                 .attr("id", 'lineNo' + counter);

            newTextBoxDiv.after().html('<td class="linecount"><input class="form-control" style="height: 36px;" id="smName'+ counter +'" value=""></td>' +
            '<td><select class="form-control smType" id="smType'+ counter +'" data-id="'+ counter +'"><option value="amount">Amount</option><option value="percentage">Percentage</option><option value="text">Text</option><option value="checkbox">Check Box</option></select></td>' +
            '<td><input class="form-control" style="height: 36px;" id="smValue'+ counter +'" value=""><select class="form-control" style="height: 36px;display:none;" id="smChkValue'+ counter +'" ><option value="yes">Yes</option><option value="no">No</option></select></td>' +
            '<td><a href="javascript:void(0)" class="btn-circle btn-circle-delete" onclick ="deleteLine('+ counter +')" id="deleteLine' + counter  + '"><i class="fa fa-minus"></i</a></td>');

            newTextBoxDiv.appendTo("#product-modules-table");

            $('#moduleCount').val(counter);
        });

        $(document).on('change', '.smType', function (e) {
            id = $(this).attr("data-id");
            if($(this).val() == "checkbox"){
                $('#smValue'+id).hide();
                $('#smChkValue'+id).show();
            }else{
                $('#smValue'+id).show();
                $('#smChkValue'+id).hide();                
            }
        });

        function deleteLine($id)
        {
            $("#lineNo" + $id).remove();
        }

        function saveProductModule()
        {
            var details = [];
            var name;
            var type;
            var format;
            var value;
            var hasError = false;
            var count = $('#moduleCount').val();

            for (i = 0; i <= count; i++) { 
              if($('#smName'+i).length > 0){
                name = $('#smName'+i).val();
                type = $('#smType'+i).val();
                value = $('#smValue'+i).val();
                if(type =="checkbox"){
                    value = $('#smChkValue'+i).val();
                }
                

                if(name == ''){
                    alert('Module Name is required');
                    return false;
                }
                if(value == ''){
                    alert('Value is required');
                    return false;
                }

                details.push({name: name, type: type, value: value});
              }
            }

            $('#moduleDetails').val(JSON.stringify(details));
            var postdata = $("#frmSubProductModule").serialize();
            $.postJSON("/products/update_sub_module/"+$('#subID').val(), postdata, function (data) {
                if (data.success) {

                }else{
                    alert(data.msg);
                }
            });
            $('#subProductModule').modal('hide');
        }


        jQuery.extend({
            postJSON: function postJSON(url, data, callback) {
                return jQuery.post(url, data, callback, "json");
            }
        });


        $(document).ready(function(){
            loadSubProducts();
        });

    </script>
@endsection