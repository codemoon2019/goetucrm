@extends('layouts.login')

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
                    <div class="card-body">
                        <form action="" name ="frmVerify" id= "frmVerify" method="post">
                            @csrf
                            <input type="hidden" id="email_address" name="email_address" value="{{$request->email_address}}">
                            <input type="hidden" id="username" name="username" value="{{$request->username}}">
                            <input type="hidden" id="password" name="password" value="{{$request->password}}">
                            <div class="form-group">
                                <label>Verification code was sent to this number:</label>
                                <input type="text" class="form-control" placeholder="Mobile Number" id="mobile_number" name="mobile_number" value="{{$user->country_code}}{{$user->mobile_number}}" readonly><br>
                                <input type="text" class="form-control" placeholder="Input Verification Code Here" id="verification_code" name="verification_code"><br>
                                <input type="button" class="btn btn-primary btn-block btn-flat" value="Submit" id="submit_verification_code" name="submit_verification_code" onclick="verifyCode();">
                                <input type="button" class="btn btn-primary btn-block btn-flat" value="Request New Code" id="request_verification_code" name="request_verification_code" onclick="requestCode();">
                                <input type="button" class="btn btn-danger btn-block btn-flat" value="Cancel" id="cancel" name="cancel" onclick="location.href = '/login';">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@section('script')
    <script>
        function requestCode() {
            var postdata = $("#frmVerify").serialize();
            $.postJSON("/request_code", postdata, function (data) {
                if (data.success) {
                    alert('New Verification Code Sent!');
                }else{
                    alert('Error Sending Verification Code!');
                }

            });
        }

        function verifyCode() {
            var postdata = $("#frmVerify").serialize();
            $.postJSON("/verify_code", postdata, function (data) {
                if (data.success) {
                    location.href = '/';
                }else{
                    alert('Invalid Verification Code!');
                }
            });
        }


        jQuery.extend({
            postJSON: function postJSON(url, data, callback) {
                return jQuery.post(url, data, callback, "json");
            }
        });
    </script>
@endsection