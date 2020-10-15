@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                My Profile
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li class="active">My Profile</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="profile-grp">
                <h4 class="title">My Account</h4>
                <p class="info">
                    <span>Username:</span><br/>
                    <span>Email Address:</span><br/>
                    <span>Role:</span><br/>
                    <span>Type:</span>
                </p>
                <a href="#" class="btn btn-primary btn-flat">Change Password</a>
            </div>
            <div class="profile-grp">
                <h4 class="title">My Account</h4>
                <p class="info">
                    <span>Firstname:</span><br/>
                    <span>Lastname:</span><br/>
                    <span>Timezone:</span>
                </p>
                <a href="#" class="btn btn-flat btn-close">Cancel</a>
                <a href="#" class="btn btn-flat btn-save">Save</a>
            </div>
        </section>
    </div>
@endsection