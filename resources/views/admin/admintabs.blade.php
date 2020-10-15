@php 
$access = session('all_user_access'); 
@endphp
<ul class="nav nav-tabs ui-sortable-handle">
	@php
		if (isset($access['admin'])){ 
	    	if(strpos($access['admin'], 'division') !== false){ @endphp
				<li class="{{strpos(Request::url(),'divisions')!== false ? "active" : "" }}"><a href="{{ url("admin/divisions") }}">Division</a></li>
	@php }} @endphp
    @php
		if (isset($access['admin'])){ 
	    	if(strpos($access['admin'], 'department') !== false){ @endphp
				<li class="{{strpos(Request::url(),'departments')!== false ? "active" : "" }}"><a href="{{ url("admin/departments") }}">Department</a></li>
	@php }} @endphp
    @php
		if (isset($access['admin'])){ 
	    	if(strpos($access['admin'], 'department full access') !== false){ @endphp
				<li class="{{strpos(Request::url(),'system-group')!== false ? "active" : "" }}"><a href="{{ url("admin/system-group") }}">System Defined Group</a></li>
	@php }} @endphp

	@php
		if (isset($access['admin'])){ 
	    	if(strpos($access['admin'], 'module') !== false){ @endphp
				<li class="{{strpos(Request::url(),'acl')!== false ? "active" : "" }}"><a href="{{ url("admin/acl") }}">Permissions</a></li>
	@php }} @endphp
	@php
		if (isset($access['users'])){ 
	    	if(strpos($access['users'], 'view') !== false){ @endphp
				<li class="{{strpos(Request::url(),'users')!== false ? "active" : "" }}"><a href="{{ url("admin/users") }}">System User</a></li>
	@php }} @endphp		
	@php
		if (isset($access['users'])){ 
	    	if(strpos($access['users'], 'full access') !== false){ @endphp
				<li class="{{strpos(Request::url(),'system-accounts')!== false ? "active" : "" }}"><a href="{{ url("admin/system-accounts") }}">System Defined Users</a></li>
	@php }} @endphp
	@php
		if (isset($access['admin'])){ 
	    	if(strpos($access['admin'], 'access rights template') !== false){ @endphp
	<li class="{{strpos(Request::url(),'group-templates')!== false ? "active" : "" }}"><a href="{{ url("admin/group-templates") }}">Access Rights Templates</a></li>
	@php }} @endphp
</ul>