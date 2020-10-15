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
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <a href="/partners/management" class="btn btn-success">Table View</a>
                    </div>
                </div>
      
                <div class="row">
                    <div id="partner-tree" class="chart"> </div>

                </div>
            </div>
            
        </section>
    </div>
    </body>
@endsection
@section('script')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.css">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js"></script>
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
    <script>
        $(document).ready(function () {
            $( ".node-desc" ).addClass( "btn btn-primary btn-sm fa fa-pencil" );
            $('.node').click(function () {

                $('.node').css("border","1px  solid #484848");
                // $('.node').css("background-color","");
                $(this).css("border","3px  solid #484848");
                // $(this).css("background-color","#17a2b8");
                 
            });
        });
        {!!  $js !!}

    </script>

@endsection