function get_columns() {
    var columns = Math.floor($('#stream_container').width() / 250);

    if (window.innerWidth < 500) {
        columns = 1;
    } else if ($('#stream_container').length === 0) {
        columns = Math.floor(window.innerWidth / 250);
    }

    if (columns > 4) {
        columns = 4;
    }

    return columns;
}

function preload_column_width(){
    var window_width = window.innerWidth;
    var width;
    
    if(window_width > 650){
        width = ((window_width * 0.8 - 15 * (frenetic.column_count + 1)) / frenetic.column_count).toFixed(2);
    }else{
        width = ((window_width - 15 * (frenetic.column_count + 1)) / frenetic.column_count).toFixed(2);
    }  
    
    return width;
}  
    


function get_avatar(username) {
    var ajax = ajaxObj("POST", "php_parsers/get_user_avatar.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            alert(ajax.responseText);
        }
    };
    ajax.send("username=" + username);
}
;

function get_filter_tags() {

    var array = [];

    $('#tribe_bar .tag_text').each(function() {
        array.push($(this).attr('tag'));
    });

    return array;
};

function set_user(username) {

    frenetic['user'].username = username;

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_user_avatar.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            var json = JSON.parse(ajax.responseText);
            frenetic['user'].avatar = json.path;
            frenetic['user'].avatar_ratio = parseFloat(json.ratio);
            $('#profile_image img').attr({'src': json.path, 'alt': username, 'title': username});
        }
    };
    ajax.send("username=" + username);

    //WAITING FOR AJAX FUNCTION

    frenetic['user'].score = Math.round(1000 * Math.random());

    ////////MARTIN THIS NEEDS TO BE CREATED IN PHP///////////////////////////////////////////////////////////////////////////////////////

//     var ajaxa = ajaxObj("POST", frenetic.root + "/php_parsers/get_user_avatar.php");
//    ajaxa.onreadystatechange = function(){
//        if(ajaxReturn(ajaxa) === true){
//            //frenetic['page_owner'].score = parseFloat(ajax.responeText);
//             
//        }
//    };
//    ajaxa.send("username="+username);


}

function set_pageowner(username) {

    frenetic['page_owner'].username = username;

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_user_avatar.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            var json = JSON.parse(ajax.responseText);
            frenetic['page_owner'].avatar = json.path;
            frenetic['page_owner'].avatar_ratio = parseFloat(json.ratio);
        }
    };
    ajax.send("username=" + username);

    //WAITING FOR AJAX FUNCTION

    frenetic['page_owner'].score = Math.round(1000 * Math.random());

    ////////MARTIN THIS NEEDS TO BE CREATED IN PHP///////////////////////////////////////////////////////////////////////////////////////


//     var ajaxa = ajaxObj("POST", frenetic.root + "/php_parsers/get_user_avatar.php");
//    ajaxa.onreadystatechange = function(){
//        if(ajaxReturn(ajaxa) === true){
//            //frenetic['page_owner'].score = parseFloat(ajax.responeText);
//             
//        }
//    };
//    ajaxa.send("username="+username);

}
;


function get_profile_src() {
    var src = '<?php echo $profile_pic_src ?>';
    return src;
}


//direct all mobile or non chrome users to usechrome.php
function detectmob() {
    if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)
            ) {
        return true;
    } else if (window.screen.width < 600) {
        return true;
    } else {
        return false;
    }
}

function get_nofication_count() {
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_notifications.php");
    ajax.onreadystatechange = function() {

        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === '0') {
                $('.notification_count').text('').removeClass('new');
            } else {
                $('.notification_count').text(ajax.responseText).addClass('new');
            }
        }
    };
    ajax.send("get_note_count=" + frenetic['user'].username);
}

function report_bug() {
    //something with ajax
    var subject = $('#debug_form input').val();
    var message = $('#debug_form textarea').val();
    var browser = BrowserDetect.browser;
    var mobile = detectmob(); //'true' or 'false'
    var width = $(window).width();
    var height = $(window).height();

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/save_debug_img.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            $('#debug_form input').val('');
            $('#debug_form textarea').val('');
        }
    };

    ajax.send("report_bug=yes" + "&subject=" + subject + "&message=" + message + "&browser=" + browser + "&mobile=" + mobile + "&window_width=" + width + "&window_height=" + height);
}
  