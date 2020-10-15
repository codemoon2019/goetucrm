import swal from "sweetalert2";
/**
 *  INITIALIZATION
 */
var database = firebase.database();
var storage = firebase.storage();

var cdn_url = $('#cdn_url').val();
var newUser = $('#full_name').val();
var username = $('#username').val();
var user_id = $('#user_id').val();
var email = $('#email').val();
var dp = $('#img_url').val();

var newuname = $('#newUsername').val();
var newuid = $('#newUserId').val();
var newuemail = $('#newEmail').val();
var newufullname = $('#newFullName').val();
var newuimg = $('#newImg').val();

var faves = '';
var checked_id = [];
var checked_name = [];
var added_mem = [];
var minus_mem = [];
var minus_mem_id = [];
var all_contacts = [];
var checked_dp = [];
var onlineChatbox = [];
var onlineUsersID = [];
var onlineGroupID = [];
var allOnlineName = [];
var allOnlineMembers = [];
var allOnlineID = [];
var allOnlineChatboxCreatorID = [];
var allArrName = [];
var allArrMembers = [];
var allArrID = [];
var allArrChatbox = [];
var allArrChatboxCreatorID = [];
var allMinimizedBox = [];
var count = 0;
var idleTime = 0;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });
    /**
     * Save recently created/updated User/Partner/Merchant/Branch to firebase database
     */
    if (newuid) {
        database.ref('users_info/' + newuid).once('value', function (snapshot) {
            database.ref('users_info/' + newuid).update({
                favorite_users: '',
                full_name: newufullname,
                status: 'offline',
                user_id: newuid,
                username: newuname,
                email: newuemail,
                groups: '',
                upline_downline: '',
                img: newuimg,
            }, function (error) {
                if (error) {
                    showWarningMessage(error);            
                }
            });
        });
        $('#newUsername').val('');
        $('#newUserId').val('');
        $('#newEmail').val('');
        $('#newFullName').val('');
        $('#newImg').val('');
    }
    /**
     * Increment the idle time counter every minute.
     */
    var idleInterval = setInterval(isActive, 60000);
    /**
     * Zero the idle timer on mouse movement
     */
    $(this).keypress(function (e) {
        idleTime = 0;
    });
    $(this).scroll(function (e) {
        idleTime = 0;
    });
    /**
     * Change status to 'offline' on logout (refresh)
     */
    database.ref('users_info/' + user_id).onDisconnect().update({
        favorite_users: '',
        full_name: newUser,
        status: 'offline',
        user_id: user_id,
        username: username,
        email: email,
        groups: '',
        upline_downline: '',
        img: dp,
    }, function (error) {
        if (error) {
            showWarningMessage(error);
        }
    });
    /**
     * Get all contacts only
     */
    if (user_id && username) {
        if (!document.getElementById('chatList')) {
            loadCreateGroupUsers();
        }
    }
    /**
     * Get logged in/out users_info for chat 
     */
    database.ref('users_info').orderByChild('full_name').on('child_changed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var mode = 'show';
        var div = document.getElementById('user_' + key);
        var container = document.createElement('div');

        if (div) {
            $('#user_' + key).remove();
        }

        if (user_id != details.user_id) {
            chatUserList(key, details.status, details.user_id, details.username, details.full_name, details.img);
            reloadNotifs();
        }
    });
    /**
     * Get new group chats available
     */
    database.ref('chat_groups').orderByChild('chat_name').on('child_changed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var box = document.getElementById('user_' + key);

        if (!details.chat_members_name.includes(newUser)) {
            $('.chatbox_p_' + key).remove();
            $('#user_' + key).remove();
        } else {
            if (!box) {
                chatUserList(key, 'group', details.chat_members_id, details.chat_members_name, details.chat_name, cdn_url + '/images/user_img/goetu-profile.png');
            }
        }
    });
    /**
     * Remove group chat on delete
     */
    database.ref('chat_groups').on('child_removed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();

        if (details.chat_members_name.includes(newUser)) {
            $('.chatbox_p_' + key).remove();
            $('#user_' + key).remove();
        }
    });
    /**
     * Create group chat
     */
    $('#createGroup').click(function () {
        var chat_name = $('#groupChatName').val();

        if (!chat_name) {
            showWarningMessage('Enter name of group!');
            return false;
        }
        if (checked_id.length < 2) {
            showWarningMessage('Group must have more than ' + checked_id.length + ' member/s');            
            return false;
        }

        var members_id = checked_id.toString();
        var members_name = checked_name.toString();
        var members_dp = checked_dp.toString();

        if (members_id == '' || members_name == '') {
            showWarningMessage('Select a user!');            
            return false;
        }

        var random_id = makeRandomId(members_id.replace(/,/g, ''));

        database.ref('chat_groups/' + random_id).update({
            creator: user_id,
            chat_name: chat_name,
            chat_members_id: members_id + ',' + user_id,
            chat_members_name: members_name + ',' + newUser,
            chat_dps: members_dp + ',' + dp,
        });

        $('#groupChatName').val('');
        $('#autoAddGroup').val('');
        checked_id = [];
        checked_name = [];
        checked_dp = [];
        added_mem = [];
        minus_mem = [];
        minus_mem_id = [];
        reloadUserList('addedUsers');
        reloadUserList('createGroupUsers');
        loadCreateGroupUsers();
        showUserDiv(chat_name, members_id + ',' + user_id, random_id, 'group');
    });
    /**
     * Close modal for creating group chat
     */
    $('#chatGroupModal').on('hidden.bs.modal', function (e) {
        e.preventDefault();
        // loadCreateGroupUsers();
        $('#createGroupUsers').find('input').prop('checked', false);
        reloadUserList('addedUsers');
        document.getElementById('chatModalTitle').innerHTML = '<strong>Create Group Chat</strong>';
        $('#groupChatName').val('');
        $('#createGroupTitle').removeAttr('style');
        $('#editGroupTitle').css('display', 'none');
        $('#editGroup').css('display', 'none');
        $('#createGroup').removeAttr('style');
        $('#groupID').val('');
        checked_id = [];
        checked_name = [];
        checked_dp = [];
    })
    /**
     * List of users that can add to group chat
     */
    $('#autoAddGroup')
        .on("keydown", function (event) {
            if (event.keyCode == '8') {
                loadCreateGroupUsers();
            }
        })
        .autocomplete({
            minLength: 3,
            source: function (request, response) {
                $.getJSON("/extras/chats/addToGroup", {
                    contact: request.term
                }, function (data) {
                    reloadUserList('createGroupUsers');
                    var data = data.data;
                    $('#autoAddGroup').removeClass('ui-autocomplete-loading');
                    if (data != 'No results found.') {
                        for (var i = data.length - 1; i >= 0; i--) {
                            var list = document.createElement('li');
                            var user_list = document.getElementById('createGroupUsers');
                            var add_list = document.getElementById('add_' + data[i].id);
                            if (!add_list) {
                                if (checked_id.includes(data[i].id)) {
                                    var user_item = '<li>\
                                        <img class="usr-img" src="' + data[i].image + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="User Image" />\
                                        <label class="user-name">' + data[i].name + '<span><small>\
                                        <i>&nbsp;' + data[i].email_address + '</i></small></span</label>\
                                        <input class="pull-right create_group_users" id="add_users" name="add_users[]" type="checkbox" value="' + data[i].id + '" data-name="' + data[i].name + '" onclick="createGroup();" checked>\
                                        </li>';
                                } else {
                                    var user_item = '<li>\
                                        <img class="usr-img" src="' + data[i].image + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="User Image" />\
                                        <label class="user-name">' + data[i].name + '<span><small><i>&nbsp;' + data[i].email_address + '</i></small></span</label>\
                                        <input class="pull-right create_group_users" id="add_users" name="add_users[]" type="checkbox" value="' + data[i].id + '" data-name="' + data[i].name + '" onclick="createGroup();">\
                                        </li>';
                                }
                            }
                            list.innerHTML = user_item;
                            add_list = list.firstChild;
                            add_list.setAttribute('id', 'add_' + data[i].id);
                            user_list.appendChild(add_list);
                        }
                    } else {
                        var list = document.createElement('li');
                        var user_list = document.getElementById('createGroupUsers');
                        var add_list = document.getElementById('noResult');
                        var user_item = '<li>\
                            <label>' + data + '</label>\
                            </li>';
                        list.innerHTML = user_item;
                        add_list = list.firstChild;
                        add_list.setAttribute('id', 'noResult');
                        user_list.appendChild(add_list);
                    }
                });
            },
            focus: function () {
                return false;
            }
        });
    /**
     * Search name to add as contact
     */
    $('#searchUsers')
        .on("keydown", function (event) {
            if (event.keyCode == '8') {
                SaveData();
            }
        })
        .autocomplete({
            minLength: 3,
            source: function (request, response) {
                $.getJSON("/extras/chats/addAsContact", {
                    add_users: request.term
                }, function (data) {
                    $('#favorites').addClass('hide');
                    $('#online').addClass('hide');
                    $('#groupconvo').addClass('hide');
                    $('#offline').addClass('hide');
                    $('#searchedUsers').removeClass('hide');
                    var data = data.data;
                    var mode = 'add';
                    $('#searchUsers').removeClass('ui-autocomplete-loading');
                    if (data != 'No results found.') {
                        reloadUserList('search-list');
                        for (var i = data.length - 1; i >= 0; i--) {
                            var container = document.createElement('div');
                            var user_list = document.getElementById('search-list');
                            var add_list = document.getElementById('user_' + data[i].id);
                            if (!add_list) {
                                var user_item = '<div class="user-item">\
                                    <div onclick="showUserDiv(\'' + data[i].name + '\',\'' + data[i].id + '\',\'' + data[i].id + '\',\'' + mode + '\');">\
                                    <div class="user-img ">\
                                    <img class="usr-img" src="' + data[i].image + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="" width="100%"/>\
                                    </div>\
                                    <div class="chat-name">\
                                    <div class="title">' + data[i].name + '</div>\
                                    <span class="">' + data[i].email_address + '</span>\
                                    </div>\
                                    </div>\
                                    </div>';

                                container.innerHTML = user_item;
                                add_list = container.firstChild;
                                add_list.setAttribute('id', 'user_' + data[i].id);
                                user_list.appendChild(add_list);
                            }
                        }
                    } else {
                        reloadUserList('search-list');
                        var container = document.createElement('div');
                        var user_list = document.getElementById('search-list');
                        var add_list = document.getElementById('noResult');
                        if (!add_list) {
                            var user_item = '<div>\
                                <label>' + data + '</label>\
                                </div>';
                            container.innerHTML = user_item;
                            add_list = container.firstChild;
                            add_list.setAttribute('id', 'noResult');
                            user_list.appendChild(add_list);
                        }
                    }
                });
            },
            focus: function () {
                return false;
            },
        });
    /**
     * Add/remove user to existing group chat
     */
    $('#editGroup').click(function () {
        $('#chat_info_' + $('#groupID').val()).remove();
        var date = new Date;
        var date_split = date.toString().split(' ');
        var date_string = date_split[0] + ', ' + date_split[1] + ' ' + date_split[2] + ', ' + date_split[3];

        database.ref('chat_groups/' + $('#groupID').val()).update({
            chat_name: $('#groupChatName').val(),
            chat_members_id: checked_id.toString(),
            chat_members_name: checked_name.toString(),
            chat_dps: checked_dp.toString(),
        });
        if (added_mem) {
            for (var i = added_mem.length - 1; i >= 0; i--) {
                database.ref('chat_messages/' + $('#groupID').val()).push({
                    notification: added_mem[i] + ' joined the group.',
                    is_read: 0,
                    sender: user_id,
                    sender_name: $('#groupChatName').val(),
                    recipient: $('#groupID').val(),
                    date: date_string,
                    time: date.toLocaleTimeString(),
                    img: dp,
                }, function (error) {
                    if (error) {
                        showWarningMessage(error);            
                    }
                });
            }
        }
        if (minus_mem) {
            for (var i = minus_mem.length - 1; i >= 0; i--) {
                database.ref('chat_messages/' + $('#groupID').val()).push({
                    notification: minus_mem[i] + ' was removed from the group.',
                    is_read: 0,
                    sender: user_id,
                    sender_name: $('#groupChatName').val(),
                    recipient: $('#groupID').val(),
                    date: date_string,
                    time: date.toLocaleTimeString(),
                    img: dp,
                }, function (error) {
                    if (error) {
                        showWarningMessage(error);                                   
                    }
                });
            }
            for (var i = minus_mem_id.length; i >= 0 ; i--) {
                database.ref('notifications/' + minus_mem_id[i]).child($('#groupID').val()).remove();
            }
        }
        $('#groupChatName').val('');
        $('#autoAddGroup').val('');
        checked_id = [];
        checked_name = [];
        added_mem = [];
        minus_mem = [];
        reloadUserList('addedUsers');
        // reloadUserList('createGroupUsers');
    });

    $('.ui-autocomplete.ui-menu').css('fontSize', '12px');
    $('.ui-autocomplete').css('z-index', '2000');
    /**
     * Listen to un/read message notification
     */
    database.ref('notifications/' + user_id).on('child_changed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var div = document.getElementById('chatusers_' + user_id);
        var count = $("#new-list div.user-item").length;

        if (allOnlineID.indexOf(key.toString()) == -1 
            || allMinimizedBox.indexOf(key.toString()) != -1 
            && !$('#messageTextID').val()) {

            if ($('#notif-count-' + key).length > 0) {
                $('#notif-count-' + key).removeClass('hide');
                div.querySelector('#notif-count-' + key).innerHTML = details.count;
            }

            $('.notif-count').removeClass('hide');
            div.querySelector('.notif-count').innerHTML = count;

            var user_div = document.getElementById('user_' + key);
            if (user_div) {
                user_div.setAttribute('data-key', key);
                $('#user_' + key).prependTo('#new-list').hide().show('slow');
            }
        } else {
            database.ref('notifications/' + user_id).child(key).remove();
        }
    });
    database.ref('notifications/' + user_id).on('child_removed', function (snapshot) {
        count--;
        var div = document.getElementById('chatusers_' + user_id);
        div.querySelector('#notif-count').innerHTML = count;

        if (count < 1) {
            $('.notif-count').addClass('hide');
        }
    });
});
/**
 * Change status to 'online' on login
 */
