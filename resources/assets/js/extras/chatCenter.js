import swal from "sweetalert2";
/**
 *  INITIALIZATION
 */
var database = firebase.database();
var storage = firebase.storage();

var newUser = $('#full_name').val();
var user_id = $('#user_id').val();
var email = $('#email').val();
var cdn_url = $('#cdn_url').val();
var dp = $('#img_url').val();

var all_contacts = [];
var allOnlineID = [];
var checked_id = [];
var checked_name = [];
var checked_dp = [];
var added_mem = [];
var minus_mem = [];

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });

    $('.chat-container').addClass('hide');

    if (document.getElementById('chatList')) {
        loadUsers();
    }

    database.ref('users_info').orderByChild('full_name').on('child_changed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var mode = 'show';
        var div = document.getElementById('usercc_' + key);
        var container = document.createElement('div');

        if (div) {
            $('#usercc_' + key).remove();
        }

        if (user_id != details.user_id) {
            var container = document.createElement('div');
            var div = document.getElementById('usercc_' + key);
            var user_item = '<div class="user-item" style="">\
                <div onclick="showConvo(\'' + details.full_name + '\',\'' + details.user_id + '\',\'' + details.status + '\',\'' + details.email + '\',\'' + key + '\',\'' + mode + '\',\'' + details.img + '\');">\
                <div class="user-img ">\
                <img class="usr-img" src="' + details.img + '" alt="" width="100%"/>\
                </div>\
                <div class="chat-name">\
                <div class="title">' + details.full_name + '</div>\
                <span class=""></span>\
                </div>\
                <span id="notifcc-count-' + key + '" class="label-danger notif-counts notifcc-count-unread hide"></span>\
                </div>\
                </div>';

            container.innerHTML = user_item;
            div = container.firstChild;
            div.setAttribute('id', 'usercc_' + key);

            if (details.status == 'online' && all_contacts.indexOf(parseInt(details.user_id)) != -1) {
                var messageList = document.getElementById('onlineList');
                messageList.appendChild(div);
                $('#usercc_' + key).find('.user-img').addClass('online-img');
                $('#usercc_' + key).find('.chat-name span').addClass('online-span');
                div.querySelector('.online-span').textContent = 'Online';
            } else if (details.status == 'offline' && all_contacts.indexOf(parseInt(details.user_id)) != -1) {
                var messageList = document.getElementById('offlineList');
                messageList.appendChild(div);
                $('#usercc_' + key).find('.user-img').addClass('offline-img');
                $('#usercc_' + key).find('.chat-name span').addClass('offline-span');
                div.querySelector('.offline-span').textContent = 'Offline';
            }
        }
    });

    database.ref('chat_groups').orderByChild('chat_name').on('child_changed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var status = 'group';
        var box = document.getElementById('usercc_' + key);

        if (!details.chat_members_name.includes(newUser)) {
            $('.chatbox_p_' + key).remove();
            $('#usercc_' + key).remove();
            $('#chat_info_' + key).remove();
            $('.chat-message-container').addClass('hide');
            if (!box) {
                var container = document.createElement('div');
                var div = document.getElementById('usercc_' + key);
                var user_item = '<div class="user-item" style="">\
				    <div onclick="showConvo(\'' + details.chat_name + '\',\'' + details.chat_members_id + '\',\'' + status + '\',\'' + details.chat_members_name + '\',\'' + key + '\',\'' + mode + '\',\'' + details.chat_dps + '\');">\
                    <div class="user-img ">\
                    <img class="usr-img" src="' + cdn_url + '/images/user_img/goetu-profile.png" alt="" width="100%"/>\
                    </div>\
                    <div class="chat-name">\
                    <div class="title">' + details.chat_name + '</div>\
                    <span class=""></span>\
                    </div>\
                    <span id="notifcc-count-' + key + '" class="label-danger notif-counts notifcc-count-unread hide"></span>\
                    </div>\
					</div>';

                container.innerHTML = user_item;
                div = container.firstChild;
                div.setAttribute('id', 'usercc_' + key);
                if (arr_members.indexOf(parseInt(user_id)) != -1) {
                    var messageList = document.getElementById('groupList');
                    messageList.appendChild(div);
                    $('#usercc_' + key).find('.user-img').addClass('busy-img');
                    $('#usercc_' + key).find('.chat-name span').addClass('busy-span');
                    div.querySelector('.busy-span').textContent = 'Group';
                }
            }
        }
        showConvo(details.chat_name, details.chat_members_id, 'group', details.chat_members_name, key, 'show', details.chat_dps);
    });

    database.ref('chat_groups').on('child_removed', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();

        if (details.chat_members_name.includes(newUser)) {
            $('.chatbox_p_' + key).remove();
            $('#usercc_' + key).remove();
            $('#chat_info_' + key).remove();
            $('.chat-message-container').addClass('hide');
        }
    });

    $('#messageText').keypress(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();

            var recipient = $('#messageTextID').val();
            var sender = user_id;
            var recipient_name = $('#messageTextName').val();
            var sender_name = newUser;
            var number = $('#messageTextID').val();
            var index = $('#messageTextID').val();
            var msg = $('#messageText').val();
            var all_recipients = $('#messageTextMem').val().split(',');
            var date = new Date();
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Etc/Greenwich' };
            var options1 = { timeZone: 'Etc/Greenwich' };

            if (msg != '' && msg != undefined) {
                var regx = /[一-龠]+|[ぁ-ゔ]+|[ァ-ヴー]+|[a-zA-Z0-9]+|[ａ-ｚＡ-Ｚ０-９]+[々〆〤]+/u;
                if (regx.test(msg)) {
                    if ($('#messageTextMode').val() == 'group') {
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
                        database.ref('chat_messages/' + sender + '-' + recipient).push({
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
            $('#messageText').val('');
        }
    });

    $('#uploadAttachment').change(function (event) {
        event.preventDefault();
        var attach = document.getElementById('uploadfile').files;
        var number = $('#messageTextMem').val();

        for (var i = attach.length - 1; i >= 0; i--) {
            var file = attach[i];
            uploadAttachment(file, number);
           
        }
        document.getElementById('uploadAttachment').reset();
    });

    database.ref('notifications/' + user_id).on('child_changed', function (snapshot) {
        var details = snapshot.val();
        var key = snapshot.key;

        if (allOnlineID.indexOf(key.toString()) == -1 
            || $('#messageTextID').val() != key) {
            var user_div = document.getElementById('usercc_' + key);
            if (user_div) {
                $('#notifcc-count-' + key).removeClass('hide');
                user_div.querySelector('#notifcc-count-' + key).innerHTML = details.count;
                user_div.setAttribute('data-key', snapshot.key);
                $('#usercc_' + key).prependTo('#newList').hide().show('slow');
            }
        } else {
            database.ref('notifications/' + user_id).child(key).remove();
        }
    });

    $('#createGroup1').click(function () {
        var chat_name = $('#groupChatName1').val();

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

        $('#groupChatName1').val('');
        $('#autoAddGroup1').val('');
        checked_id = [];
        checked_name = [];
        checked_dp = [];
        added_mem = [];
        minus_mem = [];
        reloadUserList('addedUsers1');
        reloadUserList('createGroupUsers1');
        loadUsers();
        showConvo(chat_name, members_id + ',' + user_id, 'group', members_name + ',' + newUser, random_id, 'show', members_dp + ',' + dp);

    });

    /**
     * Add/remove user to existing group chat
     */
    $('#editGroup1').click(function () {
        $('#chat_info_' + $('#messageTextID').val()).remove();
        var date = new Date;
        var date_split = date.toString().split(' ');
        var date_string = date_split[0] + ', ' + date_split[1] + ' ' + date_split[2] + ', ' + date_split[3];

        database.ref('chat_groups/' + $('#messageTextID').val()).update({
            chat_name: $('#groupChatName1').val(),
            chat_members_id: checked_id.toString(),
            chat_members_name: checked_name.toString(),
            chat_dps: checked_dp.toString(),
        });
        if (added_mem) {
            for (var i = added_mem.length - 1; i >= 0; i--) {
                database.ref('chat_messages/' + $('#messageTextID').val()).push({
                    notification: added_mem[i] + ' joined the group.',
                    is_read: 0,
                    sender: user_id,
                    sender_name: $('#groupChatName1').val(),
                    recipient: $('#messageTextID').val(),
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
                database.ref('chat_messages/' + $('#messageTextID').val()).push({
                    notification: minus_mem[i] + ' was removed from the group.',
                    is_read: 0,
                    sender: user_id,
                    sender_name: $('#groupChatName1').val(),
                    recipient: $('#messageTextID').val(),
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
        $('#groupChatName1').val('');
        $('#autoAddGroup1').val('');
        checked_id = [];
        checked_name = [];
        added_mem = [];
        minus_mem = [];
        reloadUserList('addedUsers1');
        // reloadUserList('createGroupUsers1');
    });

    $('#groupChatModal').on('hidden.bs.modal', function (e) {
        e.preventDefault();
        // loadUsers();
        $('#createGroupUsers1').find('input').prop('checked', false);
        reloadUserList('addedUsers1');
        document.getElementById('chatModalTitle').innerHTML = '<strong>Create Group Chat</strong>';
        $('#groupChatName1').val('');
        $('#createGroupTitle1').removeAttr('style');
        $('#editGroupTitle1').css('display', 'none');
        $('#editGroup1').css('display', 'none');
        $('#createGroup1').removeAttr('style');
        $('#groupID1').val('');
        checked_id = [];
        checked_name = [];
        checked_dp = [];
    })

    $('#autoAddGroup1')
        .on("keydown", function (event) {
            if (event.keyCode == '8') {
                loadUsers();
            }
        })
        .autocomplete({
            minLength: 3,
            source: function (request, response) {
                $.getJSON("/extras/chats/addToGroup", {
                    contact: request.term
                }, function (data) {
                    reloadUserList('createGroupUsers1');
                    var data = data.data;
                    $('#autoAddGroup1').removeClass('ui-autocomplete-loading');
                    if (data != 'No results found.') {
                        for (var i = data.length - 1; i >= 0; i--) {
                            var list = document.createElement('li');
                            var user_list = document.getElementById('createGroupUsers1');
                            var add_list = document.getElementById('add1_' + data[i].id);
                            if (!add_list) {
                                if (checked_id.includes(data[i].id)) {
                                    var user_item = '<li>\
                                                        <img class="usr-img" src="' + data[i].image + '" alt="User Image" />\
                                                        <label class="user-name">' + data[i].name + '<span><small><i>&nbsp;' + data[i].email_address + '</i></small></span></label>\
                                                        <input class="pull-right create_group_users" id="add_users1" name="add1_users[]" type="checkbox" value="' + data[i].id + '" data-name="' + data[i].name + '" onclick="createGroup1();" checked>\
                                                    </li>';
                                } else {
                                    var user_item = '<li>\
                                                        <img class="usr-img" src="' + data[i].image + '" alt="User Image" />\
                                                        <label class="user-name">' + data[i].name + '<span><small><i>&nbsp;' + data[i].email_address + '</i></small></span></label>\
                                                        <input class="pull-right create_group_users" id="add_users1" name="add1_users[]" type="checkbox" value="' + data[i].id + '" data-name="' + data[i].name + '" onclick="createGroup1();">\
                                                    </li>';
                                }
                            }
                            list.innerHTML = user_item;
                            add_list = list.firstChild;
                            add_list.setAttribute('id', 'add1_' + data[i].id);
                            user_list.appendChild(add_list);
                        }
                    } else {
                        var list = document.createElement('li');
                        var user_list = document.getElementById('createGroupUsers1');
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

});

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

function goOnlineGetList() {
    database.goOnline();
    database.ref('users_info/' + user_id).once('value', function (snapshot) {
        chatOnline(snapshot);
    });

    database.ref('users_info').orderByChild('full_name').on('child_added', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var mode = 'show';
        var div = document.getElementById('usercc_' + key);
        var container = document.createElement('div');

        if (!div && user_id != details.user_id) {
            var container = document.createElement('div');
            var div = document.getElementById('usercc_' + key);
            var user_item = '<div class="user-item" style="">\
                <div onclick="showConvo(\'' + details.full_name + '\',\'' + details.user_id + '\',\'' + details.status + '\',\'' + details.email + '\',\'' + key + '\',\'' + mode + '\',\'' + details.img + '\');">\
                <div class="user-img ">\
                <img class="usr-img" src="' + details.img + '" alt="" width="100%"/>\
                </div>\
                <div class="chat-name">\
                <div class="title">' + details.full_name + '</div>\
                <span class=""></span>\
                </div>\
                <span id="notifcc-count-' + key + '" class="label-danger notif-counts notifcc-count-unread hide"></span>\
                </div>\
                </div>';

            container.innerHTML = user_item;
            div = container.firstChild;
            div.setAttribute('id', 'usercc_' + key);

            if (details.status == 'online' && all_contacts.indexOf(parseInt(details.user_id)) != -1) {
                var messageList = document.getElementById('onlineList');
                messageList.appendChild(div);
                $('#usercc_' + key).find('.user-img').addClass('online-img');
                $('#usercc_' + key).find('.chat-name span').addClass('online-span');
                div.querySelector('.online-span').textContent = 'Online';
            } else if (details.status == 'offline' && all_contacts.indexOf(parseInt(details.user_id)) != -1) {
                var messageList = document.getElementById('offlineList');
                messageList.appendChild(div);
                $('#usercc_' + key).find('.user-img').addClass('offline-img');
                $('#usercc_' + key).find('.chat-name span').addClass('offline-span');
                div.querySelector('.offline-span').textContent = 'Offline';
            }
        }
    });

    database.ref('chat_groups').orderByChild('chat_name').on('child_added', function (snapshot) {
        var key = snapshot.key;
        var details = snapshot.val();
        var mode = 'show';
        var status = 'group';
        var arr_members = JSON.parse("[" + details.chat_members_id.toString() + "]");
        var div = document.getElementById('usercc_' + key);
        var container = document.createElement('div');

        if (!div) {
            var container = document.createElement('div');
            var div = document.getElementById('usercc_' + key);
            var user_item = '<div class="user-item" style="">\
                <div onclick="showConvo(\'' + details.chat_name + '\',\'' + details.chat_members_id + '\',\'' + status + '\',\'' + details.chat_members_name + '\',\'' + key + '\',\'' + mode + '\',\'' + details.chat_dps + '\');">\
                <div class="user-img ">\
                <img class="usr-img" src="' + cdn_url + '/images/user_img/goetu-profile.png" alt="" width="100%"/>\
                </div>\
                <div class="chat-name">\
                <div class="title">' + details.chat_name + '</div>\
                <span class=""></span>\
                </div>\
                <span id="notifcc-count-' + key + '" class="label-danger notif-counts notifcc-count-unread hide"></span>\
                </div>\
	            </div>';

            container.innerHTML = user_item;
            div = container.firstChild;
            div.setAttribute('id', 'usercc_' + key);
            if (arr_members.indexOf(parseInt(user_id)) != -1) {
                var messageList = document.getElementById('groupList');
                messageList.appendChild(div);
                $('#usercc_' + key).find('.user-img').addClass('busy-img');
                $('#usercc_' + key).find('.chat-name span').addClass('busy-span');
                div.querySelector('.busy-span').textContent = 'Group';
            }
        }
    });
}

function getUnreadNotif() {
    database.ref('notifications/' + user_id).on('child_added', function (snapshot) {
        var details = snapshot.val();
        var key = snapshot.key;

        if (allOnlineID.indexOf(key.toString()) == -1 
            || $('#messageTextID').val() != key) {
            var user_div = document.getElementById('usercc_' + key);
            if (user_div) {
                $('#notifcc-count-' + key).removeClass('hide');
                user_div.querySelector('#notifcc-count-' + key).innerHTML = details.count;
                user_div.setAttribute('data-key', snapshot.key);
                $('#usercc_' + key).prependTo('#newList').hide().show('slow');
            }
        } else {
            database.ref('notifications/' + user_id).child(key).remove();
        }
    });
}

function loadUsers() {
    $.ajax({
        type: 'GET',
        url: '/extras/chats/getPreloadUsers',
        dataType: 'json',
        success: function (data) {
            $('#autoAddGroup1').removeClass('ui-autocomplete-loading');

            $.each(data.data, function(index, value){
                var full_name = value.full_name;
                var user_id = value.id;
                var email_address = value.email;
                var user_image = value.image;

                var list = document.createElement('li');
                var user_list = document.getElementById('createGroupUsers1');
                var add_list = document.getElementById('add1_' + user_id);

                var user_item = '<li>\
                    <img class="usr-img" src="' + user_image + '" alt="User Image" />\
                    <label class="user-name">' + full_name + '<span><small>\
                    <i>&nbsp;' + email_address + '</i></small></span></label>\
                    <input class="pull-right create_group_users" id="add_users1" name="add1_users[]" type="checkbox" value="' + user_id + '" data-name="' + full_name + '" onclick="createGroup1();">\
                    </li>';

                list.innerHTML = user_item;
                add_list = list.firstChild;
                add_list.setAttribute('id', 'add1_' + user_id);
                user_list.appendChild(add_list);
                all_contacts.push(user_id);
            });
            
            goOnlineGetList();
            getUnreadNotif();
        }
    });
}

function createGroup1() {
    $("input[name='add1_users[]']").change(function () {
        var user_id = parseInt($(this).attr('value'));
        var full_name = $(this).attr('data-name');
        var img = $('#add1_' + user_id).find('img').attr('src');

        if ($(this).is(':checked')) {
            var user_list = document.getElementById('addedUsers1');
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
                    <img class="usr-img" src="' + img + '" alt="User Image" />\
                    <label>' + full_name + '</label>\
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
            }
            $('#added_' + user_id).remove();
        }
    });
    return false;
}

function showConvo(name, members, status, email, key, mode, img) {
    var div = document.getElementById('chatMateInfo');
    allOnlineID = [];
    if ($('#usercc_' + key).attr('data-key')) {
        var user_div = document.getElementById('usercc_' + key);
        database.ref('notifications/' + user_id).child(key).remove();
        user_div.querySelector('#notifcc-count-' + key).innerHTML = 0;
        $('#notifcc-count-' + key).addClass('hide');
    }
    allOnlineID.push(key);
    $('.chat-info').remove();
    $('.chat-message-container').removeClass('hide');
    $('.chat-mate-info').removeClass('hide');
    $('.chat-menu-icon').removeClass('hide');
    $('.chat-info-menu').removeClass('hide');
    $('.chat-message-box').removeClass('hide');
    div.querySelector('.chat-user-name').innerHTML = name;
    div.querySelector('.user-status').innerHTML = status;
    if (status == 'online') {
        $('span.user-status').addClass('online-span');
        $('span.user-status').removeClass('offline-span');
        $('span.user-status').removeClass('busy-span');
    }
    if (status == 'offline') {
        $('span.user-status').addClass('offline-span');
        $('span.user-status').removeClass('online-span');
        $('span.user-status').removeClass('busy-span');
    }
    if (status == 'group') {
        $('span.user-status').addClass('busy-span');
        $('span.user-status').removeClass('online-span');
        $('span.user-status').removeClass('offline-span-span');
    }
    if (status == 'group') {
        $('.separator').addClass('hide');
        $('.info-circle1').removeClass('hide');
        $('.info-circle1').attr('id', 'info-circle-' + key);
        $('#info-circle-' + key).attr('onclick', 'boxInfo1("' + key + '")');
        $('.usr-img-center').attr('src', cdn_url + '/images/user_img/goetu-profile.png');
        div.querySelector('.user-email').innerHTML = '';
    } else {
        $('.info-circle1').addClass('hide');
        $('.usr-img-center').attr('src', img);
        div.querySelector('.user-email').innerHTML = email;
    }
    $('#messageText').attr('disabled', false);
    reloadUserList('chatMessageList');

    $('#messageTextID').val(key);
    $('#messageTextName').val(name);
    $('#messageTextMode').val(status);
    $('#messageTextMem').val(members);

    database.ref('chat_messages').off();

    $("#chatMessageList").scroll(function(){
        if ($("#chatMessageList").scrollTop() == 0){
            var type = status == 'group' ? 'msgs' : 'pmsgs';
            if ($('#chatMessageList').children('div.' + type + ':hidden').length != 0) {
                $('div.' + type + ':hidden').slice(-12).toggle();
                $("#chatMessageList").scrollTop($("#chatMessageList")[0].scrollHeight / 3);
            } 
        }
    });

    if (status == 'group') {
        // database.ref('samples').limitToLast(12).on('child_added',function(snapshot){
        database.ref('chat_messages/' + key).on('child_added', function (snapshot) {
            messageListGroup(key, snapshot);
        });

        database.ref('chat_messages/' + key).on('child_changed', function (snapshot) {
            messageListGroup(key, snapshot);
        });

        database.ref('chat_groups/' + key).once('value').then(function(snapshot) {
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
        // database.ref('samples').limitToLast(12).on('child_added',function(snapshot){
        database.ref('chat_messages/' + key + '-' + user_id).on('child_added', function (snapshot) {
            messageList(key, snapshot);
        });

        database.ref('chat_messages/' + key + '-' + user_id).on('child_changed', function (snapshot) {
            messageList(key, snapshot);
        });

        database.ref('users_info/' + key).once('value').then(function(snapshot) {
            var details = snapshot.val();
            $('.usr-img-' + key).attr('src', details.img);
        });
    }
}

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

    if (!div && ($('#messageTextID').val() == details.recipient || $('#messageTextID').val() == details.sender)) {
        var date_key = details.date.replace(/[\ +\,']+/g, '');
        var date_div = document.getElementById('date_' + date_key);
        var container = document.createElement('div');
        if (!date_div) {
            messageInput = document.getElementById('message_c_' + details.recipient);
            messageList = document.getElementById('chatMessageList');
            container.innerHTML = '<div class="row pmsgs">\
                <div class="date">' + date_string + '</div>\
                </div>';
            date_div = container.firstChild;
            date_div.setAttribute('id', 'date_' + date_key);
            messageList.appendChild(date_div);
        }
        if (user_id == details.sender) { // sender
            messageInput = document.getElementById('message_c_' + details.recipient);
            messageList = document.getElementById('chatMessageList');
            container.innerHTML = '<div class="row pmsgs"><div class="chat-smsg" id="chat-smsg_' + details.recipient + '" title="' + dt.toLocaleTimeString() + '">' +
                '<p class="scloud scloud-center">' + msg + '</p>' +
                '<i class="fa fa-caret-right"></i>' +
                '<div class="sent-status sent-status-center">' +
                // jQuery.timeago(dt) + '</div>' +
                new_time + '</div>' +
                '</div>' +
                '<div class="clearfix"></div>' +
                '</div></div>';
            div = container.firstChild;
            div.setAttribute('id', key);
            messageList.appendChild(div);
        } else if (user_id == details.recipient) {
            messageInput = document.getElementById('message_c_' + details.sender);
            messageList = document.getElementById('chatMessageList');
            container.innerHTML = '<div class="row pmsgs"><div class="receiver-img receiver-img-' + details.sender + ' ">' +
                '<img class="usr-img" src="' + details.img + '" alt="" width="100%"/>' +
                '<div class="clearfix"></div>' +
                '</div>' +
                '<div class="chat-msg chat-msg-center" id="chat-msg_' + details.sender + '" title="' + dt.toLocaleTimeString() + '">' +
                '<i class="fa fa-caret-left"></i>' +
                '<p class="rcloud rcloud-center">' + msg + '</p>' +
                '<div class="sent-status sent-status-center">' +
                // jQuery.timeago(dt) + '</div>' +
                new_time + '</div>' +
                '<div class="clearfix"></div>' +
                '</div></div>';
            div = container.firstChild;
            div.setAttribute('id', key);
            messageList.appendChild(div);
        }
    }

    if ($('#messageTextID').val() == details.recipient || $('#messageTextID').val() == details.sender) {
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
                $('#chatMessageList').scrollTop($('#chatMessageList')[0].scrollHeight);
            } else if (user_id == details.recipient) {
                $('#chatMessageList').scrollTop($('#chatMessageList')[0].scrollHeight);
            } 
        }, 1);
        //
    }
}

function messageListGroup(recipient, snapshot) {
    var key = snapshot.key;
    var details = snapshot.val();
    var date = 'now';
    var newUser1 = newUser.replace(/ +/g, '');
    var div = document.getElementById(key);
    var messageInput = '';
    var messageList = '';
    var d = new Date();
    var gmtHrs = -d.getTimezoneOffset() / 60;
    var dt = new Date(details.date + ' ' + details.time);
    dt.setHours( dt.getHours() + gmtHrs);
    var new_time = dt.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    var date_split = dt.toString().split(' ');
    var date_string = date_split[0] + ', ' + date_split[1] + ' ' + date_split[2] + ', ' + date_split[3];

    if ((recipient == details.recipient || user_id == details.sender) 
&& $('#messageTextID').val() == details.recipient) {
        if (!div) {
            var date_key = details.date.replace(/[\ +\,']+/g, '');
            var date_div = document.getElementById('date_' + date_key);
            var container = document.createElement('div');
            if (!date_div) {
                messageInput = document.getElementById('message_c_' + details.recipient);
                messageList = document.getElementById('chatMessageList');
                container.innerHTML = '<div class="row msgs">\
                    <div class="date">' + date_string + '</div>\
                    </div>';
                date_div = container.firstChild;
                date_div.setAttribute('id', 'date_' + date_key);
                messageList.appendChild(date_div);
            }
            if (details.notification) {
                messageInput = document.getElementById('message_c_' + details.recipient);
                messageList = document.getElementById('chatMessageList');
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
                    messageInput = document.getElementById('message_c_' + details.recipient);
                    messageList = document.getElementById('chatMessageList');
                    container.innerHTML = '<div class="row msgs"><div class="chat-smsg" id="chat-smsg_' + details.recipient + '" title="' + dt.toLocaleTimeString() + '">' +
                        '<p class="scloud scloud-center"></p>' +
                        '<i class="fa fa-caret-right"></i>' +
                        '<div class="sent-status sent-status-center">' +
                        // jQuery.timeago(dt) + '</div>' +
                        new_time + '</div>' +
                        '<div class="clearfix"></div>' +
                        '</div></div>';
                    div = container.firstChild;
                    div.setAttribute('id', key);
                    messageList.appendChild(div);
                } else if (details.recipient == recipient) {
                    messageInput = document.getElementById('message_c_' + details.recipient);
                    messageList = document.getElementById('chatMessageList');
                    container.innerHTML = '<div class="row msgs"><span class="rcloud-name rcloud-name-center"><small>' + details.sender_name + '</small></span></br>' +
                        '<div class="receiver-img receiver-img-' + details.sender + ' ">' +
                        '<img class="usr-img" src="' + details.img + '" alt="" width="100%"/>' +
                        '<div class="clearfix"></div>' +
                        '</div>' +
                        '<div class="chat-msg chat-msg-center" id="chat-msg_' + details.sender + '" title="' + dt.toLocaleTimeString() + '">' +
                        '<i class="fa fa-caret-left"></i>' +
                        '<p class="rcloud rcloud-center"></p>' +
                        '<div class="sent-status sent-status-center">' +
                        // jQuery.timeago(dt) + '</div>' +
                        new_time + '</div>' +
                        '<div class="clearfix"></div>' +
                        '</div></div>';
                    div = container.firstChild;
                    div.setAttribute('id', key);
                    messageList.appendChild(div);
                }
            }
        }

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
                messageElement.textContent = details.message_content;
                // Replace all line breaks by <br>.
                messageElement.innerHTML = messageElement.innerHTML.replace(/\n/g, '<br>');
            }
        }
        // Show the card fading-in and scroll to view the new message.
        setTimeout(function () {
            $('div.msgs').slice(-12).show();
            if (user_id == details.sender) {
                $('#chatMessageList').scrollTop($('#chatMessageList')[0].scrollHeight);
            } else if (user_id == details.recipient) {
                $('#chatMessageList').scrollTop($('#chatMessageList')[0].scrollHeight);
            }
        }, 1);
        //
    }
}

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

function reloadNotifs() {
    database.ref('notifications/' + user_id).on('child_added', function (snapshot) {
        var details = snapshot.val();
        var key = snapshot.key;
        var div = document.getElementById('chatList');

        if (allOnlineID.indexOf(key.toString()) == -1) {
            var user_div = document.getElementById('usercc_' + key);
            if (user_div) {
                $('#notifcc-count-' + key).removeClass('hide');
                user_div.querySelector('#notifcc-count-' + key).innerHTML = details.count;
                user_div.setAttribute('data-key', snapshot.key);
            }
        } else {
            database.ref('notifications/' + user_id).child(key).remove();
        }
    });
}

function boxInfo1(group_id) {
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
            $('.chat-message-container').prepend('<div class="chat-info" id="chat_info_' + group_id + '" style="display:block;top:80px;">\
                <h4 class="title mb-plus-20 mt-plus-20"><strong>' + data.chat_name + '</strong>\
                <a href="javascript:void(0);" class="" title="Edit Group Chat Name" onclick="editGroup1(\'' + group_id + '\',\'' + name + '\',\'' + members_name + '\',\'' + members_id + '\',\'' + members_dp + '\', \'' + mode1 + '\');">\
                    <i class="fa fa-pencil"></i>\
                </a>\
                </h4>\
                <div class="chat-group-btn">\
                <a href="javascript:void(0);" class="btn btn-warning btn-sm" title="Add Participants to Group Chat" onclick="editGroup1(\'' + group_id + '\',\'' + name + '\',\'' + members_name + '\',\'' + members_id + '\',\'' + members_dp + '\', \'' + mode2 + '\');">\
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

function uploadAttachment(attachment, number) {
    var all_recipients = number.split(',');
    var date = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Etc/Greenwich' };
    var options1 = { timeZone: 'Etc/Greenwich' };
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

    if ($('#messageTextMode').val() == 'group') {
        database.ref('chat_messages/' + $('#messageTextID').val()).push({
            message_content: attachment.name || 'https://www.google.com/images/spin-32.gif',
            is_read: 0,
            sender: user_id,
            sender_name: newUser,
            recipient: $('#messageTextID').val(),
            date: date.toLocaleDateString("en-US", options),
            time: date.toLocaleTimeString("en-US", options1),
            img: dp,
        }).then(function (data) {
            var filePath = $('#messageTextID').val() + file + '/' + attachment.name;
            return storage.ref('chat-attachments/' + filePath).put(attachment).then(function (snapshot) {
                var fullPath = snapshot.metadata.fullPath;
                return database.ref('chat_messages/' + $('#messageTextID').val() + '/' + data.key).update({
                    message_content: storage.ref(fullPath).toString()
                });
            });
        });
        all_recipients.forEach(function (recipient_id) {
            database.ref('notifications/' + recipient_id + '/' + $('#messageTextID').val()).once('value', function (snapshot) {
                if (snapshot.exists()) {
                    var details = snapshot.val();
                    if (recipient_id != user_id) {
                        database.ref('notifications/' + recipient_id + '/' + $('#messageTextID').val()).update({
                            count: details.count + 1,
                        });
                    }
                } else {
                    if (recipient_id != user_id) {
                        database.ref('notifications/' + recipient_id + '/' + $('#messageTextID').val()).update({
                            count: 1,
                        });
                    }
                }
            });
        });
    } else {
        database.ref('chat_messages/' + $('#messageTextID').val() + '-' + user_id).push({
            message_content: attachment.name || 'https://www.google.com/images/spin-32.gif',
            is_read: 0,
            sender: user_id,
            sender_name: newUser,
            recipient: $('#messageTextID').val(),
            date: date.toLocaleDateString("en-US", options),
            time: date.toLocaleTimeString("en-US", options1),
            img: dp,
        }).then(function (data) {
            var filePath = $('#messageTextID').val() + '-' + user_id + file + '/' + attachment.name;
            return storage.ref('chat-attachments/' + filePath).put(attachment).then(function (snapshot) {
                var fullPath = snapshot.metadata.fullPath;
                return database.ref('chat_messages/' + $('#messageTextID').val() + '-' + user_id + '/' + data.key).update({
                    message_content: storage.ref(fullPath).toString()
                });
            });
        });
        database.ref('chat_messages/' + user_id + '-' + $('#messageTextID').val()).push({
            message_content: attachment.name || 'https://www.google.com/images/spin-32.gif',
            is_read: 0,
            sender: user_id,
            sender_name: newUser,
            recipient: $('#messageTextID').val(),
            date: date.toLocaleDateString("en-US", options),
            time: date.toLocaleTimeString("en-US", options1),
            img: dp,
        }).then(function (data) {
            var filePath = user_id + '-' + $('#messageTextID').val() + file + '/' + attachment.name;
            return storage.ref('chat-attachments/' + filePath).put(attachment).then(function (snapshot) {
                var fullPath = snapshot.metadata.fullPath;
                return database.ref('chat_messages/' + user_id + '-' + $('#messageTextID').val() + '/' + data.key).update({
                    message_content: storage.ref(fullPath).toString()
                });
            });
        });
        database.ref('notifications/' + $('#messageTextID').val() + '/' + user_id).once('value', function (snapshot) {
            if (snapshot.exists()) {
                var details = snapshot.val();
                if ($('#messageTextID').val() != user_id) {
                    database.ref('notifications/' + $('#messageTextID').val() + '/' + user_id).update({
                        count: details.count + 1,
                    });
                }
            } else {
                if ($('#messageTextID').val() != user_id) {
                    database.ref('notifications/' + $('#messageTextID').val() + '/' + user_id).update({
                        count: 1,
                    });
                }
            }
        });
    }
}

function editGroup1(index, name, members_name, members_id, members_dp, mode) {
    $('#groupChatModal').modal('show');
    document.getElementById('chatModalTitle1').innerHTML = '<strong>Edit Group Chat</strong>';
    $('#groupChatName1').val(name);
    $('#createGroupTitle1').css('display', 'none');
    $('#editGroupTitle1').removeAttr('style');
    $('#editGroup1').removeAttr('style');
    $('#createGroup1').css('display', 'none');
    $('#groupID1').val(index);
    if (mode == 'name') {
        $('#groupChatModal').on('shown.bs.modal', function() {
            $('#groupChatName1').focus();
        })   
    } else {
        $('#groupChatModal').on('shown.bs.modal', function() {
            $('#autoAddGroup1').focus();
        }) 
    }   

    $('#createGroupUsers1').find('input').prop('checked', false);
    reloadUserList('addedUsers1');
    // reloadUserList('createGroupUsers');

    var name = members_name.replace(/"/g, '').split(',');
    var id = members_id.split(',');
    var img = members_dp.split(',');
    for (var i = id.length - 1; i >= 0; i--) {
        var user_list = document.getElementById('addedUsers1');
        var list = document.createElement('li');
        var add_list = document.getElementById('added_' + id[i]);

        if (!checked_id.includes(parseInt(id[i]))) {
            checked_id.push(parseInt(id[i]));
            checked_name.push(name[i]);
            checked_dp.push(img[i]);
        }
        if (!add_list) {
            var user_item = '<li>\
            <img class="usr-img" src="' + img[i] + '" alt="User Image" />\
            <label class="user-name">' + name[i] + '</label>\
            </li>';
            list.innerHTML = user_item;
            add_list = list.firstChild;
            add_list.setAttribute('id', 'added_' + id[i]);
            user_list.appendChild(add_list);

            $('#add1_' + id[i]).find('input').prop('checked', true);
        }
    }
}

function showWarningMessage(msg) {
    swal("Warning", msg, "warning");
}

window.showConvo = showConvo;
window.messageList = messageList;
window.messageListGroup = messageListGroup;
window.reloadUserList = reloadUserList;
window.reloadNotifs = reloadNotifs;
window.createGroup1 = createGroup1;
window.boxInfo1 = boxInfo1;
window.uploadAttachment = uploadAttachment;
window.editGroup1 = editGroup1;