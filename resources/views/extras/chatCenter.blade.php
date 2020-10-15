@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Chat Center
            <a href="#" class="chat-menu-add" data-toggle="modal" data-target="#groupChatModal" title="Create Group">
                <img class="create-group-center" src="{{ asset('images/contact-request.png') }}">                
            </a>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#">Dashboard</a></li>
            <li>Extras</li>
            <li class="active">Chat Center</li>
        </ol>
        <div class="dotted-hr"></div>
    </section>

    <section class="container-fluid" style="margin-top:-5px;">
        <div class="col-md-12">
            <div class="row">
                <a href="#" class="btn btn-info btn-flat chat-users-btn"><i class="fa fa-list"></i> Chat User List</a>
                <div class="chat-user-list p-0">
                    <div class="chat-list" id="chatList">
                        <div id="newList">
                        </div>
                        <div id="onlineList">
                        </div>
                        <div id="groupList">
                        </div>
                        <div id="offlineList">
                        </div>
                    </div>
                </div>
                <div class="chat-message-container p-0">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="chat-mate-info hide" id="chatMateInfo">
                                <h4 class="chat-message-name">
                                    <span class="chat-user-img">
                                        <img class="usr-img-center usr-img" src="" width="100%">
                                    </span>            
                                    <span class="chat-user-name">MIYA</span>
                                    <span class="chat-user-status">
                                        <span class="user-status ">Online</span>
                                        <span class="separator"> | </span>
                                        <span class="user-email">miya.go3@goetu.com</span>
                                    </span>
                                    <a href="javascript:void(0);" class="info-circle1 hide" title="Info"><i class="fa fa-info-circle"></i></a>
                                    <!-- <a href="#" class="chat-menu-icon hide">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>  -->
                                </h4>
                            </div>
                            <!-- <div class="chat-info-menu hide">
                                    <ul>
                                        <li><a href="#">Add to Group</a></li>
                                        <li><a href="#">Edit Contact</a></li>
                                        <li><a href="#">View Profile</a></li>
                                        <li><a href="#">Block Contact</a></li>
                                        <li><a href="#">Remove Contact</a></li>
                                        <li><a href="#">Delete Chat</a></li>
                                    </ul>
                                </div> -->
                        </div>
                    </div>
                    <div class="chat-message">
                        <div class="col-md-12 chat-message-list" id="chatMessageList">
                            <!--  <div class="row">
                                    <div class="receiver-img online-img">
                                        <img src="assets/images/avatar-1577909_960_720.png" alt="" width="100%"/>
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
                                        <p class="scloud">iisque accommodare an eam</p>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="receiver-img online-img">
                                        <img src="assets/images/avatar-1577909_960_720.png" alt="" width="100%"/>
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
                                    <div class="receiver-img online-img">
                                        <img src="assets/images/avatar-1577909_960_720.png" alt="" width="100%"/>
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
                                        <div class="sent-status">
                                            sent
                                        </div>
                                    </div>
                                </div> -->
                        </div>
                        <div class="chat-message-box hide row">
                            <div class="col-lg-9">
                                <input type="hidden" name="messageTextID" id="messageTextID" value="">
                                <input type="hidden" name="messageTextName" id="messageTextName" value="">
                                <input type="hidden" name="messageTextMode" id="messageTextMode" value="">
                                <input type="hidden" name="messageTextMem" id="messageTextMem" value="">
                                <input type="hidden" name="messageTextImg" id="messageTextImg" value="">
                                <textarea placeholder="Type your message here..." disabled="" id="messageText"></textarea></div>
                            <!-- <a href="javascript:void(0);" class="chat-attach-file" > -->
                            <div class="btn-clip col-lg-3" title="Attach File" style="right:-50px;">
                                <label for="uploadfile">
                                    <form action="" method="POST" role="form" id="uploadAttachment" enctype="multipart/form-data">
                                        <i class="fa fa-paperclip"></i>
                                        <!-- </a> -->
                                        <input type="file" class="uploadfile" name="uploadfile" id="uploadfile" style="display:none"
                                            multiple>
                                    </form>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
<div class="modal fade chat-group-modal" id="groupChatModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="chatModalTitle1"><strong>Create Group Chat</strong></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <input type="text" class="name" id="groupChatName1" placeholder="Name Your Group">
                <input type="hidden" name="groupID1" id="groupID1" value="">
                <div class="col-md-12">
                    <div class="row p-0">
                        <div class="col-md-7 p-0">
                            <!-- Search Autocomplete -->
                            <div class="col-md-12" style="padding: 10px;">
                                <input type="text" id="autoAddGroup1" class="form-control" placeholder="Search for people to add">
                            </div>
                            <div class="col-md-12" style="border-top: 1px solid #d2d6de;">
                                <ul id="createGroupUsers1" style="overflow-y:auto;max-height:400px;">
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5" style="background: #f5f5f5;; ">
                            <span class="title" id="createGroupTitle1">SELECTED</span>
                            <span class="title" id="editGroupTitle1" style="display:none;">MEMBERS</span>
                            <ul id="addedUsers1" style="overflow-y:auto;max-height:400px;">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" id="createGroup1" class="btn btn-default btn-save" data-dismiss="modal">Create</a>
                <a href="javascript:void(0);" id="editGroup1" class="btn btn-default btn-save" data-dismiss="modal"
                    style="display:none;">Save</a>
            </div>
        </div>

    </div>
</div>
@endsection
@section('script')
<script src="{{ config("app.cdn") . "/js/extras/chatCenter.js" . "?v=" . config("app.version") }}"></script>
<script>
    $('.user-item').hover(function () {
        var s_wid = $(window).width();
        if (s_wid < 860) {
            $(this).find('.chat-name').fadeIn('very fast');
        }
    });
    $('.user-item').mouseleave(function () {
        var s_wid = $(window).width();
        if (s_wid < 860) {
            $(this).find('.chat-name').fadeOut('very fast');
        }
    });
    $(window).resize(function () {
        var o_wid = $(this).width();
        if (o_wid > 860) {
            $('.chat-name').removeAttr('style');
        }
    });
    $('.chat-menu-icon').click(function () {
        if ($(this).hasClass('chat-icon-active')) {
            $(this).removeClass('chat-icon-active');
            $('.chat-info-menu').css('display', 'block');
        } else {
            $(this).addClass('chat-icon-active');
            $('.chat-info-menu').removeAttr('style');
        }
    });
</script>
@endsection