function goOnline() {
    database.goOnline();
    database.ref('users_info/' + user_id).once('value', function (snapshot) {
        chatOnline(snapshot);
    });
    resetChatList('online');
}
/**
 * Listen to new message notification
 */
function getUnread() {
    database.ref('notifications/' + user_id).on('child_added', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var div = document.getElementById('chatusers_' + user_id);

        if (allOnlineID.indexOf(key.toString()) == -1 
            || allMinimizedBox.indexOf(key.toString()) != -1 
            && !$('#messageTextID').val()) {

            if ($('#notif-count-' + key).length > 0) {
                $('#notif-count-' + key).removeClass('hide');
                div.querySelector('#notif-count-' + key).innerHTML = details.count;
            }

            $('.notif-count').removeClass('hide');
            count++;
            div.querySelector('.notif-count').innerHTML = count;

            var user_div = document.getElementById('user_' + key);
            if (user_div) {
                user_div.setAttribute('data-key', key);
                $('#user_' + key).prependTo('#new-list').hide().show('slow');
            }
        } else {
            database.ref('notifications/' + user_id).child(key).remove();
        }
    });
}
/**
 *  Show listing on chat box
 */
function SaveData() {
    $('#favorites').removeClass('hide');
    $('#online').removeClass('hide');
    $('#groupconvo').removeClass('hide');
    $('#offline').removeClass('hide');
    $('#searchedUsers').addClass('hide');
}
/**
 *  Highlight unread chat on chat box
 */
function reloadNotifs() {
    var count = 0;
    database.ref('notifications/' + user_id).on('child_added', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var div = document.getElementById('chatusers_' + user_id);

        if (allOnlineID.indexOf(key.toString()) == -1
            || allMinimizedBox.indexOf(key.toString()) != -1) {

            if ($('#notif-count-' + key).length > 0) {
                $('#notif-count-' + key).removeClass('hide');
                div.querySelector('#notif-count-' + key).innerHTML = details.count;
            }
            
            count++;
            div.querySelector('.notif-count').innerHTML = count;
            var user_div = document.getElementById('user_' + key);
            if (user_div) {
                user_div.setAttribute('data-key', key);
            }
        } else {
            database.ref('notifications/' + user_id).child(snapshot.key).remove();
        }
    });
}
/**
 *  Get all users on contact list
 */
function loadCreateGroupUsers() {
    $.ajax({
        type: 'GET',
        url: '/extras/chats/getPreloadUsers',
        dataType: 'json',
        success: function (data) {
            $('#autoAddGroup').removeClass('ui-autocomplete-loading');

            $.each(data.data, function(index, value){
                var full_name = value.full_name;
                var user_id = value.id;
                var email_address = value.email;
                var user_image = value.image;

                var list = document.createElement('li');
                var user_list = document.getElementById('createGroupUsers');
                var add_list = document.getElementById('add_' + user_id);

                var user_item = '<li>\
                    <img class="usr-img" src="' + user_image + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="User Image" />\
                    <label class="user-name">' + full_name + '<span><small>\
                    <i>&nbsp;' + email_address + '</i></small></span></label>\
                    <input class="pull-right create_group_users" id="add_users" name="add_users[]" type="checkbox" value="' + user_id + '" data-name="' + full_name + '" onclick="createGroup();">\
                    </li>';

                list.innerHTML = user_item;
                add_list = list.firstChild;
                add_list.setAttribute('id', 'add_' + user_id);
                user_list.appendChild(add_list);
                all_contacts.push(user_id);
            });

            goOnline();
            getUnread();
        }
    });
}
/**
 *  Createt group chat
 */
