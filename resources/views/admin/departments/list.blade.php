@extends('layouts.app')

@if ($definedGroup)
    @section('content')
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    System Defined Group
                    <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                </h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item">System Defined Group</li>
                </ol>
                <div class="dotted-hr"></div>
            </section>

            <section class="content container-fluid">
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->                    
                    @include("admin.admintabs")
                    <div class="tab-content no-padding">
                        <div class="tab-pane active">
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div>
                                <h3>System Defined Group</h3>
                                @php $access = session('all_user_access'); 
                                    if (!isset($access['admin'])){
                                        //return redirect('/')->with('failed','You have no access to that page.')->send();
                                    }
                                @endphp
                            </div>
                            
                            <table id="system-group-table"  name="system-group-table"  class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="40%">Name</th>
                                        <th width="60%">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endsection

    @section("script")
        <script src="{{ config("app.cdn") . "/js/admin/departments.js" . "?v=" . config("app.version") }}"></script>
    @endsection

@else
    @section('content')
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Departments
                    <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                </h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item">Departments</li>
                </ol>
                <div class="dotted-hr"></div>
            </section>
            <section class="content container-fluid">

                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->                    
                    @include("admin.admintabs")
                    <div class="tab-content no-padding">
                        <div class="tab-pane active">
                            <div class="col-md-12" style="margin-bottom:10px; display:inline-block;">
                                @php $access = session('all_user_access'); 
                                    if (!isset($access['admin'])) {
                                        //return redirect('/')->with('failed','You have no access to that page.')->send();
                                    }
                                @endphp
                                <h5><b>{{$company}}</b></h5>
                                <span><input type="button" class="btn btn-success" id="toggleView" value="Table View"></span>
                                @php 
                                if(strpos($access['admin'], 'add') !== false){ @endphp
                                    <span class="pull-right"><a href="/admin/departments/create" class="btn btn-success">Create Department</a></span>
                                @php } @endphp

                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 adv-search-container">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <button class="btn btn-default pull-right adv-search-btn">Advance Search</button>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div id="department-tree" class="chart"> </div>
                            <div id="department-table"  style="display: none">
                            <table id="departments-table"  name="departments-table" class="table table-striped">
                            </div>
                                <thead>
                                    <tr>
                                        <th width="5%">Color</th>
                                        <th width="15%">Division</th>
                                        <th width="20%">Name</th>
                                        <th width="20%">Parent Department</th>
                                        <th width="20%">Company</th>
                                        <th width="20%">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                @include('incs.advanceSearch')
            </section>
        </div>

        {!! $userList !!}
    @endsection

    @section("script")
        <script src="{{ config("app.cdn") . "/js/admin/departments.js" . "?v=" . config("app.version") }}"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.css">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js"></script> -->
        <script src="{{ config("app.cdn") . "/js/treant.js" . "?v=" . config("app.version") }}"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js.map"></script> -->


        <style type="text/css">
            
        .nodeExample1 {
            padding: 2px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            background-color: #ffffff;
            border: 1px solid #000;
            width: 200px;
            font-family: Tahoma;
            font-size: 12px;
        }
        .nodeExample1 img {
            margin-right:  10px;
            width: 30%; height: 30%;
        }

        .node-desc {
            margin: 0 10px;
            font-size: .6rem;
        }
        .node-contact {
             margin: 0 10px;
            font-size: .6rem;
        }
.Treant > .node { padding: 3px; border: 2px solid #484848; border-radius: 3px; }
.Treant > .node img { width: 30%; height: 30%; }

.Treant .collapse-switch { width: 100%; height: 70%; border: none;}
.Treant .node.collapsed { background-color: #DEF82D; }
.Treant .node.collapsed .collapse-switch { background: none; width: 100%; height: 70%;}
.node-tip {
    display: none;
    position: absolute;
    border: 0px solid #333;
    background-color: rgba(0,0,0,0.7);
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
    -webkit-box-shadow: #000 1px 5px 20px;
    -moz-box-shadow: #000 1px 5px 20px;
    box-shadow: #000 1px 5px 20px;
    padding: 10px;
    color: #fff;
    font-size: 16px;
    width: 250px;
}
        </style>

        <script type="text/javascript">
            $(document).ready(function () {
                $(".node").hover(function () {
                    var title = $(this).attr('id');
                    id = title.replace("node", "");
                    $('<div class="node-tip"></div>').html($('#user'+id).val()).appendTo('body').fadeIn('slow');
                }, function () {
                    $('.node-tip').remove();
                }).mousemove(function (e) {
                    var mousex = e.pageX + 20;
                    var mousey = e.pageY + 10;
                    $('.node-tip')
                        .css({
                            top: mousey,
                            left: mousex
                        });
                });

                $( ".node-desc" ).addClass( "btn btn-primary btn-sm fa fa-pencil" );
                $( ".node-contact" ).addClass( "btn btn-danger btn-sm fa fa-trash" );
                $('.node-contact').attr('onClick', 'return confirm("Delete this Department?")');
                onclick="return confirm('.$message.')"
                $('.node').click(function () {

                    $('.node').css("border","1px  solid #484848");
                    // $('.node').css("background-color","");
                    $(this).css("border","3px  solid #484848");
                    // $(this).css("background-color","#17a2b8");
                     
                });
                $('#toggleView').click(function () {
                    if($('#toggleView').val() == 'Table View'){
                        $('#department-table').show();
                        $('#department-tree').hide();
                        $('#toggleView').val('Tree View');
                        $('.adv-search-btn').show();
                    }else{
                        $('#department-table').hide();
                        $('#department-tree').show();
                        $('#toggleView').val('Table View');
                        $('.adv-search-btn').hide();
                    }
                });

                $('.adv-search-btn').hide();

            });

            {!!  $js !!}


        </script>
    @endsection

@endif