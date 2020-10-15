@if(count($errors) > 0)
	@foreach($errors->all() as $error)
		<div class="alert alert-danger alert-notif">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			{{$error}}		
		</div>
	@endforeach
@endif

@if(session('success'))
	<div class="alert alert-success alert-notif">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		{{session('success')}}		
	</div>
@endif

@if(session('failed'))
	<div class="alert alert-danger alert-notif">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		{{session('failed')}}		
	</div>
@endif


@if(session('newUsername') || session('newUserid') || session('newEmail') || session('newFullName') || session('newImg'))
	<input type="hidden" name="newUsername" id="newUsername" value="{{ session('newUsername') }}">
	<input type="hidden" name="newUserId" id="newUserId" value="{{ session('newUserId') }}">
	<input type="hidden" name="newEmail" id="newEmail" value="{{ session('newEmail') }}">
	<input type="hidden" name="newFullName" id="newFullName" value="{{ session('newFullName') }}">
	<input type="hidden" name="newImg" id="newImg" value="{{ session('newImg') }}">
@endif