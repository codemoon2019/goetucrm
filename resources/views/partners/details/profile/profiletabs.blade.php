<ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
    <li class="{{strpos(Request::url(),'profileOverview')!== false ? "active" : "" }}"><a href='{{ url("partners/details/profile/$id/profileOverview") }}'>Summary</a></li>
    @if($isInternal)
        <li class="{{strpos(Request::url(),'profileCompanyInfo')!== false ? "active" : "" }}"><a href='{{ url("partners/details/profile/$id/profileCompanyInfo") }}'>Company Information</a></li>
        <li class="{{strpos(Request::url(),'profileContactList')!== false ? "active" : "" }}"><a href='{{ url("partners/details/profile/$id/profileContactList") }}'>Contact List</a></li>
        <li class="{{strpos(Request::url(),'profileAttachments')!== false ? "active" : "" }}"><a href='{{ url("partners/details/profile/$id/profileAttachments") }}'>Attachments</a></li>
        <li class="{{strpos(Request::url(),'profilePaymentGateway')!== false ? "active" : "" }}"><a href='{{ url("partners/details/profile/$id/profilePaymentGateway") }}'>Payment Gateway</a></li>
    @endif
</ul>