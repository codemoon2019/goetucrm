                    <div class="col-lg-12 col-xs-6">
                        <!-- LINE CHART -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title title-header">Merchant by Agents</h3>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <table class="table datatable responsive table-condense table-bordered">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th colspan="2">Agent </th>
                                            <th>Merchant Count</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($merchants as $m)
                                        <tr style="background-color: #f2f2f2">
                                            <td class='td-toggle-merchants' data-id='{{ $m->id }}'>&#9654;</td>
                                            <td colspan="2"><b>{{$m->company_name}}</b></td>
                                            <td style="text-align: center"><b>{{$m->agentCount}}</b></td>
                                            <td></td>
                                        </tr> 
                                            @foreach($m->merchant as $m2)
                                            <tr class="hidden tr-agent-merchant-{{ $m->id }}">
                                                <td colspan="2"></td>
                                                <td>{{$m2->company_name}}</td>
                                                <td>{{$m2->email}}</td>
                                                <td><a target="_blank" href="/merchants/details/{{ $m2->id }}/profile"><button class="btn btn-info btn-sm">View</button></a></td>
                                            </tr> 
                                            @endforeach    
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>


    <div id="modalViewMerchants" class="modal" role="dialog">
          <div class="modal-dialog" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Merchant List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              <div class="modal-body">
                <table class="table  table-striped" id="merchants">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
              </div>

            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
    </div>
                    