@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Change Password
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active">Change Password</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form id="frmChangePassword" name="frmChangePassword" role="form" action="{{ url("/extras/updatePassword/$user_id") }}"  enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <div class="row">
 
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="current_password">Current password:<span class="required">*</span></label>
                                    <input type="password" class="form-control" name="current_password" id="current_password"  placeholder="Enter Current Password"/>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="new_password">New Password:<span class="required">*</span></label>
                                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter New Password"/>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="confirm_new_password">Re-enter New Password:<span class="required">*</span></label>
                                    <input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password"  placeholder="Confirm New Password"/>
                                </div>
                            </div>
                            

                            <div class="clearfix"></div>
                            <div class="col-sm-12" align="right">
                                <div class="form-group col-sm-3">
                                    <input class="btn btn-primary form-control" type="submit" value="Save" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/extras/notification.js" . "?v=" . config("app.version") }}"></script>
@endsection