function createGroup() {
    $("input[name='add_users[]']").change(function () {
        var user_id = parseInt($(this).attr('value'));
        var full_name = $(this).attr('data-name');
        var img = $('#add_' + user_id).find('img').attr('src');

        if ($(this).is(':checked')) {
            var user_list = document.getElementById('addedUsers');
            var list = document.createElement('li');
            var add_list = document.getElementById('added_' + user_id);
            if (!checked_id.includes(user_id)) {
                checked_id.push(user_id);
                checked_name.push(full_name);
                checked_dp.push(img);
                added_mem.push(full_name);
            }
            if (!add_list) {
                var user_item = '<li>\
                    <img class="usr-img" src="' + img + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="User Image" />\
                    <label class="user-name">' + full_name + '</label>\
                    </li>';
                list.innerHTML = user_item;
                add_list = list.firstChild;
                add_list.setAttribute('id', 'added_' + user_id);
                user_list.appendChild(add_list);
            }
        } else {
            var index = checked_id.indexOf(parseInt(user_id));
            var index_n = checked_name.indexOf(full_name);
            var index_p = checked_dp.indexOf(img);
            if (index >= 0) {
                checked_id.splice(index, 1);
            }
            if (index >= 0) {
                checked_name.splice(index_n, 1);
            }
            if (index >= 0) {
                checked_dp.splice(index_p, 1);
            }
            if (!checked_id.includes(user_id) 
                && !minus_mem.includes(full_name)) {
                minus_mem.push(full_name);
                minus_mem_id.push(user_id);
            }
            $('#added_' + user_id).remove();
        }
    });
    return false;
}
/**
 *  Show listing on chat box depending on status
 */
function chatUserList(key, status, userid, user_name, full_name, img) {
    var array = JSON.parse("[" + faves + "]");
    var arr_members = JSON.parse("[" + userid.toString() + "]");
    if (userid.toString().includes(',')) {
        var mode = 'group';
    } else {
        var mode = 'private';
    }
    var container = document.createElement('div');
    var div = document.getElementById('user_' + key);
    var user_item = '<div class="user-item" data-key="">\
        <a href="#" class="favorite_p_' + key + ' fav-star" href="javascript:void(0);" \
        onclick="addToFavorites(\'' + mode + '\',\'' + userid + '\',\'' + full_name + '\',\'' + key + '\');" >\
        <i class="fa fa-star"></i>\
        </a>\
        <div onclick="showUserDiv(\'' + full_name + '\',\'' + userid + '\',\'' + key + '\',\'' + mode + '\');">\
        <div class="user-img ">\
        <img class="usr-img" src="' + img + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="" width="100%"/>\
        </div>\
        <div class="chat-name">\
        <div class="title">' + full_name + '</div>\
        <span class=""></span>\
        </div>\
        <span id="notif-count-' + key + '" class="label-danger notif-counts notif-count-unread hide"></span>\
        </div>\
        </div>';

    container.innerHTML = user_item;
    div = container.firstChild;
    div.setAttribute('id', 'user_' + key);

    if (faves && array.indexOf(parseInt(key)) != -1 && (all_contacts.indexOf(parseInt(userid)) != -1 || status == 'group')) {
        var messageList = document.getElementById('favorites-list');
        messageList.appendChild(div);
        $('#user_' + key).find('a.favorite_p_' + key).addClass('fav-active');
        $('#user_' + key).find('i.fa.fa-star').addClass('text-yellow');
        $('#user_' + key).find('.user-img').addClass('fav-img');
        $('#user_' + key).find('.chat-name span').addClass('fav-span');
        // div.querySelector('.fav-span').textContent = 'Favorite';
        div.querySelector('.fav-span').textContent = user_name;
    } else if (status == 'online' && all_contacts.indexOf(parseInt(userid)) != -1) {
        var messageList = document.getElementById('online-list');
        messageList.appendChild(div);
        $('#user_' + key).find('.user-img').addClass('online-img');
        $('#user_' + key).find('.chat-name span').addClass('online-span');
        // div.querySelector('.online-span').textContent = 'Online';
        div.querySelector('.online-span').textContent = user_name;
    } else if (status == 'offline' && all_contacts.indexOf(parseInt(userid)) != -1) {
        var messageList = document.getElementById('offline-list');
        messageList.appendChild(div);
        $('#user_' + key).find('.user-img').addClass('offline-img');
        $('#user_' + key).find('.chat-name span').addClass('offline-span');
        // div.querySelector('.offline-span').textContent = 'Offline';
        div.querySelector('.offline-span').textContent = user_name;
    } else if (status == 'group' && arr_members.indexOf(parseInt(user_id)) != -1) {
        var messageList = document.getElementById('group-list');
        messageList.appendChild(div);
        $('#user_' + key).find('.user-img').addClass('busy-img');
        $('#user_' + key).find('.chat-name span').addClass('busy-span');
        div.querySelector('.busy-span').textContent = 'Group';
    }
}
/**
 *  UI on chat messages for private chat
 */
