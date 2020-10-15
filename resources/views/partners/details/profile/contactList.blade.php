@extends('layouts.app')

@section('content')
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                @include("partners.details.profile.profiletabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Contact List</h3>
                                @php 
                                    $access = session('all_user_access'); 
                                    if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                                    if(strpos($access[strtolower($partner_info->partner_type_description)], 'create contact') !== false){ @endphp
                                        <span class="pull-right mt-minus-40">
                                            <a href='{{ url("/partners/details/profile/profileContactList/create/$id") }}' class="btn btn-success btn-sm">Create Contact</a>
                                        </span>
                                @php } } @endphp
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                <tr>
                                    <th width="20%">Last Name</th>
                                    <th width="20%">First Name</th>
                                    <th width="20%">Mobile Number</th>
                                    <th width="20%">Email Address</th>
                                    <th width="20%">Title</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($partner_contacts)>0)
                                    @foreach($partner_contacts as $partner_contact)
                                    <tr>
                                        <td><a href='{{ url("/partners/details/profile/profileContactList/edit/$id/$partner_contact->id") }}'> {{$partner_contact->last_name}} </a></td>
                                        <td>{{$partner_contact->first_name}}</td>
                                        <td>
                                            @if($partner_contact->mobile_number!="")
                                                {{$partner_contact->country_code}}{{$partner_contact->mobile_number}}
                                            @endif
                                        </td>
                                        <td>{{$partner_contact->email}}</td>
                                        <td>{{$partner_contact->position}}</td>
                                    </tr>
                                    @endforeach
                                @endif
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
    <script src="{{ config("app.cdn") . "/js/partners/list.js" . "?v=" . config("app.version") }}"></script>
@endsection
