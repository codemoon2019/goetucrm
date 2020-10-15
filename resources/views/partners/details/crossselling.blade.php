@extends('layouts.app')

@section('content')
    @include("partners.details.profile.partnertabs")
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <li class="active"><a href="#commission-setup" data-toggle="tab" aria-expanded="false">Cross Selling Agents</a></li>
                </ul>

                <div class="tab-content no-padding">
                     <div class="tab-pane active" id="payment-gateway">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Agent List</h3>
                            </div>
                            <table class="table agenttable table-striped">
                                <thead>
                                    <th>Agent</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                  @foreach($agents as $a)
                                    <tr>
                                        <td>{{$a->agent}}</td>
                                        <td>{{$a->company}}</td>
                                        <td>@if($a->status == 'A') Cross-Selling @else Regular @endif</td>
                                        <td>
                                            
                                            <button type="button" id="btnRemoveCS{{$a->agent_id}}" class="btn btn-danger btn-sm" onclick="removeCrossSelling({{$id}},{{$a->agent_id}})" @if ($a->status != 'A') style="display: none;" @endif>Set As Regular</button>

                                            <button type="button" id="btnAddCS{{$a->agent_id}}" class="btn btn-info btn-sm" onclick="addCrossSelling({{$id}},{{$a->agent_id}})" @if ($a->status == 'A') style="display: none;" @endif>Set As Cross Selling</button>

                                        </td>
                                    </tr>

                                  @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
    <script type="text/javascript">
        $('.agenttable').dataTable( {
            "order": []
        });

        function addCrossSelling(id,agent_id){
            //showLoadingModal("Updating... Please wait.....");
            $.getJSON('/partners/addCrossSellingAgent/' + id + '/' + agent_id, null, function (data) {
                //closeLoadingModal();
                $('#btnRemoveCS'+agent_id).show();
                $('#btnAddCS'+agent_id).hide();
            });
        }

        function removeCrossSelling(id,agent_id){
            //showLoadingModal("Updating... Please wait.....");
            $.getJSON('/partners/removeCrossSellingAgent/' + id + '/' + agent_id, null, function (data) {
                //closeLoadingModal();
                $('#btnRemoveCS'+agent_id).hide();
                $('#btnAddCS'+agent_id).show();              
            });
        }



    </script>
@endsection
