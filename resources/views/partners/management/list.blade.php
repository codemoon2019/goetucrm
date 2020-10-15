@extends('layouts.app')

@section('content')
    <body> 
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Partners
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active">Partners</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                <div class="row">
                    <div class="col-sm-8">
                        <h5>Select a partner to view their information ...</h5>
                    </div>
                    @if(App\Models\Access::hasPageAccess('company','add',true) ||
                    App\Models\Access::hasPageAccess('iso','add',true) ||
                    App\Models\Access::hasPageAccess('sub iso','add',true) ||
                    App\Models\Access::hasPageAccess('agent','add',true) ||
                    App\Models\Access::hasPageAccess('sub agent','add',true)
                    )
                    <!-- <div class="col-sm-4">
                        <a href="{{ url("partners/create") }}" class="btn btn-primary pull-right">Create Partner</a>
                    </div> -->
                    <div class="col-md-12">
                        <button class="btn btn-primary pull-right" onclick="upload();">Upload Partner</button>
                        <button class="btn btn-primary pull-right" style="margin-right:5px" onclick="showLPFileFormatDialog();">Get Upload File Format</button>
                    </div>

                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <ul class="tabs-rectangular">
                    @if(count($partner_types)>0)
                        @php $i=1 @endphp
                        @foreach($partner_types as $partner_type)
                            @if(session('user_type_desc')!=$partner_type->name)
                            <li class="{{($partner_type->name == $active_partner_tab) ? "active" : "" }}"><a href="#" id="{{$partner_type->id}}" data-toggle="tabs">@if(session('user_type_desc')=='AGENT' && $partner_type->name == 'SUB AGENT') My Partner @else {{$partner_type->display_name }} @endif</a></li>
                            @php $i+=1 @endphp
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
            @if(count($partner_details)>0)
                @foreach($partner_details as $partner_detail)
                    @if(session('user_type_desc')!=$partner_detail['name'])
                    <div id="{{$partner_detail['id']}}Container" class="{{($partner_detail['name'] == $active_partner_tab)? "" : "hide" }}">
                        <div class="col-md-12 mb-plus-20">
                            <div class="row">
                                {{--<div class="col-md-6">--}}
                                    {{--<input type="text" class="form-control search-sys-usr" placeholder="Search Company...">--}}
                                    {{--<button class="btn btn-primary system-usr-srch-btn">Search</button>--}}
                                {{--</div>--}}

                                <div class="col-md-8">
                                    <a href="#" class="dropdown-toggle btn btn-info" data-toggle="dropdown" aria-expanded="false">Show / Hide Columns <span class="caret"></span></a>
                                    <ul class="dropdown-menu user-dept tbl{{$partner_detail['id']}}" role="menu">
<!--                                         @if($partner_detail['name'] != 'COMPANY')
                                            <li class="hide">
                                                <input type="checkbox" name="toggle-cols" id="toggle-col-0" class="toggle-vis" data-column="0" checked="checked">
                                                <label for="toggle-col-0" class="dept-name">Partners</label>
                                            </li>
                                        @endif
                                        <li class="hide">
                                            <input type="checkbox" name="toggle-cols" id="@if($partner_detail['name'] != 'COMPANY') toggle-col-1 @else toggle-col-0 @endif" class="toggle-vis" data-column="@if($partner_detail['name'] != 'COMPANY') 1 @else 0 @endif" checked="checked">
                                            <label for="toggle-col-1" class="dept-name">Company Name</label>
                                        </li>
                                        <li class="hide">
                                            <input type="checkbox" name="toggle-cols" id="@if($partner_detail['name'] != 'COMPANY') toggle-col-2 @else toggle-col-1 @endif" class="toggle-vis" data-column="@if($partner_detail['name'] != 'COMPANY') 2 @else 1 @endif" checked="checked">
                                            <label for="toggle-col-2" class="dept-name">Contact Person</label>
                                        </li> -->
                                        <li>
                                            <input type="checkbox" name="toggle-cols" id="toggle-col-3" class="toggle-vis" data-column="2" checked="checked">
                                            <label for="toggle-col-3" class="dept-name">Mobile Phone</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="toggle-cols" id="toggle-col-4" class="toggle-vis" data-column="3" checked="checked"> 
                                            <label for="toggle-col-4" class="dept-name">Email</label>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="toggle-cols" id="toggle-col-5" class="toggle-vis" data-column="4" checked="checked">
                                            <label for="toggle-col-5" class="dept-name">State</label>
                                        </li>
<!--                                         <li class="hide">
                                            <input type="checkbox" name="toggle-cols" id="@if($partner_detail['name'] != 'COMPANY') toggle-col-6 @else toggle-col-5 @endif" class="toggle-vis" data-column="@if($partner_detail['name'] != 'COMPANY') 6 @else 5 @endif" checked="checked">
                                            <label for="toggle-col-6" class="dept-name">Option</label>
                                        </li> -->
                                    </ul>
                                    <a href="/partners/management-treeview" class="btn btn-success">Tree View</a>
                                </div>
                                
                                <!-- <div class="col-md-4"> -->
                                <!-- </div> -->

                                <div class="col-md-4">
                                    <a href="#" class="btn btn-default pull-right adv-search-btn">Advance Search</a>
                                </div>
                            </div>
                        </div>
                        <div class="no-padding">
                            <table id="tbl{{$partner_detail['id']}}" class="table responsive datatables table-condense p-0">
                                <thead>
                                <tr>
                                    @if($partner_detail['name']!=="COMPANY" && $is_admin)
                                    <th>Partners</th>
                                    @endif
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Mobile Phone</th>
                                    <th>Email</th>
                                    <th>State</th>
                                    <!-- <th>Option</th> -->
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    @endif
                 @endforeach
            @endif
            @include('incs.advanceSearch')
        </section>
    </div>
    </body>

        <div id="modalUploadCSV" class="modal" role="dialog">
            <form role="form" name="frmUploadCSV" id="frmUploadCSV" method="post" enctype="multipart/form-data" files="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">GoETU Partners Upload</h4>
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
                            <a href="{{ "/uploadfiles/partnerfilespecs.pdf" . "?v=" . config("app.version") }}" target="_blank" style="text-align:center" class="fa fa-file-pdf-o fa-5x" title="Download File Specification"></a>
                        </div>
                        <div class="col-md-4"><label>Upload File Specifications and Guidelines</label> </div>
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/partnerfiletemplate.csv" . "?v=" . config("app.version") }}"  style="text-align:center" class="fa fa-file-excel-o fa-5x" title="Download Upload File Template"></a> 
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
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/partners/list.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
    <script>
        load_partners();
    </script><script>
        let hiddenCols = [];
        $("input.toggle-vis").on('click', function () {
            let curActive = $('.tabs-rectangular li a').parents('.tabs-rectangular');
            let curActiveId = curActive.find('li.active a').attr('id');
            let table = $('#tbl'+ curActiveId).DataTable();
            let column = table.column($(this).attr('data-column'));
            let colNum = $(this).attr('data-column');

            column.visible(!column.visible());
            $(this).attr("checked", !$(this).attr("checked"));

            if (!hiddenCols.includes(colNum)) {
                hiddenCols.push(colNum);
            } else {
                let index = hiddenCols.indexOf(colNum);
                if (index > -1) {
                    hiddenCols.splice(index, 1);
                }
            }
        });

        function redrawTable(tbl) {
            let table = $(tbl).DataTable();
            table.columns(hiddenCols).visible(false, false);
            table.columns.adjust().draw(false); // adjust column sizing and redraw
        }
    </script>
@endsection