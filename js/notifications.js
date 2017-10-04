
$(document).ready(function() {
    
    get_nofication_count();

    $('.mark_all').click(function() {
        if($(this).hasClass('unread')){
            mark_all_as_unread();
        }else{
            mark_all_as_read();
        }
        
    });

    retrieve_notifications();

    $('#post_button, #search_bar, #tribe_bar').remove();

    if ($('.notification_container').overflow()) {
        $('.notification_container').css({"overflow-y": "scroll", "overflow-x": "visible"});
    }
});

$(window).resize(function() {
    
    get_nofication_count();
    
    if ($('.notification_container').overflow()) {
        $('.notification_container').css({"overflow-y": "scroll", "overflow-x": "visible"});
    } else {
        $('.notification_container').removeAttr('style');
    }
});

function mark_all_as_read() {
    $('.notification').each(function() {

        $(this).removeClass('new').addClass('read');
         $(this).find('.mark').text('mark as new');

        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
        ajax.onreadystatechange = function() {

            if (ajaxReturn(ajax) === true) {
                $('.note_count').text("").removeClass('new');
                get_nofication_count();
            }
        };

        ajax.send("read_note=all");
    });
}

function mark_as_read(elem) {
    elem.each(function() {

        $(this).removeClass('new').addClass('read');
        $(this).find('.mark').text('mark as new');

        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
        ajax.onreadystatechange = function() {

            if (ajaxReturn(ajax) === true) {
                get_nofication_count();
            }

        };

        ajax.send("read_note=" + $(this).parents('.notification_wrapper').attr('nid'));
    });
}

function mark_as_unread(elem) {
    elem.each(function() {

        $(this).removeClass('read').addClass('new');
        $(this).find('.mark').text('mark as read');

        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
        ajax.onreadystatechange = function() {

            if (ajaxReturn(ajax) === true) {
                get_nofication_count();
            }

        };

        ajax.send("mark_new=" + $(this).parents('.notification_wrapper').attr('nid'));
    });
}

function mark_all_as_unread() {
    $('.notification').each(function() {

        $(this).removeClass('read').addClass('new');
        $(this).find('.mark').text('mark as read');

        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
        ajax.onreadystatechange = function() {

            if (ajaxReturn(ajax) === true) {
                get_nofication_count();
            }
        };
        ajax.send("mark_new=" + $(this).parents('.notification_wrapper').attr('nid'));
        
    });
}

function retrieve_notifications() {

    var container = $('.notification_container');

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_notifications.php");
    ajax.onreadystatechange = function() {

        if (ajaxReturn(ajax) === true) {
            
            //console.log(ajax.responseText)
            var json = JSON.parse(ajax.responseText);
            
            if (json.length === 0) {
                
                container.append('<div class="no_notifications"><p>No new notifications yet. Don\'t be shy, get out there and start posting!</p></div>');

            } else {


                for (var i = 0; i < json.length; i++) {                    
                    
                    var data = new datawrapper_notification(json[i]);
                    
                    container[0].appendChild(notification(data));                   

                    if ($('.notification_container').overflow()) {
                        $('.notification_container').css({"overflow-y": "scroll", "overflow-x": "visible"});
                    } else {
                        $('.notification_container').removeAttr('style');
                    }                   
               

                }
            }
        }
    };

    ajax.send("generate_notifications=getting");


}