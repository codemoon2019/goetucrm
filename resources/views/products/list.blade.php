@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Products
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
                <li class="active">Products</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3>Available products</h3>
                
            </div>

            <div class="row">
                <div class="col-md-6">
                    @if ($isSuperAdmin)
                        <select name="sel-company" class="form-control">
                            <option value="-1">All Companies</option>
                            
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->partner_company->company_name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                @if ($canAdd)
                    <div class="col-md-6">
                        @if ($canAdd)
                            <a href="{{ url("products/create") }}" class="btn btn-primary pull-right">Create Product</a>
                            <button class="btn btn-primary pull-right" onclick="upload();" style="margin-right:5px">Upload Product</button>
                             <button class="btn btn-primary pull-right" style="margin-right:5px" onclick="showLPFileFormatDialog();">Get Upload File Format</button>
                        @endif
                    </div>
                @endif
            </div>

            <div class="clearfix"></div>

            <div class="col-md-6" hidden>
                <input type="text" class="form-control search-sys-usr" placeholder="Search Products...">
                <button class="btn btn-primary system-usr-srch-btn">Search</button>
            </div>

            <div class="col-md-12" style="margin-top:20px;">
                <table class="table datatables table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Product Type</th>
                            <th>Company Owner</th>
                            <th>Cost</th>

                            @if ($canView || $canEdit || $canDelete)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>

                </table>
            </div>
        </section>
    </div>

        <div id="modalUploadCSV" class="modal" role="dialog">
            <form role="form" name="frmUploadCSV" id="frmUploadCSV" method="post" enctype="multipart/form-data" files="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">GoETU Products Upload</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select CSV file:</label>
                                        <input type="file" id="fileUploadCSV" name="fileUploadCSV" accept=".csv"/>
                                    </div>

                                    <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadCSV">Clear Input</butto>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnUploadCSV" name="btnUploadCSV" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
            </form>
        </div>



        <div id="modalFileFormat" class="modal fade" role="dialog">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-title-message">Upload File Format</h4>
                    </div>
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/productfilespecs.pdf" . "?v=" . config("app.version") }}" target="_blank" style="text-align:center" class="fa fa-file-pdf-o fa-5x" title="Download File Specification"></a>
                        </div>
                        <div class="col-md-4"><label>Upload File Specifications and Guidelines</label> </div>
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/productfiletemplate.csv" . "?v=" . config("app.version") }}"  style="text-align:center" class="fa fa-file-excel-o fa-5x" title="Download Upload File Template"></a> 
                        </div>
                        <div class="col-md-4"><label>Upload File Template</label></div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    
                    </div>
                </div>
                </div>
            </div>

@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/products/list.js" . "?v=" . config("app.version") }}"></script>
@endsection