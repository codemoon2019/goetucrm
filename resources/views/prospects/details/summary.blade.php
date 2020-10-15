@extends('layouts.app')

@section('style')
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        p {
            margin-left: 24px;
        }
        .box-title {
            padding: 10px 15px;
            font-size: 22px !important;
        }
        .box-body {
            padding: 25px;
        }
        .custom-fl-right{
            float: right;
        }
        .ta-right{
            text-align: right;
        }
        #post-comment select{
            display: inline-block;
            width: auto;
            height: 32px;
            padding: 4px 8px;
        }
        #post-comment .custom-fl-right, .comment-post-reply .custom-fl-right{
            margin-top: 5px;
        }
        .comment-view{
            margin-top: 0px !important;
        }
        .comment-view a{
            display: inline-block;
            vertical-align: top;
            padding: 1px 4px;
            background-color: #FFFFFF;
            margin-right: 5px;
            box-shadow: 0px 1px 4px #CDCDCD;
        }
        #comment-list .comment{
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            background-color: #FFFFFF;
        }
        #comment-list .comment .comment-block{
            margin: 0 10px;
            padding: 10px 0;
            border-bottom: 1px solid #D7D7D7;
        }
        #comment-list .comment .comment-reply{
            margin-left: 30px;
            display: none;
        }
        #comment-list .comment .comment-block .comment-author{
            font-weight: 600;
        }
        #comment-list .comment .comment-block .comment-date{
            color:#3A3A3A;
        }
        #comment-list .comment .comment-block .comment-desc{
            padding-top: 6px;
        }
        #comment-list .comment .comment-options{
            padding: 10px;
        }
        #comment-list .comment .comment-options a{
            margin-right: 10px;
        }
        #comment-list .comment .comment-options a.showless, #comment-list .comment .comment-options a.cancelreply{
            display: none;
        }
        #comment-list .comment .comment-post-reply{
            display: none;
            margin: 0 10px;
            padding-top: 10px;
        }
        .discussion {
            box-shadow: 0px 0px 2px #E6E6E6;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Prospect
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/prospects">Prospects</a></li>
                <li class="active">Summary</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                @if(!isset($partner_info) || count($partner_info) <= 0)
                <h3>Prospect Company</h3>
                <a href="{{ url("prospects/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Prospects</a>
                <div class="crearfix"></div>
                <small class="small-details">
                    Prospect ID <br/>
                    Business Address <br/>
                    Contact Phone <br/>
                    Email Address
                </small>
                @else
                <h3>{{ $partner_info[0]->company_name }}</h3>
                <a href="{{ url("prospects/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Prospects</a>
                <div class="crearfix"></div>
                <small class="small-details">
                    {{ $partner_info[0]->partner_id_reference }} <br/>
                    {{ $partner_info[0]->address1 }}, {{ $partner_info[0]->city}} {{ $partner_info[0]->state }}, {{ $partner_info[0]->zip }}, {{ $partner_info[0]->country_name }} <br/>
                    {{ $calling_code }}{{ $partner_info[0]->phone1 }} <br/>
                    {{ $partner_info[0]->email }}
                </small>
                @endif
            </div>
            <div class="nav-tabs-custom">
                <ul class="tabs-rectangular">
                    <li class="active"><a href="{{ url('prospects/details/summary/'.$partner_id) }}">Summary</a></li>
                    @if($isInternal)
                    <li><a href="{{ url('prospects/details/profile/'.$partner_id) }}">Profile</a></li>
                    <li><a href="{{ url('prospects/details/contact/'.$partner_id) }}">Contact</a></li>
                    <li><a href="{{ url('prospects/details/interested/'.$partner_id) }}">Interested Products</a></li>
                    <!-- <li><a href="{{ url('prospects/details/applications/'.$partner_id) }}">Applications</a></li> -->
                    <li><a href="{{ url('prospects/details/appointment/'.$partner_id) }}">Appointment</a></li>
                    @endif
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <!-- <li class="active"><a href="#overview" data-toggle="tab" aria-expanded="true">Overview</a></li> -->
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="overview">
                        <div class="row">
                            <div class="col-md-5">
                                <!-- Company Information -->
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h2 class="box-title">Company Information</h2>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong><i class="fa fa-level-up margin-r-5"></i> Upline</strong>

                                                    <p class="text-muted">
                                                        @foreach ($upline as $item)
                                                            @if($assigned_id < 0)
                                                                @if($item->parent_id == $partner_info[0]->parent_id) {{ $item->partner_id_reference }} - {{ $item->company_name }} @endif
                                                            @else
                                                                @if($assigned_id == $item->parent_id) {{ $item->partner_id_reference }} - {{ $item->company_name }} @if($assigned_id == $item->parent_id) (Pending) @endif @endif
                                                            @endif
                                                        @endforeach

                                                        @if($assigned_id < 0 && $partner_info[0]->parent_id < 0)
                                                            No Assignee
                                                        @endif
                                                        <br>
                                                    </p>
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <strong><i class="fa fa-building margin-r-5"></i> DBA</strong>

                                                    <p class="text-muted">
                                                        @isset($partner_info[0]->company_name)
                                                            {{ $partner_info[0]->company_name }}	
                                                        @endisset
                                                        <br>								
                                                    </p>
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <strong><i class="fa fa-industry margin-r-5"></i> Business Industry - MCC</strong>

                                                    <p class="text-muted">
                                                        @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                                            @foreach ($businessTypes as $item)
                                                                @if($item->mcc == $partner_info[0]->business_type_code)  {{ $groupName }} - {{ $item->description }} - {{ $item->mcc }} @endif
                                                            @endforeach
                                                        @endforeach
                                                        <br>
                                                    </p>

                                                    
                                                </div>
                                                <div class="form-group">
                                                    <strong><i class="fa fa-book margin-r-5"></i> Legal Business Name</strong>

                                                    <p class="text-muted">
                                                        @isset($partner_info[0]->dba)
                                                            {{ $partner_info[0]->dba }}
                                                        @endisset
                                                        <br>
                                                    </p>

                                                    
                                                </div>
                                                <div class="form-group">
                                                    <strong><i class="fa fa-flag margin-r-5"></i> Ownership</strong>

                                                    <p class="text-muted">
                                                        @isset($partner_info[0]->ownership)
                                                            @foreach($ownership as $item)
                                                                @if($partner_info[0]->ownership == $item->code)
                                                                    {{ $item->name }}
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>									
                                                    </p>

                                                </div>
                                                <div class="form-group">
                                                    <strong><i class="fa fa-phone margin-r-5"></i> Business Phone 1</strong>

                                                    <p class="text-muted">
                                                        @isset($partner_info[0]->phone1)
                                                            {{ $calling_code . $partner_info[0]->phone1 }}	
                                                        @endisset
                                                        <br>								
                                                    </p>

                                                </div>
                                                <div class="form-group">
                                                    <strong><i class="fa fa-envelope margin-r-5"></i> Email Address</strong>

                                                    <p class="text-muted">
                                                        @isset($partner_info[0]->email)
                                                            {{ $partner_info[0]->email }}	
                                                        @endisset
                                                        <br>								
                                                    </p>
                                                    
                                                </div>
                                                @if($partner_info[0]->partner_type_id ==1)
                                                <div class="form-group">
                                                    <strong><i class="fa fa-envelope margin-r-5"></i> Tax ID Number</strong>

                                                    <p class="text-muted" id="taxIDNumber">
                                                        @isset($partner_info[0]->tax_id_number)
                                                            {{ $partner_info[0]->tax_id_number }}	
                                                        @endisset
                                                        <br>								
                                                    </p>
                                                    
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <!-- Business Address -->	
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h2 class="box-title">Business Address Information</h2>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong> Business Address</strong>

                                                    <div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
                                                        <span class="text-muted">
                                                            @isset($partner_info[0]->address1)
                                                                {{ $partner_info[0]->address1 }}	
                                                            @endisset
                                                            <br>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between" style="margin-left: 24px;">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @isset($partner_info[0]->city)
                                                                {{ $partner_info[0]->city }}
                                                            @endisset
                                                            <br>
                                                            </span>
                                                            <small class=" text-muted align-self-center">(City)</small>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @isset($partner_info[0]->state)
                                                                @foreach($states as $item)
                                                                    @if($item->abbr == $partner_info[0]->state || $item->name == $partner_info[0]->state)
                                                                        {{ $item->name }} ({{ $item->abbr }})
                                                                    @endif
                                                                @endforeach
                                                            @endisset
                                                            <br>
                                                            </span>
                                                            <small class="text-muted align-self-center">(State)</small>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @isset($partner_info[0]->country)
                                                                @foreach($country as $item)
                                                                    @if($item->name == $partner_info[0]->country)
                                                                        {{ $item->name }} ({{ $item->iso_code_2 }})
                                                                    @endif
                                                                @endforeach
                                                            @endisset
                                                            <br>
                                                            </span>
                                                            <small class="text-muted align-self-center">(Country)</small>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @isset($partner_info[0]->zip)
                                                                {{ $partner_info[0]->zip }}
                                                            @endisset
                                                            <br>
                                                            </span>
                                                            <small class="text-muted align-self-center">(Zip)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- Contact Person Information -->	
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h2 class="box-title">Contact Person Information</h2>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong> Contact Person</strong>
                                                    <div class="d-flex justify-content-between" style="margin-left: 24px;padding: 10px 10px 10px 0;">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @isset($partner_info[0]->position)
                                                                {{ $partner_info[0]->position }}	
                                                            @endisset
                                                            <br>
                                                            </span>
                                                            <small class=" text-muted align-self-center">(Title/Position)</small>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @if($partner_info[0]->first_name != "" || $partner_info[0]->middle_name != "" || $partner_info[0]->last_name != "")
                                                                {{ $partner_info[0]->first_name }}  {{ $partner_info[0]->middle_name }}  {{ $partner_info[0]->last_name }}	
                                                            @endif
                                                            <br>
                                                            </span>
                                                            <small class="text-muted align-self-center">(Full name)</small>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-muted">
                                                            @isset($partner_info[0]->mobile_number)
                                                                {{ $calling_code }}{{ $partner_info[0]->mobile_number }}	
                                                            @endisset
                                                            <br>
                                                            </span>
                                                            <small class="text-muted align-self-center">(Mobile number)</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Company Information -->
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Notes</h3> 
                                    </div>
                                    <!-- /.box-header -->
                                    <!-- form start -->
                                        <div class="box-body">
                                            <div class="modal-body">
                                                <form id="frmComment{{$partner_id}}" name="frmComment{{$partner_id}}" enctype="multipart/form-data">
                                                    <input type="hidden" name="txtPartnerId" id="txtPartnerId" value="{{$partner_id}}"/>
                                                    <input type="hidden" name="txtParentId" id="txtParentId" value="-1"/>
                                                    
                                                    <div id="post-comment">
                                                        <div class="form-group">
                                                            <br>
                                                            <div class="custom-fl-right comment-view">
                                                                <a href="#" class="cv-showall" onclick="showAllReplies();  return false;" title="Show All"><i class="fa fa-navicon"></i></a>
                                                                <a href="#" class="cv-showless" onclick="hideAllReplies();  return false;" title="Show Less"><i class="fa fa-minus"></i></a>
                                                            </div>
                                                            @if($canAdd==1)
                                                            <textarea name="txtComment" id="txtComment" class="form-control custom-textarea" placeholder="Type here..." rows="6"></textarea>
                                                            <input type="file" name="file{{$partner_id}}" id="file{{$partner_id}}" class="inputfile" data-multiple-caption="files selected" multiple style="display:none;" />
                                                            @endif

                                                        </div>
                                                        <div class="form-group ta-right">
                                                            @if($canAdd==1)
                                                            <label for="state">Status:</label>
                                                            <select  id="txtPartnerStatus" name="txtPartnerStatus">
                                                            @foreach($partner_status as $item)
                                                                    <option value="{{$item->name}}"  @if($item->name == $partner_info[0]->partner_status) selected="selected" @endif>{{$item->name}}</option>
                                                            @endforeach
                                                            </select>
                                                            <button type="submit" class="btn btn-primary">Send</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </form>  
                                                @foreach($comments as $comment) 
                                                <div id="comment-list">
                                                    
                                                    <div class="comment discussion" id="comment{{$comment->comment_id}}">
                                                        
                                                        <div class="comment-block comment-main">
                                                            <span class="comment-author">{{$comment->first_name}} {{$comment->last_name}}</span> | 
                                                            <span class="comment-date">{{$comment->created_at}}</span> 
                                                            <span class="comment-author" style="text-transform: uppercase;">| {{$comment->lead_status}}</span>
                                                            <div class="comment-desc">
                                                                {{$comment->comment}}
                                                            </div>
                                                        </div>
                                                        @if(count($comment->sub_comments)>0)
                                                        @foreach($comment->sub_comments as $sub)             
                                                        <div class="comment-block comment-reply" style="display:none;">
                                                            <span class="comment-author">{{$sub->first_name}} {{$sub->last_name}}</span> | 
                                                            <span class="comment-date">{{$sub->created_at}}</span> 
                                                            <span class="comment-author" style="text-transform: uppercase;">| {{$sub->lead_status}}</span>  
                                                            <div class="comment-desc">
                                                                {{$sub->comment}}
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        @endif

                                                        <form name="frmSubComment{{$comment->comment_id}}" id="frmSubComment{{$comment->comment_id}}">
                                                            <div class="comment-post-reply" id="divCommentPostReply{{$comment->comment_id}}" name="divCommentPostReply{{$comment->comment_id}}" style="display:none;">  
                                                                @if($canAdd==1)
                                                                <div class="form-group">
                                                                    <input type="hidden" name="txtPartnerId" id="txtPartnerId" value="{{$comment->partner_id}}"/>
                                                                    <input type="hidden" name="txtParentId" id="txtParentId" value="{{$comment->comment_id}}"/>
                                                                    <textarea name="txtSubComment" id="txtSubComment" class="form-control custom-textarea" placeholder="Type here..." rows="2"></textarea>
                                                                    <input type="file" name="file{{$comment->comment_id}}" id="file{{$comment->comment_id}}" class="inputfile" data-multiple-caption="files selected" multiple style="display:none;" />
                                                                    <div class="custom-fl-right">
                                                                    
                                                                    <label for="state">Status:</label>
                                                                        <select  id="txtPartnerStatusSub" name="txtPartnerStatusSub">
                                                                        @foreach($partner_status as $item)
                                                                                <option value="{{$item->name}}"  @if($item->name == $partner_info[0]->partner_status) selected="selected" @endif>{{$item->name}}</option>
                                                                        @endforeach
                                                                        </select>
                                                                        <button type="submit" class="btn btn-primary">Send</button>
                                                                    
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </form>
                                                        <div class="comment-options">
                                                            @if($canAdd==1)
                                                            <a href="#" id="addreply{{$comment->comment_id}}" name="addreply{{$comment->comment_id}}" class="addreply" onClick="addReply('{{$comment->comment_id}}'); return false;"><i class="fa fa-reply"></i> Add Reply</a>
                                                            @endif
                                                            <a href="#" id="cancelreply{{$comment->comment_id}}" name="cancelreply{{$comment->comment_id}}" class="cancelreply" onClick="cancelReply('{{$comment->comment_id}}');  return false;" style="display:none;"><i class="fa fa-times"></i> Cancel Reply</a>
                                                            @if(count($comment->sub_comments) > 0)
                                                                <a href="#" class="showall" name="showall{{$comment->comment_id}}" id="showall{{$comment->comment_id}}" onclick="showAllSpecific('{{$comment->comment_id}}');  return false;">
                                                                    ({{count($comment->sub_comments)}})Show All
                                                                </a>
                                                            @endif
                                                            <a href="#" class="showless" name="showless{{$comment->comment_id}}" id="showless{{$comment->comment_id}}" onclick="hideAllSpecific('{{$comment->comment_id}}');  return false;" style="display:none;">Show Less</a>
                                                        </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        <!-- /.box-body -->
                                        </div>
                                <!-- /.box -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/prospects/list.js" . "?v=" . config("app.version") }}"></script>
@endsection
