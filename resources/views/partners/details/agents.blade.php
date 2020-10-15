@extends('layouts.app')

@section('content')
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    @if(count($partner_types)>0)
                        @foreach($partner_types as $partner_type)
                            @if($partner_info->partner_type_description!=$partner_type->name)
                                <li class="{{strpos($partner_type->id.$partner_type->name,$active_partner_tab)!== false ? "active" : "" }}"><a href="#{{$partner_type->id}}" data-toggle="tab" aria-expanded="true">{{($partner_type->display_name == "") ? $partner_type->name : $partner_type->display_name }}</a></li>
                            @endif
                        @endforeach
                    @endif
                </ul>
                <div class="tab-content no-padding">
                    @if(count($partner_details)>0)  
                        @foreach($partner_details as $partner_detail)  
                        @if($partner_info->partner_type_description!=$partner_detail['name'])                
                        <div class="tab-pane {{strpos($partner_detail['id'].$partner_detail['name'],$active_partner_tab)!== false ? "active" : "" }}" id="{{$partner_detail['id']}}">
                            <div class="row">
                                <div class="row-header">
                                    <h3 class="title">{{$partner_detail['name']}}</h3>
                                </div>
                                <table class="table datatables table-striped">
                                    <thead>
                                        {{-- <th>Type</th> --}}
                                        @if($is_admin)
                                            @if(strpos($partner_detail['name'],"COMPANY") ===false)
                                                <th>Partners</th>
                                            @endif
                                        @endif
                                        <th>Company Name</th>
                                        <th>Contact Person</th>
                                        <th>Mobile Phone</th>
                                        <th>Email</th>
                                        <th>State</th>
                                    </thead>
                                    <tbody>
                                        @foreach($partner_detail['partner_details'] as $detail)
                                            <tr>
                                            {{-- <td>{{$detail['partner_type']}}</td> --}}
                                            @if($is_admin)
                                                @if(strpos($partner_detail['name'],"COMPANY")===false)
                                                    <td>
                                                        @foreach($detail['upline_partners'] as $upline_partner)
                                                            {{$upline_partner->first_name}} {{$upline_partner->last_name}} - {{$upline_partner->merchant_id}} <br>
                                                        @endforeach
                                                    </td>
                                                @endif
                                            @endif
                                            @php
                                                $link_opening = "";
                                                $link_closing = "</a>";
                                                if($partner_detail['name']=="MERCHANT")
                                                {
                                                    $link_opening = '<a href="/merchants/details/'.$detail['partner_id'].'/profile">';
                                                } elseif($partner_detail['name']=="PROSPECT") {
                                                    $link_opening = '<a href="/leads/details}/profile/'.$detail['partner_id'].'">';
                                                } elseif($partner_detail['name']=="LEAD") {
                                                    $link_opening = '<a href="/prospects/details/profile/'.$detail['partner_id'].'">';
                                                } else {
                                                    $link_opening = '<a href="/partners/details/profile/'.$detail['partner_id'].'/profileCompanyInfo">';
                                                }
                                            @endphp
                                            <td>{!!$link_opening!!}{{$detail['company_name']}}{!!$link_closing!!}</td>
                                            <td>{{$detail['first_name']}} {{$detail['last_name']}}</td>
                                            <td>{{$detail['country_code']}}{{$detail['phone1']}}</td>
                                            <td>{{$detail['email']}}</td>
                                            <td>{{$detail['state']}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    @endif
                </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/list.js" . "?v=" . config("app.version") }}"></script>
@endsection