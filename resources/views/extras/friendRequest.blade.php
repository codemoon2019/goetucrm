@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Contact Request
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li>Extras</li>
                <li class="active">Contact Request</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="clearfix"></div>
            <div id="companyContainer" class="">

                <div class="col-md-12 no-padding">
                    <table class="table datatables table-condense">
                        <thead>
                        <tr>
                            <th>From</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        @if($friendRequest)
                        @foreach($friendRequest as $fr)
                        <tr>
                            <td>{{$fr->name}}</td>
                            <td>
                                <button class="btn btn-default btn-sm" onclick="acceptRequest({{$fr->id}})">Accept</button>
                                <button class="btn btn-danger btn-sm" onclick="declineRequest({{$fr->id}})">Decline</button>
                            </td>
                           <!--  <td>Go2POS</td>
                            <td>
                                <button class="btn btn-default btn-sm">Accept</button>
                                <button class="btn btn-danger btn-sm">Decline</button>
                            </td> -->
                        </tr>
                        @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $('.datatables').dataTable();
        function acceptRequest(id){
            var data = '&id='+id;
            $.ajax({
                type:'POST',
                url:'/extras/chats/acceptRequest',
                data:data,
                dataType:'json',
                success:function(data){
                    alert(data.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 300);
                }
            });
        }   
        function declineRequest(id){
            var data = '&id='+id;
            $.ajax({
                type:'POST',
                url:'/extras/chats/declineRequest',
                data:data,
                dataType:'json',
                success:function(data){
                    alert(data.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 300);
                }
            });
        }
    </script>
@endsection