@extends('layouts.app')

@section('content')
        <link href="https://fonts.googleapis.com/css?family=Oswald:400,500,600,700" rel="stylesheet">
        <style>
            .process img {
                max-width: 150px;
                width: 100%;
            }
            .process ul {
                list-style: square;
            }
        </style>
        <div class="content-wrapper">
            <div class="content">
                <div class="col-md-8 offset-md-2">

                    <h2 class="text-center m-4"><strong>GOETU PROCESS FLOW</strong></h2>

                    <div class="row process">
                        @php $count=1; @endphp

                        @if($canAccessProduct)
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/products.png") }}" width="150px" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>{{$count}}. Products</strong></p>
                                        <ul>
                                            @if($canAddProduct) 
                                                <li><a href="{{ url("products/create") }}">Create Product</a></li> 
                                            @endif
                                            @if($hasWorkflow) 
                                                <li><a href="{{ url("products/listTemplate#workflow") }}">Setup Workflow</a></li> 
                                            @endif
                                            @if($hasProductFee) 
                                                <li><a href="{{ url("products/listTemplate") }}">Setup Fee Template</a></li> 
                                            @endif
                                            @if($canViewProduct) 
                                                <li><a href="{{ url("products") }}">View Available Products</a></li> 
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @php $count+=1; @endphp
                        @endif

                        @if($canAccessPartners)
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/partners.png") }}" width="150px" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>{{$count}}. Partners</strong></p>
                                        <ul>
                                            @if($canAddPartner) 
                                                <li><a href="{{ url("partners/create") }}">Create Partner</a></li>
                                            @endif
                                            @if($canViewDepartment) 
                                                <li><a href="{{ url("admin/departments") }}">Setup Departments</a></li>
                                            @endif
                                            @if($canViewUser) 
                                                <li><a href="{{ url("admin/users") }}">Setup Users</a></li>
                                            @endif 
                                            @if($canViewPartner) 
                                                <li><a href="{{ url("partners/management") }}">View Partners</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @php $count+=1; @endphp
                        @endif
                        @if($canAccessMerchants)
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/merchants.png") }}" width="150px" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>{{$count}}. Merchants</strong></p>
                                        <ul>
                                            @if($canCreateMerchant) 
                                                <li><a href="{{ url("merchants/create") }}">Create Merchant</a></li>
                                            @endif
                                            @if($canCreateOrder) 
                                                <li><a href="#" data-toggle="modal" data-target="#modal-default">Create Order</a></li>
                                            @endif
                                            @if($canViewOrder) 
                                                <li><a href="{{ url("merchants/details/orders") }}">View Orders</a></li>
                                            @endif
                                            @if($canViewWorkFlow) 
                                                <li><a href="{{ url("merchants/workflow") }}">View Workflows</a></li>
                                            @endif
                                            @if($canViewBilling) 
                                                <li><a href="{{ url("merchants/details/billing") }}">Billing</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @php $count+=1; @endphp
                        @endif

                        @if($canAccessReport)
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/reports.png") }}" width="150px" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>{{$count}}. Reports</strong></p>
                                        <ul>
                                            <li><a href="{{ url("billing/report") }}">View Reports</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>

                <div class="col-lg-12">
                    <div class="row process">
                        @if($canAccessLeads)
                            <div class="col-lg-4 mb-3">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/leads.png") }}" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>Leads</strong></p>
                                        <ul>
                                            @if($canCreateLead)
                                                <li><a href="{{ url("leads/createLeadProspect") }}">Create Lead</a></li>
                                            @endif
                                            @if($canAccessLeads)
                                                <li><a href="{{ url("leads/incoming") }}">Incoming Leads</a></li>
                                            @endif
                                            @if($canViewLead)
                                                <li><a href="{{ url("leads") }}">View Leads</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($canAccessProspects)
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/prospects.png") }}" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>Prospects</strong></p>
                                        <ul>
                                            @if($canCreateProspect)
                                                <li><a href="{{ url("prospects/createLeadProspect") }}">Create Prospects</a></li>
                                            @endif
                                            @if($canAccessProspects)
                                                <li><a href="{{ url("prospects/incoming") }}">Incoming Prospects</a></li>
                                            @endif
                                            @if($canViewProspect)
                                                <li><a href="{{ url("prospects") }}">View Prospects</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($canAccessTickets)
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ asset("images/workflow/tickets.png") }}" alt="">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="h4"><strong>Tickets</strong></p>
                                        <ul>
                                            @if($canCreateTicket)
                                                <li><a href="{{ url("tickets/create") }}">Create Ticket</a></li>
                                            @endif
                                            @if($canViewTicket)
                                                <li><a href="{{ url("tickets") }}">View Tickets</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($canAccessCalendar)
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-4">
                                    <img src="{{ asset("images/workflow/calendar.png") }}" alt="">
                                </div>
                                <div class="col-lg-8">
                                    <p class="h4"><strong>Calendar</strong></p>
                                    <ul>
                                        <li><a href="{{ url("calendar") }}">View Calendar</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($canAccessTraining)
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-4">
                                    <img src="{{ asset("images/workflow/training.png") }}" alt="">
                                </div>
                                <div class="col-lg-8">
                                    <p class="h4"><strong>Training</strong></a></p>
                                    <ul>
                                        <li><a href="{{ url("training/training_list") }}">Manuals</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
   

    <div class="modal fade" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">SELECT MERCHANT</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>

          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="merchant">Merchant:</label>
                <select name="txtMerchant" id="txtMerchant" class="form-control" style="width:100%">
                    @if(count($merchants)>0)
                        @foreach($merchants as $merchant)
                            <option value="{{$merchant->partner_id}}">{{$merchant->company_name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
            <button id="btnProceed" type="button" class="btn btn-primary">Proceed</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    
@endsection
@section("script")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $('.select2').select2();
    </script>
    <script type="text/javascript">
        $('#btnProceed').click(function () {
            var id = $('#txtMerchant').val();
            window.location.href = getBaseUrl()+'merchants/details/'+id+'/products?tab=create-order'; 
        });

        function getBaseUrl() {
            var re = new RegExp(/^.*\//);
            return re.exec(window.location.href);
        }
    </script>
@endsection