function messageList(recipient, snapshot) {
    var key = snapshot.key;
    var details = snapshot.val();
    var date = 'now';
    var newUser1 = newUser.replace(/ +/g, '');
    var div = document.getElementById(key);
    var msg = details.message_content;
    var messageInput = '';
    var messageList = '';
    var d = new Date();
    var gmtHrs = -d.getTimezoneOffset() / 60;
    var dt = new Date(details.date + ' ' + details.time);
    dt.setHours( dt.getHours() + gmtHrs);
    var new_time = dt.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    var date_split = dt.toString().split(' ');
    var date_string = date_split[0] + ', ' + date_split[1] + ' ' + date_split[2] + ', ' + date_split[3];

    if (!div && allOnlineID.indexOf(recipient.toString()) != -1) {
        var container = document.createElement('div');
        if (user_id == details.sender) { // sender
            var date_key = details.date.replace(/[\ +\,']+/g, '');
            var date_div = document.getElementById('date_' + date_key + '_' + recipient);
            if (!date_div) {
                messageInput = document.getElementById('message_p_' + details.recipient);
                messageList = document.getElementById('row_' + details.recipient + '_' + newUser1);
                container.innerHTML = '<div class="row pmsgs">\
                    <div class="date">' + date_string + '</div>\
                    </div>';
                date_div = container.firstChild;
                date_div.setAttribute('id', 'date_' + date_key + '_' + recipient);
                messageList.appendChild(date_div);
            }
            messageInput = document.getElementById('message_p_' + details.recipient);
            messageList = document.getElementById('row_' + details.recipient + '_' + newUser1);
            container.innerHTML = '<div class="row pmsgs">' +
                '<div class="chat-smsg" id="chat-smsg_' + details.recipient + '" title="' + dt.toLocaleTimeString() + '">' +
                '<p class="scloud scloud-default">' + msg + '</p>' +
                '<i class="fa fa-caret-right"></i>' +
                '<div class="sent-status sent-status-default">' +
                // jQuery.timeago(dt) +
                new_time +
                '</div>' +
                '<div class="clearfix"></div>' +
                '</div></div>';
            div = container.firstChild;
            div.setAttribute('id', key);
            messageList.appendChild(div);
        } else if (user_id == details.recipient) {
            var date_key = details.date.replace(/[\ +\,']+/g, '');
            var date_div = document.getElementById('date_' + date_key + '_' + details.sender);
            if (!date_div) {
                messageInput = document.getElementById('message_p_' + details.recipient);
                messageList = document.getElementById('row_' + details.sender + '_' + newUser1);
                container.innerHTML = '<div class="row pmsgs">\
                    <div class="date">' + date_string + '</div>\
                    </div>';
                date_div = container.firstChild;
                date_div.setAttribute('id', 'date_' + date_key + '_' + details.sender);
                messageList.appendChild(date_div);
            }
            messageInput = document.getElementById('message_p_' + details.sender);
            messageList = document.getElementById('row_' + details.sender + '_' + newUser1);
            container.innerHTML = '<div class="row pmsgs">' +
                '<div class="receiver-img receiver-img-' + details.sender + ' ">' +
                '<img class="usr-img usr-img-' + details.sender + '" src="' + details.img + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="" width="100%"/>' +
                '<div class="clearfix"></div>' +
                '</div>' +
                '<div class="chat-msg chat-msg-default" id="chat-msg_' + details.sender + '" title="' + dt.toLocaleTimeString() + '">' +
                '<i class="fa fa-caret-left"></i>' +
                '<p class="rcloud rcloud-default">' + msg + '</p>' +
                '<div class="sent-status sent-status-default">' +
                // jQuery.timeago(dt) +
                new_time +
                '</div>' +
                '<div class="clearfix"></div>' +
                '</div></div>';
            div = container.firstChild;
            div.setAttribute('id', key);
            messageList.appendChild(div);
        }
    }
    if (allOnlineID.indexOf(recipient.toString()) != -1) {
        if (user_id == details.sender) {
            var messageElement = div.querySelector('.scloud');
        } else if (user_id == details.recipient) {
            var messageElement = div.querySelector('.rcloud');
        }

        if (msg.startsWith('gs:') || msg.startsWith('https:')) {
            var ext = msg.split('.').pop();
            var fileName = msg.split('/').pop();

            if (msg.match(/\.(jpeg|jpg|gif|png)$/)) { // If the message is an image.
                var file = document.createElement('a');
                var image = document.createElement('img');
                image.src = 'https://www.google.com/images/spin-32.gif'; // Display a loading image first.
                storage.refFromURL(msg).getDownloadURL().then(function (metadata) {
                    image.src = metadata;
                    image.style = 'width:100%;border-radius:4px';
                    file.href = metadata;
                    // file.target = '_blank';
                    file.appendChild(image);
                });
                file.setAttribute('data-fancybox', 'group');
                file.setAttribute('class', 'fancybox');

                messageElement.innerHTML = '';
                messageElement.appendChild(file);

                $('.fancybox').fancybox();
            } else if (msg.match(/\.(mp4|ogg|webm)$/)) { // If the message is a video.
                var video = document.createElement('video');
                var source = document.createElement('source');
                video.poster = 'https://www.google.com/images/spin-32.gif'; // Display a loading image first.
                storage.refFromURL(msg).getDownloadURL().then(function (metadata) {
                    video.setAttribute('controls', true);
                    video.poster = '';
                    video.src = metadata;
                    video.type = 'video/' + ext;
                    video.width = '150';
                    video.height = '100';
                    source.src = metadata;
                    source.type = 'video/' + ext;
                    video.appendChild(source);
                });
                messageElement.innerHTML = '';
                messageElement.appendChild(video);
            } else { // If the message is a file.
                var file = document.createElement('a');
                var fa_file = document.createElement('i');
                file.href = 'https://www.google.com/images/spin-32.gif';
                storage.refFromURL(msg).getDownloadURL().then(function (metadata) {
                    file.href = metadata;
                    file.target = '_blank';
                    fa_file.setAttribute('class', 'fa fa-file');
                    file.append(fileName);
                    file.appendChild(fa_file);
                });
                messageElement.innerHTML = '';
                messageElement.appendChild(file);
            }
        } else { // If the message is text.
            messageElement.textContent = msg;
            // Replace all line breaks by <br>.
            messageElement.innerHTML = messageElement.innerHTML.replace(/\n/g, '<br>');
        }

        // Show the card fading-in and scroll to view the new message.
        setTimeout(function () {
            $('div.pmsgs').slice(-12).show();
            if (user_id == details.sender) {
                $('.chat-body_p_' + details.recipient).scrollTop($('.chat-body_p_' + details.recipient)[0].scrollHeight);
            } else if (user_id == details.recipient) {
                $('.chat-body_p_' + details.sender).scrollTop($('.chat-body_p_' + details.sender)[0].scrollHeight);
            }
        }, 1);
        //
    }
}
/**
 *  UI on chat messages for group chat
 */
function messageListGroup(recipient, snapshot) {
    var key = snapshot.key;
    var details = snapshot.val();
    var date = 'now';
    var newUser1 = newUser.replace(/ +/g, '');
    var date_key = details.date.replace(/[\ +\,']+/g, '');
    var div = document.getElementById(key);
    var div1 = document.getElementById('date_' + date_key + '_' + recipient);
    var messageInput = '';
    var messageList = '';
    var d = new Date();
    var gmtHrs = -d.getTimezoneOffset() / 60;
    var dt = new Date(details.date + ' ' + details.time);
    dt.setHours( dt.getHours() + gmtHrs);
    var new_time = dt.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    var date_split = dt.toString().split(' ');
    var date_string = date_split[0] + ', ' + date_split[1] + ' ' + date_split[2] + ', ' + date_split[3];

    if (recipient == details.recipient || user_id == details.sender) {
        if (!div && allOnlineID.indexOf(recipient.toString()) != -1) {
            var date_div = document.getElementById('date_' + date_key + '_' + recipient);
            var container = document.createElement('div');
            if (!date_div) {
                messageInput = document.getElementById('message_p_' + details.recipient);
                messageList = document.getElementById('row_' + details.recipient + '_' + newUser1);
                container.innerHTML = '<div class="row msgs">\
                    <div class="date">' + date_string + '</div>\
                    </div>';
                date_div = container.firstChild;
                date_div.setAttribute('id', 'date_' + date_key + '_' + recipient);
                messageList.appendChild(date_div);
            }
            if (details.notification) {
                messageInput = document.getElementById('message_p_' + details.recipient);
                messageList = document.getElementById('row_' + details.recipient + '_' + newUser1);
                container.innerHTML = '<div class="row msgs">\
                    <div class="date">' + details.notification + '</div>\
                    </div>';
                div = container.firstChild;
                div.setAttribute('id', key);
                if (messageList) {
                    messageList.appendChild(div);
                }
            }
            if (details.message_content) {
                if (user_id == details.sender) { // sender
                    messageInput = document.getElementById('message_p_' + details.recipient);
                    messageList = document.getElementById('row_' + details.recipient + '_' + newUser1);
                    container.innerHTML = '<div class="row msgs">' +
                        '<div class="chat-smsg" id="chat-smsg_' + details.recipient + '" title="' + dt.toLocaleTimeString() + '">' +
                        '<p class="scloud scloud-default"></p>' +
                        '<i class="fa fa-caret-right"></i>' +
                        '<div class="sent-status sent-status-default">' +
                        // jQuery.timeago(dt) +
                        new_time +
                        '</div>' +
                        '<div class="clearfix"></div>' +
                        '</div></div>';
                    div = container.firstChild;
                    div.setAttribute('id', key);
                    messageList.appendChild(div);
                } else if (details.recipient == recipient) {
                    messageInput = document.getElementById('message_p_' + details.recipient);
                    messageList = document.getElementById('row_' + details.recipient + '_' + newUser1);
                    container.innerHTML = '<div class="row msgs"><span class="rcloud-name rcloud-name-default"><small>' + details.sender_name + '</small></span></br>' + 
                        '<div class="receiver-img receiver-img-' + details.sender + ' ">' +
                        '<img class="usr-img usr-img-' + details.sender + '" src="' + details.img + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="" width="100%"/>' +
                        '<div class="clearfix"></div>' +
                        '</div>' +
                        '<div class="chat-msg chat-msg-default" id="chat-msg_' + details.sender + '" title="' + dt.toLocaleTimeString() + '">' +
                        '<i class="fa fa-caret-left"></i>' +
                        '<p class="rcloud rcloud-default">' + 
                        '</p>' +
                        '<div class="sent-status sent-status-default">' +
                        // jQuery.timeago(dt) +
                        new_time +
                        '</div>' +
                        '<div class="clearfix"></div>' +
                        '</div></div>';
                    div = container.firstChild;
                    div.setAttribute('id', key);
                    messageList.appendChild(div);
                }
            }
        }
        if (allOnlineID.indexOf(recipient.toString()) != -1) {
            if (user_id == details.sender) {
                var messageElement = div.querySelector('.scloud');
            } else if (recipient == details.recipient) {
                var messageElement = div.querySelector('.rcloud');
            }
            if (details.message_content) {
                if (details.message_content.startsWith('gs:') || details.message_content.startsWith('https:')) {
                    var ext = details.message_content.split('.').pop();
                    var fileName = details.message_content.split('/').pop();
                    if (details.message_content.match(/\.(jpeg|jpg|gif|png)$/)) { // If the message is an image.
                        var file = document.createElement('a');
                        var image = document.createElement('img');
                        image.src = 'https://www.google.com/images/spin-32.gif'; // Display a loading image first.
                        storage.refFromURL(details.message_content).getDownloadURL().then(function (metadata) {
                            image.src = metadata;
                            image.style = 'width:100%;border-radius:4px';
                            file.href = metadata;
                            // file.target = '_blank';
                            file.appendChild(image);
                        });
                        file.setAttribute('data-fancybox', 'group');
                        file.setAttribute('class', 'fancybox');

                        messageElement.innerHTML = '';
                        messageElement.appendChild(file);

                        $('.fancybox').fancybox();
                    } else if (details.message_content.match(/\.(mp4|ogg|webm)$/)) { // If the message is a video.
                        var video = document.createElement('video');
                        var source = document.createElement('source');
                        video.poster = 'https://www.google.com/images/spin-32.gif'; // Display a loading image first.
                        storage.refFromURL(details.message_content).getDownloadURL().then(function (metadata) {
                            video.setAttribute('controls', true);
                            video.poster = '';
                            video.src = metadata;
                            video.type = 'video/' + ext;
                            video.width = '150';
                            video.height = '100';
                            source.src = metadata;
                            source.type = 'video/' + ext;
                            video.appendChild(source);
                        });
                        messageElement.innerHTML = '';
                        messageElement.appendChild(video);
                    } else { // If the message is a file.
                        var file = document.createElement('a');
                        var fa_file = document.createElement('i');
                        file.href = 'https://www.google.com/images/spin-32.gif';
                        storage.refFromURL(details.message_content).getDownloadURL().then(function (metadata) {
                            file.href = metadata;
                            file.target = '_blank';
                            fa_file.setAttribute('class', 'fa fa-file');
                            file.append(fileName);
                            file.appendChild(fa_file);
                        });
                        messageElement.innerHTML = '';
                        messageElement.appendChild(file);
                    }
                } else { // If the message is text.
                    messageElement.innerHTML = details.message_content;
                    // Replace all line breaks by <br>.
                    messageElement.innerHTML = messageElement.innerHTML.replace(/\n/g, '<br>');
                }
            }

        }
        // Show the card fading-in and scroll to view the new message.
        setTimeout(function () {
            $('div.msgs').slice(-12).show();
            if (user_id == details.sender) {
                $('.chat-body_p_' + details.recipient).scrollTop($('.chat-body_p_' + details.recipient)[0].scrollHeight);
            } else if (user_id == details.recipient) {
                $('.chat-body_p_' + details.sender).scrollTop($('.chat-body_p_' + details.sender)[0].scrollHeight);
            }
        }, 1);
        //
    }
}
/**
 *  Update firebase db to 'offline' on logout
 */
function chatOffline() {
    database.ref('users_info/' + user_id).onDisconnect().update({
        full_name: newUser,
        status: 'offline',
        img: dp,
    }, function (error) {
        if (error) {
            showWarningMessage(error);            
        }
    });
    database.goOffline();
    $('#is_online').val('0');
}
/**
 *  Update firebase db to 'online' on login
 */
function chatOnline(snapshot) {
    if (snapshot.exists()) {
        database.ref('users_info/' + user_id).update({
            full_name: newUser,
            status: 'online',
            img: dp,
        }, function (error) {
            if (error) {
                showWarningMessage(error);            
            }
        });
    } else {
        database.ref('users_info/' + user_id).update({
            favorite_users: '',
            full_name: newUser,
            status: 'online',
            user_id: user_id,
            username: username,
            email: email,
            groups: '',
            upline_downline: '',
            img: dp,
        }, function (error) {
            if (error) {
                showWarningMessage(error);            
            }
        });
    }
    $('#is_online').val('1');
}
/**
 *  Check if active to update user status
 */
function isActive() {
    idleTime = idleTime + 1;
    if (idleTime > 29 && $('#is_online').val() == '1') { // 30 minutes
        chatOffline();
    } else if (idleTime <= 1 && $('#is_online').val() == '0') {
        database.goOnline();
        database.ref('users_info/' + user_id).update({
            full_name: newUser,
            status: 'online',
            img: dp,
        }, function (error) {
            if (error) {
                showWarningMessage(error);            
            }
        });
        $('#is_online').val('1');
    }
}
/**
 *  Get all to list on chat box
 */
function resetChatList(status) {
    database.ref('users_info/' + user_id + '/favorite_users').once('value', function (snapshot) {
        faves = snapshot.val();
    });

    database.ref('users_info').orderByChild('full_name').on('child_added', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var mode = 'show';
        var div = document.getElementById('user_' + key);
        var container = document.createElement('div');

        if (!div && user_id != details.user_id) {
            chatUserList(key, details.status, details.user_id, details.username, details.full_name, details.img);
        }
    });

    database.ref('chat_groups').orderByChild('chat_name').on('child_added', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var mode = 'show';
        var div = document.getElementById('user_' + key);
        var container = document.createElement('div');

        if (!div && user_id != details.user_id) {
            chatUserList(key, 'group', details.chat_members_id, details.chat_members_name, details.chat_name, cdn_url + '/images/user_img/goetu-profile.png');
        }
    });
}
/**
 *  Add to favorites
 */
function addToFavorites(status, fave_user_id, fave_username, key) {
    var favorites = '';

    database.ref('users_info/' + user_id + '/favorite_users').once('value', function (snapshot) {
        favorites = snapshot.val();
    });

    var updatedString = '';

    var array = JSON.parse("[" + favorites + "]");

    if (status == 'group') {
        var id = key;
    } else {
        var id = fave_user_id;
    }
    var n = array.indexOf(parseInt(id));

    $('#user_' + id).remove();

    if (n >= 0) { // unfavorite a user
        array.splice(n, 1);
        updatedString = array.toString();
        $('.favorite_p_' + id).removeClass('text-yellow');
        $('.favorite_p_' + id).find('i.fa.fa-star').removeClass("text-yellow");
    } else { // favorite a user
        if (!favorites) {
            updatedString = id;
        } else {
            updatedString = favorites + ',' + id;
        }
        $('.favorite_p_' + id).addClass('text-yellow');
        $('.favorite_p_' + id).find('i.fa.fa-star').addClass("text-yellow");
    }

    database.ref('users_info/' + user_id).update({
        favorite_users: updatedString
    });

    resetChatList('favorites');
}
/**
 *  Pre-show chatbox
 */
function showUserDiv(name, members_id, id, mode) {
    var creator_id = members_id;
    var members = name;
    var div = document.getElementById('chatusers_' + user_id);

    if ($('#user_' + id).attr('data-key') && $('#notif-count-' + id).length > 0) {
        database.ref('notifications/' + user_id).child(id).remove();
        div.querySelector('#notif-count-' + id).innerHTML = 0;
        $('#notif-count-' + id).addClass('hide');
    }

    if (members_id.includes(',')) {
        var found = checkAvailability(onlineGroupID, id.toString());
        mode = 'group';
    } else {
        var found = checkAvailability(onlineUsersID, id.toString());
        if (mode == 'private') {
            mode = 'private';
        } else {
            mode = 'add';
        }
    }

    if (!found) {
        if (members_id.includes(',')) {
            onlineGroupID.push(id.toString());
        } else {
            onlineUsersID.push(id.toString());
        }

        allOnlineName.push(name);
        allOnlineMembers.push(members);
        allOnlineID.push(id);
        allOnlineChatboxCreatorID.push(members_id);
        onlineChatbox.push(id);

        if ($(window).width() >= 1280) {
            if (allOnlineID.length <= 3) {
                showChatbox(name, id, members, mode, members_id, creator_id);
            } else {
                showMoreChatbox(name, id, members, mode, members_id, creator_id);
            }
        } else if ($(window).width() < 1280) {
            if (allOnlineID.length <= 1) {
                minimize('chatusers');
                $('#chatusers').addClass('hide');
                $('.chatboxes_more').removeClass('hide');
                showChatbox(name, id, members, mode, members_id, creator_id);
            } else {
                showMoreChatbox(name, id, members, mode, members_id, creator_id);
            }
        }
    }
}
/**
 *  Show chatbox
 */
function showChatbox(item, index, members, mode, number, creator_id) {
    var user = newUser.replace(/ /g, '');
    var newUser1 = newUser.replace(/ +/g, '');
    var id = index;
    var name = members.replace(/ +/g, "");
    name = name.replace(/,/g, '');

    if (creator_id.includes(',')) {
        var chat = 'group';
    } else {
        if (mode == 'private') {
            var chat = 'private';
        } else {
            var chat = 'add';
        }
    }
    var temp = 'p_' + index;

    $('.chat-container').prepend(
        '<div id="box_p_' + index + ' box' + index + '" class="chatbox_p_' + index + ' chatbox">' +
        '<div  class="chat-header" >' +
        '<div class="chat-header-left" >' +
        '<div class="title chat-header-title" title="' + item + '" >' + item + '</div>' +
        '<div class="clearfix"></div>' +
        '</div>' +
        '<div class="chat-header-right">' +
        '<a href="javascript:void(0);" class="contact-request-' + index + '" onclick="addToContacts(\'' + index + '\');" title="Send Contact Request">'+
        '<i class="request_' + index + ' fa fa-user-plus"></i></a>' +
        '<a href="javascript:void(0);" onclick="minimize(\'' + index + '\',\'' + members + '\',\'' + number + '\',\'' + chat + '\')">' +
        '<i class="minmax_' + index + ' fa fa-chevron-down"></i>' +
        '</a>' +
        '<a href="javascript:void(0);" class="info-circle info-circle-' + index + '" onclick="boxInfo(\'' + index + '\')">'+
        '<i class="fa fa-info-circle"></i></a>' +
        '<a href="javascript:void(0);" onclick="closeChat(\'' + item + '\',\'' + number + '\',\'' + members + '\',\'' + index + '\',\'' + creator_id + '\')">' +
        '<i class="fa fa-close"></i>' +
        '</a>' +
        '<div class="clearfix"></div>' +
        '</div>' +
        '</div>' +
        '<div class="chatbody_' + index + '">' +
        '<div class="chat-body chat-body_p_' + index + '" id="chat-body_p_' + index + '">' +
        '<div class="users_checkbox" id="users_checkbox_p_' + index + '"></div>' +
        '<div class="row_p_' + index + '_' + name + '_' + newUser1 + ' col-lg-12" id="row_' + index + '_' + newUser1 + '"></div>' +
        '</div>' +
        '</div>' +
        '<div class="chat-footer chat-footer_p_' + index + ' border-top">' +
        '<textarea id="message_p_' + id + '" class="text_' + index + '" placeholder="Type your message here..."></textarea>' +
        '<div class="btn-clip" title="Attach File">' +
        '<label for="uploadfile_p_' + index + '">' +
        '<i class="fa fa-paperclip"></i>' +
        '<form action="" method="POST" role="form" id="uploadAttachment_p_' + index + '" enctype="multipart/form-data">' +
        '<input type="file" class="uploadfile_p_' + index + '" name="uploadfile_p_' + index + '" id="uploadfile_p_' + index + '" style="display:none" multiple>' +
        '</form>' +
        '</label>' +
        '</div>' +
        '</div>' +
        '</div>');

    if (chat != 'group') {
        $('.info-circle-' + index).css('display', 'none')
    }

    if (mode != 'add') {
        $('.contact-request-' + index).css('display', 'none')
    } else {
        $('#message_p_' + id).attr('placeholder', 'User must accept request before you can chat them.');
        $('#message_p_' + id).attr('disabled', true);
        $('#uploadfile_p_' + id).attr('disabled', true);
        $('.chat-footer_p_' + id).css('background-color', '#E3E3E3');
    }

    $('#message_p_' + id).focus();

    $('#message_p_' + id).keypress(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            enterMsg(item, id, members, number, creator_id, chat);
        }
    });

    $('#uploadAttachment_' + temp).change(function (event) {
        event.preventDefault();
        var attachment = document.getElementById('uploadfile_' + temp).files;
        for (var i = attachment.length - 1; i >= 0; i--) {
            var file = attachment[i];
            uploadAttachment(file, number, members, id, creator_id, chat);
        }
        document.getElementById('uploadAttachment_' + temp).reset();
    });

    $(".chat-body_p_" + index).scroll(function(){
        if ($(".chat-body_p_" + index).scrollTop() == 0){
            var type = chat == 'group' ? 'msgs' : 'pmsgs';
            if ($('div#row_' + index + '_' + newUser1).children('div.' + type + ':hidden').length != 0) {
                $('div.' + type + ':hidden').slice(-12).toggle();
                $(".chat-body_p_" + index).scrollTop($(".chat-body_p_" + index)[0].scrollHeight / 3);
            } 
        }
    });

    history(index, chat);
}
/**
 *  Retrieve all messages of a chat box
 */
function history(recipient, chat) {
    database.ref('chat_messages').off();

    if (chat == 'group') {
        // database.ref('chat_messages/' + recipient).limitToLast(12).on('child_added',function(snapshot){
        database.ref('chat_messages/' + recipient).on('child_added', function (snapshot) {
            messageListGroup(recipient, snapshot);
        });

        database.ref('chat_messages/' + recipient).on('child_changed', function (snapshot) {
            messageListGroup(recipient, snapshot);
        });

        database.ref('chat_groups/' + recipient).once('value').then(function(snapshot) {
            var details = snapshot.val();
            var mem_id = details.chat_members_id;
            var mem_id_arr = mem_id.split(',');
            for (let i = 0; i < mem_id_arr.length; i++) {
                const id = mem_id_arr[i];
                database.ref('users_info/' + id).once('value').then(function(snapshot) {
                    var details1 = snapshot.val();
                    if (details1) {
                        $('.usr-img-' + id).attr('src', details1.img);
                    }
                });
            }
        });
    } else {
        // database.ref('chat_messages/' + recipient + '-' + user_id).limitToLast(12).on('child_added',function(snapshot){
        database.ref('chat_messages/' + recipient + '-' + user_id).on('child_added', function (snapshot) {
            messageList(recipient, snapshot);
        });

        database.ref('chat_messages/' + recipient + '-' + user_id).on('child_changed', function (snapshot) {
            messageList(recipient, snapshot);
        });

        database.ref('users_info/' + recipient).once('value').then(function(snapshot) {
            var details = snapshot.val();
            $('.usr-img-' + recipient).attr('src', details.img);
        });
    }
}
/**
 *  Handle multiple chatbox
 */
function showMoreChatbox(item, index, members, mode, number, creator_id) {
    var newUser1 = newUser.replace(/ +/g, '');
    if (number.includes(',')) {
        var item = item;
        var id = index;
        var index = index.replace(/,/g, '');
        var name = members.replace(/,/g, '').replace(/ +/g, '');
        var found = checkAvailability(allOnlineID, index);
        mode = 'group';
    } else {
        var id = index;
        var name = members.replace(/ +/g, "").replace(/,/g, '');

        if (mode == 'private') {
            mode = 'private';
        } else {
            mode = 'add';
        }
    }

    $('.chatbox_p_' + allOnlineID[0].replace(/ +/g, "").replace(/,/g, '')).remove();

    allArrName.push(allOnlineName[0]);
    allArrID.push(allOnlineID[0]);
    allArrMembers.push(allOnlineMembers[0]);
    allArrChatbox.push(onlineChatbox[0]);
    allArrChatboxCreatorID.push(allOnlineChatboxCreatorID[0]);

    allOnlineName.shift();
    allOnlineID.shift();
    allOnlineMembers.shift();
    onlineChatbox.shift();
    allOnlineChatboxCreatorID.shift();

    showChatbox(item, id, members, mode, number, creator_id);
    $('.chatboxes').removeClass('hide');
    if ($(window).width() >= 1280) {
        $('.chat-other').css('right', '1100px');
        $('.chat-header-hidden').css('right', '1100px');
    } else {
        $('.chat-other').css('right', '567px');
        $('.chat-header-hidden').css('right', '566px');
    }
    appendChatbox(mode, number);
}
/**
 *  On enter a message save to firebase db
 */
function enterMsg(item, index, members, number, creator_id, chat) {
    var recipient = index;
    var sender = user_id;
    var recipient_name = members;
    var sender_name = newUser;
    var all_recipients = number.split(',');
    var date = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Etc/Greenwich' };
    var options1 = { timeZone: 'Etc/Greenwich' };

    if (index.includes(',')) {
        var index = index.replace(/,/g, '');
        var msg = $('#message_g_' + number + '_' + index).val();
    } else {
        var index = index;
        var msg = $('#message_p_' + index).val();
    }

    if (msg != '' && msg != undefined) {
        var regx = /[一-龠]+|[ぁ-ゔ]+|[ァ-ヴー]+|[a-zA-Z0-9]+|[ａ-ｚＡ-Ｚ０-９]+[々〆〤]+/u;
        if (regx.test(msg)) {
            if (chat == 'group') {
                database.ref('chat_messages/' + recipient).push({
                    message_content: msg,
                    is_read: 0,
                    sender: user_id,
                    sender_name: sender_name,
                    recipient: recipient,
                    date: date.toLocaleDateString("en-US", options),
                    time: date.toLocaleTimeString("en-US", options1),
                    img: dp,
                }, function (error) {
                    if (error) {
                        showWarningMessage(error);            
                    }
                });
                all_recipients.forEach(function (recipient_id) {
                    database.ref('notifications/' + recipient_id + '/' + recipient).once('value', function (snapshot) {
                        if (snapshot.exists()) {
                            var details = snapshot.val();
                            if (recipient_id != user_id) {
                                database.ref('notifications/' + recipient_id + '/' + recipient).update({
                                    count: details.count + 1,
                                });
                            }
                        } else {
                            if (recipient_id != user_id) {
                                database.ref('notifications/' + recipient_id + '/' + recipient).update({
                                    count: 1,
                                });
                            }
                        }
                    });
                });
            } else {
                database.ref('chat_messages/' + recipient + '-' + sender).push({
                    message_content: msg,
                    is_read: 0,
                    sender: user_id,
                    sender_name: members,
                    recipient: recipient,
                    date: date.toLocaleDateString("en-US", options),
                    time: date.toLocaleTimeString("en-US", options1),
                    img: dp,
                }, function (error) {
                    if (error) {
                        showWarningMessage(error);            
                    }
                });
                database.ref('chat_messages/' + sender + '-' + recipient).push({
                    message_content: msg,
                    is_read: 0,
                    sender: user_id,
                    sender_name: members,
                    recipient: recipient,
                    date: date.toLocaleDateString("en-US", options),
                    time: date.toLocaleTimeString("en-US", options1),
                    img: dp,
                }, function (error) {
                    if (error) {
                        showWarningMessage(error);            
                    }
                });
                database.ref('notifications/' + recipient + '/' + sender).once('value', function (snapshot) {
                    if (snapshot.exists()) {
                        var details = snapshot.val();
                        if (recipient != user_id) {
                            database.ref('notifications/' + recipient + '/' + sender).update({
                                count: details.count + 1,
                            });
                        }
                    } else {
                        if (recipient != user_id) {
                            database.ref('notifications/' + recipient + '/' + sender).update({
                                count: 1,
                            });
                        }
                    }
                });
            }
        }
    }
    $('.text_' + index).val('');
}
/**
 *  Upload attachment to save to firebase storage
 */
function uploadAttachment(attachment, number, members, id, creator_id, chat) {
    var date = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Etc/Greenwich' };
    var options1 = { timeZone: 'Etc/Greenwich' };
    var all_recipients = number.split(',');
    var file = '';

    if (attachment.type.match('image.*')) { // filter type of chat-attachments
        file = '/images/';
    } else if (attachment.type.match('application.*')) {
        file = '/applications/';
    } else if (attachment.type.match('video.*')) {
        file = '/videos/';
    } else if (attachment.type.match('text.*')) {
        file = '/texts/';
    } else {
        showWarningMessage('Invalid attachment.');            
        return false;
    }

    if (chat == 'group') {
        all_recipients.forEach(function (recipient_id) {
            database.ref('notifications/' + recipient_id + '/' + id).once('value', function (snapshot) {
                if (snapshot.exists()) {
                    var details = snapshot.val();
                    if (recipient_id != user_id) {
                        database.ref('notifications/' + recipient_id + '/' + id).update({
                            count: details.count + 1,
                        });
                    }
                } else {
                    if (recipient_id != user_id) {
                        database.ref('notifications/' + recipient_id + '/' + id).update({
                            count: 1,
                        });
                    }
                }
            });
        });
        database.ref('chat_messages/' + id).push({
            message_content: attachment.name || 'https://www.google.com/images/spin-32.gif',
            is_read: 0,
            sender: user_id,
            sender_name: newUser,
            recipient: id,
            date: date.toLocaleDateString("en-US", options),
            time: date.toLocaleTimeString("en-US", options1),
            img: dp,
        }).then(function (data) {
            var filePath = id + file + '/' + attachment.name;
            return storage.ref('chat-attachments/' + filePath).put(attachment).then(function (snapshot) {
                var fullPath = snapshot.metadata.fullPath;
                return database.ref('chat_messages/' + id + '/' + data.key).update({
                    message_content: storage.ref(fullPath).toString()
                });
            });
        });
    } else {
        database.ref('chat_messages/' + id + '-' + user_id).push({
            message_content: attachment.name || 'https://www.google.com/images/spin-32.gif',
            is_read: 0,
            sender: user_id,
            sender_name: members,
            recipient: id,
            date: date.toLocaleDateString("en-US", options),
            time: date.toLocaleTimeString("en-US", options1),
            img: dp,
        }).then(function (data) {
            var filePath = id + '-' + user_id + file + '/' + attachment.name;
            return storage.ref('chat-attachments/' + filePath).put(attachment).then(function (snapshot) {
                var fullPath = snapshot.metadata.fullPath;
                return database.ref('chat_messages/' + id + '-' + user_id + '/' + data.key).update({
                    message_content: storage.ref(fullPath).toString()
                });
            });
        });
        database.ref('chat_messages/' + user_id + '-' + id).push({
            message_content: attachment.name || 'https://www.google.com/images/spin-32.gif',
            is_read: 0,
            sender: user_id,
            sender_name: members,
            recipient: id,
            date: date.toLocaleDateString("en-US", options),
            time: date.toLocaleTimeString("en-US", options1),
            img: dp,
        }).then(function (data) {
            var filePath = user_id + '-' + id + file + '/' + attachment.name;
            return storage.ref('chat-attachments/' + filePath).put(attachment).then(function (snapshot) {
                var fullPath = snapshot.metadata.fullPath;
                return database.ref('chat_messages/' + user_id + '-' + id + '/' + data.key).update({
                    message_content: storage.ref(fullPath).toString()
                });
            });
        });
        database.ref('notifications/' + id + '/' + user_id).once('value', function (snapshot) {
            if (snapshot.exists()) {
                var details = snapshot.val();
                if (id != user_id) {
                    database.ref('notifications/' + id + '/' + user_id).update({
                        count: details.count + 1,
                    });
                }
            } else {
                if (id != user_id) {
                    database.ref('notifications/' + id + '/' + user_id).update({
                        count: 1,
                    });
                }
            }
        });
    }
}
/**
 *  Show chat box info
 */
function boxInfo(group_id) {
    var div = document.getElementById('chat_info_' + group_id);
    if (!div) {
        database.ref('chat_groups/' + group_id).once('value', function (snapshot) {
            var data = snapshot.val();
            var creator = data.creator;
            var name = data.chat_name;
            var members_name = data.chat_members_name;
            var members_id = data.chat_members_id;
            var members_dp = data.chat_dps;
            var mode1 = 'name';
            var mode2 = 'members';
            $('.chatbody_' + group_id).append('<div class="chat-info" id="chat_info_' + group_id + '" style="display:block;">\
                <h4 class="title mb-plus-20 mt-plus-20"><strong>' + name + '</strong>\
                <a href="javascript:void(0);" class="" title="Edit Group Chat Name" onclick="editGroup(\'' + group_id + '\',\'' + name + '\',\'' + members_name + '\',\'' + members_id + '\',\'' + members_dp + '\', \'' + mode1 + '\');">\
                <i class="fa fa-pencil"></i>\
                </a>\
                </h4>\
                <div class="chat-group-btn">\
                <a href="javascript:void(0);" class="btn btn-warning btn-sm" title="Add Participants" onclick="editGroup(\'' + group_id + '\',\'' + name + '\',\'' + members_name + '\',\'' + members_id + '\',\'' + members_dp + '\', \'' + mode2 + '\');">\
                <img class="create-group-on-group" src="' + cdn_url + '/images/contact-request.png">\
                </a>\
                <a href="javascript:void(0);" class="btn btn-info btn-sm" title="Leave Chat Group" onclick="leaveGroup(\'' + group_id + '\',\'' + name + '\',\'' + members_name + '\',\'' + members_id + '\',\'' + creator + '\',\'' + members_dp + '\')"><i class="fa fa-sign-out"></i></a>\
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Delete Chat Group" onclick="deleteGroup(\'' + group_id + '\',\'' + creator + '\');"><i class="fa fa-trash"></i></a>\
                </div>\
                <br><span class="title">Group Participants</span>\
                <ul>\
                <li><label>' + members_name + '</label></li>\
                </ul>\
                </div>');
        });
    } else {
        $('#chat_info_' + group_id).remove();
    }
}
/**
 *  Show functions to edit group chat
 */
function editGroup(index, name, members_name, members_id, members_dp, mode) {
    $('#chatGroupModal').modal('show');
    document.getElementById('chatModalTitle').innerHTML = '<strong>Edit Group Chat</strong>';
    $('#groupChatName').val(name);
    $('#createGroupTitle').css('display', 'none');
    $('#editGroupTitle').removeAttr('style');
    $('#editGroup').removeAttr('style');
    $('#createGroup').css('display', 'none');
    $('#groupID').val(index);
    if (mode == 'name') {
        $('#chatGroupModal').on('shown.bs.modal', function() {
            $('#groupChatName').focus();
        })   
    } else {
        $('#chatGroupModal').on('shown.bs.modal', function() {
            $('#autoAddGroup').focus();
        }) 
    }   

    $('#createGroupUsers').find('input').prop('checked', false);
    reloadUserList('addedUsers');
    // reloadUserList('createGroupUsers');

    var name = members_name.replace(/"/g, '').split(',');
    var id = members_id.split(',');
    var img = members_dp.split(',');
    for (var i = id.length - 1; i >= 0; i--) {
        var user_list = document.getElementById('addedUsers');
        var list = document.createElement('li');
        var add_list = document.getElementById('added_' + id[i]);
        if (!checked_id.includes(parseInt(id[i]))) {
            checked_id.push(parseInt(id[i]));
            checked_name.push(name[i]);
            checked_dp.push(img[i]);
        }
        if (!add_list) {
            var user_item = '<li>\
            <img class="usr-img" src="' + img[i] + '" onerror="this.onerror=null;this.src=\'/images/agent.png\'" alt="User Image" />\
            <label class="user-name">' + name[i] + '</label>\
            </li>';
            list.innerHTML = user_item;
            add_list = list.firstChild;
            add_list.setAttribute('id', 'added_' + id[i]);
            user_list.appendChild(add_list);

            $('#add_' + id[i]).find('input').prop('checked', true);
        }
    }
}
/**
 *  Leaving on a group chat
 */
function leaveGroup(index, name, members_name, members_id, creator_id, members_dp) {
    var date = new Date;
    var date_split = date.toString().split(' ');
    var date_string = date_split[0] + ', ' + date_split[1] + ' ' + date_split[2] + ', ' + date_split[3];
    if (creator_id == user_id) {
        showWarningMessage('Group chat creator cannot leave the group!');            
    } else {
        var temp_id = members_id.split(",");

        var n_id = temp_id.indexOf(user_id);
        temp_id.splice(n_id, 1);
        var updatedString_id = temp_id.toString();

        var temp_name = members_name.split(",");

        var n_name = temp_name.indexOf(newUser);
        temp_name.splice(n_name, 1);
        var updatedString_name = temp_name.toString();

        var temp_dp = members_dp.split(",");

        var n_dp = temp_dp.indexOf(newUser);
        temp_dp.splice(n_dp, 1);
        var updatedString_dp = temp_dp.toString();

        if (confirm('Leave this conversation?')) {
            database.ref('chat_groups/' + index).update({
                chat_members_id: updatedString_id,
                chat_members_name: updatedString_name,
                chat_dps: updatedString_dp
            }, function (error) {
                if (error) {
                    showWarningMessage(error);            
                }
            });
            database.ref('chat_messages/' + index).push({
                notification: newUser + ' has left the group.',
                is_read: 0,
                sender: user_id,
                sender_name: name,
                recipient: index,
                date: date_string,
                time: date.toLocaleTimeString(),
                img: dp,
            }, function (error) {
                if (error) {
                    showWarningMessage(error);            
                }
            });
        }
    }
}
/**
 *  Delete group chat
 */
function deleteGroup(index, creator_id) {
    if (creator_id == user_id) {
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        })
        .then((result) => {
            if (result.value) {
                database.ref('chat_groups').child(index).remove();
                database.ref('chat_messages').child(index).remove();
                swal(
                    'Deleted!',
                    'Group Chat has been deleted.',
                    'success'
                )
            } else {
                swal("Group Chat is safe!");
            }
        })
    } else {
        showWarningMessage('Only creator of Group Chat can delete the Conversation.');            
    }
}
/**
 *  Send a friend request
 */
function addToContacts(id) {
    showLoadingAlert('Loading...');
    var data = '&name=' + name + '&id=' + id;
    $.ajax({
        type: 'POST',
        url: '/extras/chats/sendFriendRequest',
        data: data,
        dataType: 'json',
        success: function (data) {
            closeLoading();
            showSuccessMessage(data.msg, data.url);
            $('#favorites').removeClass('hide');
            $('#online').removeClass('hide');
            $('#groupconvo').removeClass('hide');
            $('#offline').removeClass('hide');
            $('#searchedUsers').addClass('hide');
        }
    });
}
/**
 *  Minimize chat box
 */
function minimize(index, members, number, chat) {
    var found = checkAvailability(allMinimizedBox, index.toString());
    var div = document.getElementById('chatusers_' + user_id);

    if (index == 'chatusers') {
        if ($('i.minmax').hasClass('fa-chevron-down')) {
            $('i.minmax').addClass("fa-chevron-up").removeClass("fa-chevron-down");
        } else if ($('i.minmax').hasClass('fa-chevron-up')) {
            $('i.minmax').addClass("fa-chevron-down").removeClass("fa-chevron-up");
        }
    }
    if ($('.chatbox_p_' + index).find('i.minmax_' + index).hasClass('fa-chevron-down')) {
        $('i.minmax_' + index).addClass("fa-chevron-up").removeClass("fa-chevron-down");
    } else {
        $('i.minmax_' + index).addClass("fa-chevron-down").removeClass("fa-chevron-up");
    }
    if ($('.chatbox_p_' + index).hasClass('minimized')) {
        if (found && index != 'chatusers' && $('#notif-count-' + index).length > 0) {
            allMinimizedBox.splice(allMinimizedBox.indexOf(index), 1);
            database.ref('notifications/' + user_id).child(index).remove();
            div.querySelector('#notif-count-' + index).innerHTML = 0;
            $('#notif-count-' + index).addClass('hide');
        }

        $('.chatbox_p_' + index).removeClass('minimized');
        $('div.chat-body_p_' + index).removeClass("hide");
        $('div.chat-footer_p_' + index).removeClass("hide");
    } else {
        if (!found && index != 'chatusers') {
            allMinimizedBox.push(index);
        }

        $('.chatbox_p_' + index).addClass('minimized');
        $("div.chat-body_p_" + index).addClass("hide");
        $("div.chat-footer_p_" + index).addClass("hide");

        if (!$('.chatbox_p_' + index).find('div.chatusers').hasClass('hide') 
            && $(window).width() < 768) {
            $('#chatusers').addClass('hide');
            $('.chat-container').css('right', '45px');
            $('.chatboxes_more').removeClass('hide');
        }

        $("#chat-filter").focus();
    }
}
/**
 *  Close chat box
 */
function closeChat(item, index, members, number, creator_id) {
    var mode = 'show';

    $('.chatbox_p_' + number.replace(/,/g, '')).remove();
    $('.chatbox_p_' + number.replace(/,/g, '')).hide();

    if (creator_id.includes(',')) {
        var chat = 'group';
        onlineGroupID.splice(onlineGroupID.indexOf(number), 1);
        onlineChatbox.splice(onlineChatbox.indexOf(number), 1);
    } else {
        var chat = 'private';
        onlineUsersID.splice(onlineUsersID.indexOf(number), 1);
        onlineChatbox.splice(onlineChatbox.indexOf(number), 1);
    }

    if (allArrID.length >= 1) {
        showChatbox(allArrName[0], allArrID[0], allArrMembers[0], mode, allArrChatbox[0], allArrChatboxCreatorID[0]);

        allOnlineName.push(allArrName[0]);
        allOnlineMembers.push(allArrMembers[0]);
        allOnlineID.push(allArrID[0]);
        allOnlineChatboxCreatorID.push(allArrChatboxCreatorID[0]);

        allOnlineName.splice(allOnlineName.indexOf(item), 1);
        allOnlineMembers.splice(allOnlineMembers.indexOf(members), 1);
        allOnlineID.splice(allOnlineID.indexOf(index), 1);
        allOnlineChatboxCreatorID.splice(allOnlineChatboxCreatorID.indexOf(number), 1);

        allArrName.splice(allArrName.indexOf(allArrName[0]), 1);
        allArrMembers.splice(allArrMembers.indexOf(allArrMembers[0]), 1);
        allArrID.splice(allArrID.indexOf(allArrID[0]), 1);
        allArrChatboxCreatorID.splice(allArrChatboxCreatorID.indexOf(allArrChatboxCreatorID[0]), 1);
    } else {
        allOnlineName.splice(allOnlineName.indexOf(item), 1);
        allOnlineMembers.splice(allOnlineMembers.indexOf(members), 1);
        allOnlineID.splice(allOnlineID.indexOf(index), 1);
        allOnlineChatboxCreatorID.splice(allOnlineChatboxCreatorID.indexOf(number), 1);

        allArrName.splice(allArrName.indexOf(item), 1);
        allArrMembers.splice(allArrMembers.indexOf(members), 1);
        allArrID.splice(allArrID.indexOf(index), 1);
        allArrChatboxCreatorID.splice(allArrChatboxCreatorID.indexOf(number), 1);
    }

    appendChatbox(chat, number);

    if (allArrMembers.length < 1) {
        $('.chatboxes').addClass('hide');
        if (allOnlineMembers.length == 0) {
            $('.chatboxes_more').addClass('hide');
            $('#chatusers').removeClass('hide');
        }
        $('.chat-header-hidden').addClass('hide');
    }
}
/**
 *  Hidden chat box
 */
function appendChatbox(mode, number) {
    for (var i = 0; i < allArrName.length + 1; i++) {
        reloadUserList('chat-header-hidden-list');
    }

    for (var i = 0; i < allArrName.length; i++) {
        var title = allArrMembers[i].replace(/ +/g, '').replace(/,/g, '');
        var members_title = allArrMembers[i].split(",").join("\n")
        var id = allArrID[i];
        $('.chat-header-hidden-list').append('<div class="chat-name" title="' + allArrName[i] + '" style="overflow:hidden;">' +
            '<a id="showDiv" href="javascript:void(0);" onclick="showUserDivAgain(\'' + allArrName[i] + '\',\'' + id + '\',\'' + allArrMembers[i] + '\',\'' + mode + '\',\'' + allArrChatbox[i] + '\',\'' + allArrChatboxCreatorID[i] + '\');">' +
            '<div class="hidden-title" title="' + members_title + '">' + allArrName[i] + '</div>' +
            '</a>' +
            '</div>');
    }
}
/**
 *  Show hidden chat box
 */
function showUserDivAgain(item, index, members, mode, number, creator_id) {
    if (creator_id.includes(',')) {
        mode = 'group';
    } else {
        if (mode == 'private') {
            mode = 'private';
        } else {
            mode = 'add';
        }
    }
    allOnlineName.push(item);
    allOnlineMembers.push(members);
    allOnlineID.push(index);
    onlineChatbox.push(number);
    allOnlineChatboxCreatorID.push(creator_id);

    allArrName.push(allOnlineName[0]);
    allArrMembers.push(allOnlineMembers[0]);
    allArrID.push(allOnlineID[0]);
    allArrChatbox.push(onlineChatbox[0]);
    allArrChatboxCreatorID.push(allOnlineChatboxCreatorID[0]);

    allArrName.splice(allArrName.indexOf(item), 1);
    allArrMembers.splice(allArrMembers.indexOf(members), 1);
    allArrID.splice(allArrID.indexOf(index), 1);
    allArrChatbox.splice(allArrChatbox.indexOf(number), 1);
    allArrChatboxCreatorID.splice(allArrChatboxCreatorID.indexOf(creator_id), 1);

    allOnlineName.shift();
    allOnlineMembers.shift();
    allOnlineID.shift();
    onlineChatbox.shift();
    allOnlineChatboxCreatorID.shift();

    var length_id = parseInt(allArrID.length) - 1;
    var length_num = parseInt(allArrChatbox.length) - 1;

    $('.chatbox_p_' + allArrID[length_id].replace(/ +/g, "").replace(/,/g, '')).remove();

    $('.chat-header-hidden').addClass('hide');
    showChatbox(item, index, members, mode, number, creator_id);
    appendChatbox(mode, number);
}
/**
 *  Reload chat user list
 */
function reloadUserList(id) {
    var myNode = document.getElementById(id);
    if (myNode == null) {
        myNode = '';
    } else {
        while (myNode.firstChild) {
            myNode.removeChild(myNode.firstChild);
        }
    }
}
/**
 *  Show other chat box
 */
function showOtherChatbox() {
    if ($('.chat-header-hidden').hasClass('hide')) {
        $('.chat-header-hidden').removeClass('hide');
    } else {
        $('.chat-header-hidden').addClass('hide');
    }
}
/**
 *  Check availability
 */
function checkAvailability(arr, val) {
    return arr.includes(val);
}
/**
 *  Make random id for group chat
 */
function makeRandomId(makefrom) {
    var text = "";

    for (var i = 0; i < 8; i++)
        text += makefrom.charAt(Math.floor(Math.random() * makefrom.length));

    return text;
}
/**
 *  Split
 */
function split(val) {
    return val.split(/,\s*/);
}
/**
 *  Extract last
 */
function extractLast(term) {
    return split(term).pop();
}

function showWarningMessage(msg) {
    swal("Warning", msg, "warning");
}

function showSuccessMessage(msg, url) {
    if (url == '') {
        swal("Success", msg, "success")
    }
    swal("Success", msg,"success").then((value) => {
        window.location.href = url;
    })
}

function showLoadingAlert(msg) {
    swal({
        title: msg,
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
          swal.showLoading();
        }
    })
}

function closeLoading() {
    swal.close();
}

window.reloadNotifs = reloadNotifs;
window.createGroup = createGroup;
window.chatOffline = chatOffline;
window.addToFavorites = addToFavorites;
window.resetChatList = resetChatList;
window.showUserDiv = showUserDiv;
window.showChatbox = showChatbox;
window.enterMsg = enterMsg;
window.boxInfo = boxInfo;
window.editGroup = editGroup;
window.leaveGroup = leaveGroup;
window.deleteGroup = deleteGroup;
window.addToContacts = addToContacts;
window.minimize = minimize;
window.closeChat = closeChat;
window.appendChatbox = appendChatbox;
window.showUserDivAgain = showUserDivAgain;
window.reloadUserList = reloadUserList;
window.showOtherChatbox = showOtherChatbox;
window.checkAvailability = checkAvailability;
window.makeRandomId = makeRandomId;
window.split = split;
window.extractLast = extractLast;