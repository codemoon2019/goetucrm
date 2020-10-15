@php
$full_name = auth()->user()->first_name .' '. auth()->user()->last_name;
$username = auth()->user()->username;
$user_id = auth()->user()->id;
$email = auth()->user()->email_address;
$usertype = auth()->user()->user_type_id;
$img_url = auth()->user()->image;
@endphp

<input type="hidden" name="full_name" id="full_name" value="{{$full_name}}">
<input type="hidden" name="username" id="username" value="{{$username}}">
<input type="hidden" name="user_id" id="user_id" value="{{$user_id}}">
<input type="hidden" name="email" id="email" value="{{$email}}">
<input type="hidden" name="usertype" id="usertype" value="{{$usertype}}">
<input type="hidden" name="cdn_url" id="cdn_url" value="{{config('app.cdn')}}">
<input type="hidden" name="img_url" id="img_url" value="{{$img_url}}">
<input type="hidden" name="is_online" id="is_online">


<div id="chatboxes" class="chatboxes minimized hide">
    <div id="chat-header-hidden" class="chat-header-hidden title hide pull-left">
        <div class="chat-header-hidden-list" id="chat-header-hidden-list">
        </div>
    </div>
    <div class="chat-other">
        <a href="javascript:void(0);" onclick="showOtherChatbox();" style="color:#ffffff;">
            <div class="chat-header-more">
                <i class="fa fa-comments-o"></i>
            </div>
        </a>
    </div>
</div>
<div class="chat-container">
    <!-- <div id="box1" class="chatbox minimized">
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="title">Franco</div>
                <div class="clearfix"></div>
            </div>
            <div class="chat-header-right">
                <a href="#" class="minimize"><i class="fa fa-chevron-down"></i></a>
                <a href="#"><i class="fa fa-window-maximize"></i></a>
                <a href="#"><i class="fa fa-close"></i></a>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="chat-body hide"></div>
        <div class="chat-footer border-top hide">
            <textarea class="" placeholder="Type a message here.."></textarea>
            <div class="btn-send">
                <a href=""><i class="fa fa-send"></i></a>
            </div>
            <div class="btn-clip">
                <a href=""><i class="fa fa-paperclip"></i></a>
            </div>
        </div>
    </div>
    <div id="box2" class="chatbox minimized">
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="title">Miya Arsosa</div>
                <div class="clearfix"></div>
            </div>
            <div class="chat-header-right">
                <a href="#" class="minimize"><i class="fa fa-chevron-down"></i></a>
                <a href="#"><i class="fa fa-window-maximize"></i></a>
                <a href="#"><i class="fa fa-close"></i></a>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="chat-body hide">
            <div class="col-md-12">
                <div class="row">
                    <div class="receiver-img oline-img">
                        <img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/>
                        <div class="clearfix"></div>
                    </div>
                    <div class="chat-msg">
                        <i class="fa fa-caret-left"></i>
                        <p class="rcloud">case utamur eam</p>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="chat-smsg">
                        <i class="fa fa-caret-right"></i>
                        <p class="scloud">iisque accommodare an eam</p> -->
    <!-- <div class="clearfix"></div> -->
    <!--     </div>
                </div>
                <div class="row">
                    <div class="receiver-img oline-img">
                        <img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/>
                        <div class="clearfix"></div>
                    </div>
                    <div class="chat-msg">
                        <i class="fa fa-caret-left"></i>
                        <p class="rcloud">Reque blandit qui eu, cu vix nonumy volumus.</p>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="chat-smsg">
                        <i class="fa fa-caret-right"></i>
                        <p class="scloud">Legendos intellegam</p>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="date">Today</div>
                </div>
                <div class="row">
                    <div class="receiver-img oline-img">
                        <img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/>
                        <div class="clearfix"></div>
                    </div>
                    <div class="chat-msg">
                        <i class="fa fa-caret-left"></i>
                        <p class="rcloud">id usu, vide oporteat vix eu, id illud principes has. Nam tempor</p>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="row">
                <div class="chat-smsg">
                    <i class="fa fa-caret-right"></i>
                    <p class="scloud">Nam tempor utamur gubergren no.</p>
                    <div class="clearfix"></div>
                </div>
            </div>
            </div>
        </div>
        <div class="chat-footer border-top hide">
            <textarea class="" placeholder="Type a message here.."></textarea>
            <div class="btn-send">
                <a href=""><i class="fa fa-send"></i></a>
            </div>
            <div class="btn-clip">
                <a href=""><i class="fa fa-paperclip"></i></a>
            </div>
        </div>
    </div> -->

    <!-- <div class="chat-boxes-container">    
    </div> -->

    <div id="chatusers_{{$user_id}}" class="chatbox minimized">
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="title">Chat Users</div>
                <div class="clearfix"></div>
            </div>
            <div class="chat-header-right">
                <!-- <a href="#" class="info-circle"><i class="fa fa-info-circle"></i></a> -->
                <label id="notif-count" class="label-danger notif-count hide">0</label>
                <a href="javascript:void(0);" onclick="minimize('chatusers');" class="minimize"><i class="minmax fa fa-chevron-up"></i></a>
                <a href="#" data-toggle="modal" data-target="#chatGroupModal">
                    <img class="create-group" src="{{ asset('images/contact-request.png') }}">                
                </a>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="chat-body hide">
            <div id="recents">
                <div class="cat-title">RECENTS</div>
                <div id="new-list">
                </div>
            </div>
            <div id="favorites">
                <div class="cat-title">FAVORITES</div>
                <div id="favorites-list">
                </div>
            </div>
            <div id="online">
                <div class="cat-title">ONLINE</div>
                <div id="online-list">
                    <!-- <div id="user_1">
                        <div class="user-img online-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Miya</div><span class="online-span">online</span></div>
                    </div>
                    <div id="user_4">
                        <div class="user-img online-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Eudora</div><span class="online-span">online</span></div>
                    </div> -->
                </div>
            </div>
            <div id="groupconvo">
                <div class="cat-title">GROUP CONVERSATION</div>
                <div id="group-list">
                    <!-- <div id="user_2">
                        <div class="user-img away-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Moscov, Eudora, Layla</div><span class="away-span">away</span></div>
                    </div> -->
                </div>
            </div>
            <div id="offline">
                <div class="cat-title">OFFLINE</div>
                <div id="offline-list">
                    <!-- <div id="user_3">
                        <div class="user-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Layla</div><span>offline</span></div>
                    </div>
                    <div id="user_5">
                        <div class="user-img busy-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Franco</div><span class="busy-span">busy</span></div>
                    </div> -->
                </div>
            </div>
            <div id="searchedUsers" class="searchedUsers hide">
                <div class="cat-title">Search to Add</div>
                <div id="search-list">
                    <!-- <div id="user_3">
                        <div class="user-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Layla</div><span>offline</span></div>
                    </div>
                    <div id="user_5">
                        <div class="user-img busy-img"><img src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="" width="100%"/></div>
                        <div class="chat-name"><div class="title">Franco</div><span class="busy-span">busy</span></div>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- <div class="chat-info">
            <h4 class="title mb-plus-20 mt-plus-20"><strong>chat group name</strong></h4>

            <span class="title">Group Participants</span>
            <ul>
                <li><label>Miya , </label></li>
                <li><label>Franco, </label></li>
                <li><label>Layla, </label></li>
                <li><label>Moscov, </label></li>
                <li><label>Alpha</label></li>
            </ul>

            <div class="chat-group-btn">
                <a href="#" class="btn btn-warning btn-sm" title="Edit Chat Group"><i class="fa fa-pencil"></i></a>
                <a href="#" class="btn btn-info btn-sm" title="Leave Chat Group"><i class="fa fa-sign-out"></i></a>
                <a href="#" class="btn btn-danger btn-sm" title="Delete Chat Group"><i class="fa fa-trash"></i></a>
            </div>
        </div> -->
        <div class="chat-footer border-top hide">
            <input type="text" class="chat-filter" id="searchUsers" placeholder="Search to Add">
            <div class="symbl"><i class="fa fa-search"></i></div>
        </div>
    </div>
