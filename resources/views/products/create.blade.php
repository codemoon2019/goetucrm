@extends('layouts.app')

@section('content')
<input type="hidden" name="txtProductCatID" id="txtProductCatID" value="" />
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Create Products
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
                <li><a href="/products">Products</a></li>
                <li class="active">Create Products</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section id="crtMainPrd" class="content container-fluid">
            <div class="col-md-12">
                <div class="new-prd-header">
                    <h3 class="new-prd-title">Main Product</h3>
                </div>
            </div>
                <input type="hidden" name="txtCustomFields" id="txtCustomFields" />
                <input type="hidden" name="txtSubProductFields" id="txtSubProductFields" />
                <input type="hidden" name="txtSumBuyRate" id="txtSumBuyRate" />
                <input type="hidden" name="txtProductID" id="txtProductID" value="-1" />

                <div class="col-md-12">
                    <div class="row">
                        <div class="offset-md-2 col-md-4">
                            <br />

                            @component('components.products.imageUpload', [
                                'name' => "fileImageUpload",
                                'imageSource' => "storage/products/display_pictures/default.jpg",
                            ]) 
                                Product Image
                            @endcomponent
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="offset-md-2 col-md-4">
                            <div class="form-group">
                                <label for="productName">Product Name</label>
                                <input class="form-control" name="txtProductName" id="txtProductName" value="" placeholder="Product Name" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="productType">Product Type</label>
                                <select name="txtProductType" id="txtProductType" class="form-control">
                                @if(!isset($product_type_info) || $product_type_info->count() < 1)
                                    <option value="-1">Goetu</option>
                                @else
                                    @foreach($product_type_info as $product_type_info)
                                        <option value="{{ $product_type_info->id }}" data-prodtype="{{ $product_type_info->description }}">{{ $product_type_info->description }}</option>
                                    @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-md-2 col-md-4">
                            <div class="form-group">
                                <label for="productDescription">Product Description</label>
                                <input class="form-control" name="txtProductDescription" id="txtProductDescription" value="" placeholder="Product Description" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Owner">Owner</label>
                                <select name="txtOwnerID" id="txtOwnerID" class="form-control">
                                @if(!isset($company_info) || $company_info->count() < 1)
                                    <option value="-1">No Company</option>
                                @else
                                    @foreach($company_info as $company_info)
                                        <option value="{{ $company_info->id }}" data-owner="{{ $company_info->company_name }}">{{ $company_info->company_name }}</option>
                                    @endforeach
                                @endif
                                </select>   
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="offset-md-2 col-md-4">
                            <div class="form-group" >
                                <label>Single Category per Transaction: </label>
                                <label class="switch switch-unpaid">
                                    <input type="checkbox" id="togSingleSelection">
                                    <div class="slider round">
                                        <span class="on">On</span><span class="off">Off</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="offset-md-2 col-md-4">
                            <p>*Creating sub product will be available after saving the main product</p>
                        </div>
                        <div class="col-md-4">
                            <a href="#" id="saveMainProduct" class="btn btn-primary btn-sm pull-right">Save Main Product</a>
                        </div>
                    </div>
                </div>
        </section>
        <section id="crtMainPrdCat" class="content container-fluid hide">
            <div class="col-md-12">
                <div class="new-prd-header">
                    <h3 class="new-prd-title">Main Product</h3>
                </div>
            </div>
            <div class="col-md-12">
                <div class="main-prd-details">
                    <h3 class="title" id="newProductName">New Sample Product</h3>
                    <a href="#" class="icon-img" data-toggle="modal" data-target="#editMainProduct"><img src="/images/pencil.png"/></a>
                    <a href="#" class="icon-img" data-toggle="modal" data-target="#template"><img src="/images/theme-ico.png"/></a>
                    <p id="newProductDescription">New Sample Product Description</p>
                    <div class="prd-details">
                        <label><strong>Product Type:</strong></label>
                        <p id="newProductType">Goetu</p>
                    </div>
                    <div class="prd-details">
                        <label><strong>Owner:</strong></label>
                        <p id="newProductOwner">Go3 Solutions Inc.</p>
                    </div>
                    <div class="prd-details">
                        <br/>
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#createProductCategory">Create Category</a>
                     </div>
                </div>
            </div>
        </section>
        <section id="crtMainPrdCatView" class="content container-fluid hide">
            <div class="content">
                <div class="box-group" id="accordionCategory">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <!-- <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title" id="categoryName"> Sample Category #1 </h4>
                            <div class="box-tools pull-right">
                                <a href="#" class="btn-circle btn-circle-plus" ><i class="fa fa-plus"></i></a>
                                <a href="#" class="btn-circle btn-circle-edit"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="btn-circle btn-circle-delete" ><i class="fa fa-trash"></i></a>
                                <a href="#collapseOne" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-arrow-down"></i></a>
                            </div>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Sample Category #2 </h4>
                            <div class="box-tools pull-right">
                                <a href="#" class="btn-circle btn-circle-plus" data-toggle="modal" data-target="#createSubProduct"><i class="fa fa-plus"></i></a>
                                <a href="#" class="btn-circle btn-circle-edit" data-toggle="modal" data-target="#editSubProduct"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="btn-circle btn-circle-delete" ><i class="fa fa-trash"></i></a>
                                <a href="#collapseTwo" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-arrow-down"></i></a>
                            </div>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse in show">
                            <div class="box-body">
                                <table class="table datatables table-condense table-striped">
                                    <thead>
                                        <td>Sub Product Name</td>
                                        <td>Description</td>
                                        <td>Cost</td>
                                        <td>Actions</td>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Sub Product 1</td>
                                            <td>Sub Product 1 Description</td>
                                            <td>$ 1.00</td>
                                            <td>Edit | Delete</td>
                                        </tr>
                                        <tr>
                                            <td>Sub Product 2</td>
                                            <td>Sub Product 2 Description</td>
                                            <td>$ 2.00</td>
                                            <td>Edit | Delete</td>
                                        </tr>
                                        <tr>
                                            <td>Sub Product 3</td>
                                            <td>Sub Product 3 Description</td>
                                            <td>$ 3.00</td>
                                            <td>Edit | Delete</td>
                                        </tr>
                                        <tr>
                                            <td>Sub Product 4</td>
                                            <td>Sub Product 4 Description</td>
                                            <td>$ 4.00</td>
                                            <td>Edit | Delete</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Sample Category #3 </h4>
                            <div class="box-tools pull-right">
                                <a href="#" class="btn-circle btn-circle-plus" ><i class="fa fa-plus"></i></a>
                                <a href="#" class="btn-circle btn-circle-edit"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="btn-circle btn-circle-delete" ><i class="fa fa-trash"></i></a>
                                <a href="#collapseThree" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-arrow-down"></i></a>
                            </div>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="editMainProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Main Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productName">Product Name:</label>
                        <input type="text" class="form-control" name="productName" id="productName" value="New Sample Product" placeholder="Enter Product Name"/>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Product Description:</label>
                        <input type="text" class="form-control" name="productDescription" id="productDescription" value="New Sample Product Description" placeholder="Enter Product Description"/>
                    </div>
                    <div class="form-group">
                        <label for="productType">Product Type:</label>
                        <select id="productType" name="productType" class="form-control">
                            <option>Goetu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productOwner">Owner</label>
                        <select id="productOwner" name="productOwner" class="form-control">
                            <option>Go3 Solutions Inc.</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="template" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title alert">Alert!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productName">Product Name:</label>
                        <input type="text" class="form-control" name="txtProductCatName" id="txtProductCatName" value="" placeholder="Enter Product Category Name"/>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Product Description:</label>
                        <input type="text" class="form-control" name="txtProductCatDescription" id="txtProductCatDescription" value="" placeholder="Enter Category Description"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <a href="#" id="saveCategory" class="btn btn-primary btn-save" data-dismiss="modal">Save Category</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createSubProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Sub Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="subProductName">Sub Product Name:</label>
                        <input type="text" class="form-control" name="txtSubProductName" id="txtSubProductName" value="" placeholder="Enter Sub Product"/>
                    </div>
                    <div class="form-group">
                        <label for="subProductDescription">Sub Product Description:</label>
                        <input type="text" class="form-control" name="txtSubProductDescription" id="txtSubProductDescription" value="" placeholder="Enter Product Description"/>
                    </div>
                    <div class="form-group">
                        <label for="subProductCost">Cost:</label>
                        <input type="text" class="form-control" name="txtSubProductCost" id="txtSubProductCost" value="" placeholder="Enter Cost"/>
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
                    <h5 class="modal-title">Create Product Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productName">Category Name:</label>
                        <input type="text" class="form-control" name="productName" id="productName" value="Sample Category #2" placeholder="Enter Product Category Name"/>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Category Description:</label>
                        <input type="text" class="form-control" name="productDescription" id="productDescription" value="Sample Category #2 Description" placeholder="Enter Category Description"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save">Save Category</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/products/create.js" . "?v=" . config("app.version") }}"></script>
@endsection