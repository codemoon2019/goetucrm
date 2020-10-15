@extends('layouts.login')
<style type="text/css">
    .container {
    width: 100% !important;
}

</style>
@section('content')
    <div class="container login-bg">
        <div class="row justify-content-center">
            <div class="col-md-5 ">
                <div class="text-center">
                    <img src="{{ asset('images/logo.png') }}">
                </div>
            </div>
        </div>

        <div class="row mt-plus-20 justify-content-center">

            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body" id="loginDiv" @if(session('failed')) style="display:none;"  @endif>
                        @if(session('success')) 
                            <p class="text-center" id="forgotPasswordTxt"><b style="color:green">Email/Messages Sent</b></p>
                        @elseif(session('exist'))
                            <p class="text-center" id="forgotPasswordTxt"><b style="color:green">User is currently logged in</b></p>
                        @elseif(session('invalid'))
                            <p class="text-center" id="forgotPasswordTxt"><b style="color:red">{{session('invalid')}}</b></p>
                        @else
                            <p class="text-center">{{ __('Sign In') }}</p>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
                                @if ($errors->has('username'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" data-toggle="password"  required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group pull-right mb-0">
                                <button type="submit" class="btn btn-primary btn-flat">
                                    {{ __('Login') }}
                                </button>
                            </div>
                            <div class="clearfix"></div><br>

                            <div class="form-group mb-0">
                                <a href="javascript:void(0);" id="forgotPassword">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>

                        </form>
                    </div>

                    <div class="card-body" @if(!session('failed')) style="display:none;"  @endif  id="forgotDiv">
                        @if(session('failed')) 
                            <p class="text-center" id="forgotPasswordTxt"><b style="color:red">{{session('failed')}}</b></p>
                        @else
                            <p class="text-center" id="forgotPasswordTxt">Forgot Password</p>
                        @endif 
                        <form method="POST" action="\forgot-password">
                            @csrf

                            <div class="form-group">
                                <input id="email_address_forgot" type="text" class="form-control{{ $errors->has('email_address') ? ' is-invalid' : '' }}" name="email_address" value="{{ old('email_address') }}" placeholder="Username or Email or Mobile (Country Code-xxxxxxxxx)" required autofocus>
                            </div>

                            <div class="form-group pull-right mb-0">
                                <button type="submit" class="btn btn-primary btn-flat" id="sendEmail">
                                    Send
                                </button>
                            </div>
                            <div class="clearfix"></div>

                            <div class="form-group mb-0">
                                <a href="javascript:void(0);" id="goBack">
                                    Go Back
                                </a>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $('#forgotPassword').click(function (e) {
            $('#loginDiv').hide();
            $('#forgotDiv').show();
            $('#forgotPasswordTxt').html('Forgot Password')
        });

        $('#goBack').click(function (e) {
            $('#loginDiv').show();
            $('#forgotDiv').hide();
        });

        $('#sendEmail').click(function (e) {
            if($('#email_address_forgot').val() != "")
            {
                $('#sendEmail').hide();
            }
        });     

    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.1.2/bootstrap-show-password.min.js"></script>
    <script type="text/javascript">        $("#password").password('toggle');</script>
@endsection