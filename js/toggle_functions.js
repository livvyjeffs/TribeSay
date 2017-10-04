function friendToggle(user) {
    if ($('#friend_status').hasClass('stranger')) {
        var typeo = "follow";
        var type = 'friend';
    } else if ($('#friend_status').hasClass('friend')) {
        var typeo = "unfollow";
        var type = 'unfriend';
    }
    //var conf = confirm("Press OK to '" + typeo + "' " + user +".");
    //if (conf !== true) {
    //    return false;
    //}
    //_(elem).innerHTML = 'please wait ...';
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/friend_system.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "friend_request_sent") {
                ga('send', 'event', frenetic['user'].username, 'follow', user);
                alert("You are now following " + user + ".");
                $('#friend_status').removeClass('stranger').addClass('friend').text(" - ");
            } else if (ajax.responseText === "unfollow_ok") {
                ga('send', 'event', frenetic['user'].username, 'unfollow', user);
                alert("You are no longer following " + user + ".");
                $('#friend_status').removeClass('friend').addClass('stranger').text(" + ");
            } else {
                //alert(ajax.responseText);
            }
        }
    };
    ajax.send("type=" + type + "&user=" + user);
}

function blockToggle(type, blockee, elem) {
    var conf = confirm("Press OK to confirm the '" + type + "' action on user <?php echo $u; ?>.");
    if (conf !== true) {
        return false;
    }
    var elem = document.getElementById(elem);
    elem.innerHTML = 'please wait ...';
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/block_system.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "blocked_ok") {
                elem.innerHTML = '<button onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock User</button>';
            } else if (ajax.responseText === "unblocked_ok") {
                elem.innerHTML = '<button onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block User</button>';
            } else {
                alert(ajax.responseText);
                elem.innerHTML = 'Try again later';
            }
        }
    };
    ajax.send("type=" + type + "&blockee=" + blockee);
}




