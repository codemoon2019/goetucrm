@extends('layouts.app')

@section('style')
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
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
        .ticket-img-xs {
            box-shadow: 0 0 2.5px #000000;
            height: 20px;
            width: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
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
                <li class="active">Profile</li>
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
                    <li><a href="{{ url('prospects/details/summary/'.$partner_id) }}">Summary</a></li>
                    @if($isInternal)
                    <li class="active"><a href="{{ url('prospects/details/profile/'.$partner_id) }}">Profile</a></li>
                    <li><a href="{{ url('prospects/details/contact/'.$partner_id) }}">Contact</a></li>
                    <li><a href="{{ url('prospects/details/interested/'.$partner_id) }}">Interested Products</a></li>
                    <li><a href="{{ url('prospects/details/applications/'.$partner_id) }}">Applications</a></li>
                    <li><a href="{{ url('prospects/details/appointment/'.$partner_id) }}">Appointment</a></li>
                    @endif
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    @if($isInternal)
                    <li class="active"><a href="#info" data-toggle="tab" aria-expanded="true">Information</a></li>
                    <li class=""><a href="#notes" data-toggle="tab" aria-expanded="false">Notes</a></li>
                    @endif
                </ul>
                <div class="tab-content no-padding">
                @if($isInternal)
                    <div class="tab-pane active" id="info">
                    <form role="form" name="frmUpdateLead" id="frmUpdateLead">
                        <input type="hidden" id="txtLeadID" name="txtLeadID" value="{{$partner_id}}">
                        <input type="hidden" id="assignedID" name="assignedID" value="{{$assigned_id}}">
                        <input type="hidden" id="mobileNumber" name="mobileNumber" value="{{$partner_info[0]->mobile_number}}">
                        <input type="hidden" id="isUpdate" name="isUpdate" value="1">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Personal Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-4 hide">
                                <div class="form-group">
                                    <label for="partnerType">Partner Type:</label>
                                    <select name="partnerType" id="partnerType" class="form-control">
                                        @foreach ($partner_type as $item)
                                            <option value="{{$item->id}}"  @if($item->id == $partner_info[0]->partner_type_id) selected="selected" @endif>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="assignTo">Parent: </label>
                                            <select name="assignTo" id="assignTo" class="form-control select2">
                                                <option value="-1">Unassigned</option>    
                                                @foreach ($upline as $item)
                                                    @if($assigned_id < 0)
                                                        <option data-image="{{ $item->image }}"  value="{{ $item->parent_id }}" @if($item->parent_id == $partner_info[0]->parent_id) selected="selected" @endif>&nbsp;{{ $item->partner_id_reference }} - {{ $item->company_name }}</option>
                                                    @else
                                                        <option data-image="{{ $item->image }}"  value="{{ $item->parent_id }}" @if($assigned_id == $item->parent_id) selected="selected" @endif>&nbsp;{{ $item->partner_id_reference }} - {{ $item->company_name }} @if($assigned_id == $item->parent_id) (Pending) @endif</option>
                                                    @endif


                                                    
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 sm-col">
                                        <div class="form-group">
                                            <label for="email">{{$partner_info[0]->partner_type_description}} Source:</label>
                                            <input type="email" class="form-control" id="txtLeadSource" name="txtLeadSource" placeholder="" value="{{$partner_info[0]->partner_source}}" readonly>
                                        </div>     
                                    </div>   
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="currentProfessor">Current Payment Processor:</label>
                                    <select name="currentProcessor" id="currentProccessor" class="form-control select2" style="width:100%">
                                        <option value="None" >None</option>
                                        @foreach ($paymentProcessor as $item)
                                            <option value="{{$item->name}}" @if( $partner_info[0]->merchant_processor == $item->name) selected="selected" @endif>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="legalName">DBA:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="legalName" id="legalName" value="{{$partner_info[0]->company_name}}" placeholder="Enter DBA"/>
                                    <span id="legalName-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ownership">Ownership:<span class="required"></span></label>
                                    <select class="form-control" name="ownership" id="ownership">
                                        @foreach($ownership as $item)
                                            <option value="{{$item->code}}" @if($item->code == $partner_info[0]->ownership) selected="selected" @endif>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-5 pr-0">
                              <label for="business_industry">Business Industry<span class="required"></span></label>
                              <select name="business_industry" id="business_industry" class="form-control select2">
                                @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                  <optgroup label="{{ $groupName }}">
                                    @foreach ($businessTypes as $businessType)
                                      <option value="{{ $businessType->mcc }}" {{ $partner_info[0]->business_type_code == $businessType->mcc ? 'selected' : ''}}>
                                        {{ $businessType->description }}
                                      </option>
                                    @endforeach
                                  </optgroup>
                                @endforeach
                              </select>

                              <span id="business_industry-error" 
                                    class="business_industry-error hidden" style="color:red"><small></small></span>
                            </div>

                            <div class="form-group col-md-1 pl-0 text-center">
                              <label for="mcc">MCC<span class="required"></span></label>
                              <input type="text" id="mcc" name="mcc" class="form-control" style="border-left: 0px; text-align: center">
                              <span id="mcc-error" class="mcc-error" style="color:red"><small></small></span>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ownership">Legal Name (Business Name):<span class="required"></span></label>
                                    <input type="text" class="form-control" name="dba" id="dba" value="{{$partner_info[0]->dba}}" placeholder="Enter Legal Name"/>
                                </div>
                            </div>


                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="ownership">Status:<span class="required"></span></label>
                                    <select name="currentStatus" id="currentStatus" class="form-control select2" style="width:100%">
                                        @foreach ($leadStatus as $item)
                                            <option value="{{$item->id}}" @if( $partner_info[0]->lead_status_id == $item->id) selected="selected" @endif>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Business Address</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="businessAddress1">Business Address 1:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="businessAddress1" id="businessAddress1" value="{{$partner_info[0]->address1}}" placeholder="Enter Address"/>
                                    <span id="businessAddress1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="businessAddress2">Business Address 2:</label>
                                    <input type="text" class="form-control" name="businessAddress2" id="businessAddress2" value="{{$partner_info[0]->address2}}" placeholder="Enter Address"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="country">Country:<span class="required"></span></label>
                                    <select name="country" id="country" class="form-control s2-country">
                                         @foreach($country as $item)
                                            <option value="{{ $item->name }}" data-code="{{ $item->iso_code_2 }}" 
                                                @if($partner_info[0]->country_name == $item->name) selected="selected" @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="zip">Zip:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="zip" id="zip" value="{{$partner_info[0]->zip}}" placeholder="Zip"/>
                                    <span id="zip-error" style="color:red;"><small></small></span>
                                    @include('incs.zipHelpNote')
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label for="state">State:<span class="required">*</span></label>
                                    <select name="state" id="state" class="form-control">
                                        @foreach($state as $item)
                                            <option value="{{$item->code}}">{{$item->code}}</option>
                                        @endforeach
                                    </select> -->

                                    <div id="state_us" >
                                        <label for="state">State:<span class="required"></span></label>
                                        <select class="form-control s2-state" style="width: 100%;" id="txtState" name="txtState" disabled>
                                          @foreach($state as $item)
                                              <option value="{{$item->code}}" data-code="{{ $item->id }}" @if($partner_info[0]->state == $item->code) selected="selected" @endif>{{$item->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>

                                    <div id="state_ph" style="display:none;">
                                        <label for="state">State:<span class="required"></span></label>
                                        <select class="form-control s2-state" style="width: 100%;" id="txtStatePH" name="txtStatePH">
                                          @if(isset($statePH))
                                          @foreach($statePH as $item)
                                              <option value="{{$item->code}}" data-code="{{ $item->id }}" @if($partner_info[0]->state == $item->code) selected="selected" @endif>{{$item->name}}</option>
                                          @endforeach
                                          @endif
                                        </select>
                                    </div>

                                    <div id="state_cn" style="display:none;">
                                        <label for="state">State:<span class="required"></span></label>
                                        <select class="form-control s2-state" style="width: 100%;" id="txtStateCN" name="txtStateCN">
                                          @if(isset($stateCN))
                                          @foreach($stateCN as $item)
                                              <option value="{{$item->code}}" data-code="{{ $item->id }}" @if($partner_info[0]->state == $item->code) selected="selected" @endif>{{$item->name}}</option>
                                          @endforeach
                                          @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city">City:<span class="required"></span></label>
                                    {{-- <input type="text" class="form-control" name="city" id="city" value="{{$partner_info[0]->city}}" placeholder="Enter City"/> --}}
                                    <select name="city" id="city" class="form-control select2" disabled>
                                            <option value="{{ $partner_info[0]->city }}" selected>{{ $partner_info[0]->city }}</option>
                                    </select>
                                    <span id="city-error" style="color:red;"><small></small></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Personal Contact Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <!-- <label for="businessPhone1">Business Phone 1:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="businessPhone1" id="businessPhone1" value="" placeholder="Enter Business Phone 1"/> -->
                                    <label>Business Phone 1:<span class="required"></span></label> 
                                    <div class="input-group"> 
                                        <div class="input-group-addon"><label for="businessPhone1">1</label></div>
                                        <input type="text" class="form-control number-only" id="businessPhone1" name="businessPhone1" placeholder="Enter Business Phone 1" value="{{$partner_info[0]->nd_phone1}}">
                                    </div>
                                    <span id="businessPhone1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="extension1">Extension:</label>
                                    <input type="text" class="form-control" name="extension1" id="extension1" value="{{ $partner_info[0]->business_extension }}" placeholder="Ext"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- <label for="fax">Fax:</label>
                                    <input type="text" class="form-control" name="fax" id="fax" value="" placeholder="Enter Fax"/> -->
                                    <label>Fax:</label>
                                    <div class="input-group"> 
                                    <div class="input-group-addon"><label for="businessPhone1">1</label></div>
                                    <input type="text" class="form-control number-only" id="fax" name="fax" placeholder="Enter Fax" value="{{$partner_info[0]->fax}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <!-- <label for="businessPhone2">Business Phone 2:</label> -->
                                    <!-- <input type="text" class="form-control" name="businessPhone2" id="businessPhone2" value="" placeholder="Enter Business Phone 2"/> -->
                                    <label>Business Phone 2:</label> 
                                    <div class="input-group"> 
                                    <div class="input-group-addon"><label for="businessPhone2">1</label></div>
                                    <input type="text" class="form-control number-only" id="businessPhone2" name="businessPhone2" placeholder="Enter Business Phone 2" value="{{$partner_info[0]->nd_phone2}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="extension2">Extension:</label>
                                    <input type="text" class="form-control" name="extension2" id="extension2" value="{{ $partner_info[0]->business_extension_2 }}" placeholder="Ext"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtEmailPros">Email(must be valid):<span class="required">@if(!isset($partner_info[0]->mobile_number)) * @endif</span></label>
                                    <input type="text" class="form-control" name="txtEmailPros" id="txtEmailPros" value="{{$partner_info[0]->email}}" placeholder="Enter Email" onblur="validateData('users','email_address',this,{{$partner_id}},'true','reference_', 'Email address already been used by other users'); validateData('partner_contacts','email',this,{{$partner_id}},'false','partner_', 'Email address already been used by other partners');" />
                                    <span id="txtEmailPros-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group pull-right">
                        @if($canEdit != 0)
                            <a href="#" class="btn btn-primary" id="updateLeadProspect">Save</a>
                        @endif
                        @if($canConvert != 0)
<!--                             <button type="button" class="btn btn-success tabbtn" id="btnConvertToMerchant" name="btnConvertToMerchant">
                                Convert to Merchant
                            </button>
 -->                        @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    </form>
                    <div class="tab-pane" id="notes">
                        <div class="col-sm-12">
                        <div class="box">
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
                                                    <a href="#" id="addreply{{$comment->comment_id}}" name="addreply{{$comment->comment_id}}" class="addreply" onClick="addReply('{{$comment->comment_id}}');  return false;"><i class="fa fa-reply"></i> Add Reply</a>
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
        </section>
                <div id="modalConvertToMerchant" class="modal" role="dialog">
                    <form role="form" name="frmConvertToMerchant" id="frmConvertToMerchant" method="post">
                    <input type="hidden" id="txtPartnerID" name="txtPartnerID" value="{{$partner_id}}"> 
                    <input type="hidden" id="txtPartnerReferenceID" name="txtPartnerReferenceID" value="{{$partner_info[0]->merchant_id}}"> 
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Convert to Merchant</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Merchant MID:</label>
                                                <input type="text" id="txtMerchantMID" name="txtMerchantMID" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="btnProcessConvert" name="btnProcessConvert" class="btn btn-primary">Convert</button>
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                <!-- /.modal-content -->
                    </div>
              <!-- /.modal-dialog -->
                </form>
                @endif
            </div>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/prospects/list.js" . "?v=" . config("app.version") }}"></script>
    <script src=@cdn('/js/supplierLeads/mcc.js')></script>
@endsection