</div>
<div class="modal fade chat-group-modal" id="chatGroupModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="chatModalTitle"><strong>Create Group Chat</strong></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <input type="text" class="name" id="groupChatName" placeholder="Name Your Group">
                <input type="hidden" name="groupID" id="groupID" value="">
                <div class="col-md-12">
                    <div class="row p-0">
                        <div class="col-md-7 p-0">
                            <!-- Search Autocomplete -->
                            <div class="col-md-12" style="padding: 10px;">
                                <input type="text" id="autoAddGroup" class="form-control" placeholder="Search for people to add">
                            </div>
                            <div class="col-md-12" style="border-top: 1px solid #d2d6de;">
                                <ul id="createGroupUsers" style="overflow-y:auto;max-height:400px;">

                                    <!-- <li>
                                        <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                        <label>Miya</label>
                                        <input class="pull-right" type="checkbox" checked>
                                    </li>
                                    <li>
                                        <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                        <label>Franco</label>
                                        <input class="pull-right" type="checkbox" checked>
                                    </li>
                                    <li>
                                        <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                        <label>Layla</label>
                                        <input class="pull-right" type="checkbox" >
                                    </li>
                                    <li>
                                        <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                        <label>Moscov</label>
                                        <input class="pull-right" type="checkbox" >
                                    </li>
                                    <li>
                                        <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                        <label>Alpha</label>
                                        <input class="pull-right" type="checkbox" >
                                    </li> -->
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5" style="background: #f5f5f5;; ">
                            <span class="title" id="createGroupTitle">SELECTED</span>
                            <span class="title" id="editGroupTitle" style="display:none;">MEMBERS</span>
                            <ul id="addedUsers" style="overflow-y:auto;max-height:400px;">
                                <!--  <li>
                                    <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                    <label>Miya</label>
                                </li>
                                <li>
                                    <img class="" src="https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_960_720.png" alt="User1 Image" />
                                    <label>Franco</label>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" id="createGroup" class="btn btn-default btn-save" data-dismiss="modal">Create</a>
                <a href="javascript:void(0);" id="editGroup" class="btn btn-default btn-save" data-dismiss="modal"
                    style="display:none;">Save</a>
            </div>
        </div>

    </div>
</div>