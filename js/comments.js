//adds functionality to reply area on main description

function submit_comment(new_comment, type) {
    
    var link = frenetic.root + '/' + new_comment.media_type + '/' + new_comment.content_id;
    
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/comment_system.php");

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            
            var response = ajax.responseText.split("||");

            switch (type) {
                case 'fake':

                    break;
                case 'starter':
                    $('#comment_viewer').prepend('<div class="conversation_container">' + response[0] + '</div>');
                    break;
                case 'reply':
                    $(response[0]).insertAfter(new_comment.position);
                    break;
            }

            //gives scrolling based on overflow
            resizeCommentContainer();

            $('.delete_comment').click(function() {
                delete_content($(this).parents('.comment_wrapper').attr('comment_id'), 'comment');
                $(this).parents('.comment_wrapper.just_added').remove();
                resizeCommentContainer();
            });
            //generate notifications
            var noti_jax = ajaxObj("POST", frenetic.root + "/php_parsers/gen_notifications.php");
            noti_jax.onreadystatechange = function() {
                if (ajaxReturn(noti_jax) === true) {
                    //alert(noti_jax.responseText);
                }
            };
            //alert('generating notifications');
            noti_jax.send("comment_unique=" + response[1] + "&content_unique=" + new_comment.content_id + "&original_poster=" + response[2] + "&media_type=" + new_comment.media_type + "&parent_unique=" + new_comment.pid + "&category=comment"  + "&link=" + link);
        }
    };



    ajax.send("action=" + "comment_post" + "&parent_unique=" + new_comment.pid + "&content_unique=" + new_comment.content_id + "&data=" + new_comment.text + "&content_type=" + new_comment.media_type + "&level=" + new_comment.level + "&url=" + window.location.href);

}

function addCommentListeners() {
    $('.comment_container .previous_button, .comment_container .next_button').click(function() {
        retrieve_comments("default", $(this), $(this).attr('direction'));
    });

    $('.comment_container img').click(function() {
        var close_info = new Object();
        close_info.upstream = 'modal_go_to_person_click';
        frenetic.modal.viewer.close();
        go_to_person($(this).attr('poster'));
       
    });

    $('.delete_comment').click(function() {
        //alert($(this).parents('.comment_wrapper').attr('comment_id'));
        delete_content($(this).parents('.comment_wrapper').attr('comment_id'), 'comment');
        resizeCommentContainer();
    });

    $('.comment_actions .share').click(function() {
        share($(this));
    });


}

function resizeCommentContainer() {

//    if ($('#modal_ad img').length > 0) {
//        $('#modal_ad').css('height', $('#modal_ad').width() * 0.1236263736263736);
//        $('#comment_description_container').css({'bottom': $('#modal_ad').height() + 0.5 * parseInt($('html').css('font-size'), 10)});
//    }
//    ;
//
//    $('#comment_container').animate({'height': $('#comment_description_container').innerHeight() - $('#description_input').outerHeight(true) - 5}, 0, function() {
//        if ($('#comment_container').overflow() === true) {
//            $('#comment_container').css({"overflow-y": "scroll", "overflow-x": "hidden"});
//            $('#comment_container').children().css({'margin-right': '0.1rem'});
//        } else {
//            $('#comment_container').css({"overflow-y": "hidden", "overflow-x": "hidden"});
//            $('#comment_container').children().css({'margin-right': ''});
//        }
//
//    });

}


function retrieve_comments(content, timing, button) {

    //elem, direction, cid

    var opts = {
        lines: 13, // The number of lines to draw
        length: 20, // The length of each line
        width: 10, // The line thickness
        radius: 30, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#000', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent in px
        left: '50%' // Left position relative to parent in px
    };

    var target = document.getElementById('comment_container');
    var spinner_comment = new Spinner(opts).spin(target);

    //$('#comment_container').append('<div class="loading_container"><img class="loading_gif" src="sourceImagery/round_loading_gif_large.gif"></div>');

    var nth_sibling;

    var action = "retrieve_comments";
    
    var new_comment = new Object();

    switch (timing) {
        case 'link_load':
            action = "retrieve_specific_convo";
            new_comment.cid = content.content_id;
            break;
        case 'initial':
            new_comment.content_id = content.content_id;
            new_comment.media_type = content.content_type;
            new_comment.pid = new_comment.content_id;
            new_comment.level = 0;
            nth_sibling = 1;
            break;
        case 'default':
            //triggered on next or previous buttons
            new_comment.content_id = content.content_id; //content id
            new_comment.media_type = content.content_type;
            new_comment.pid = content.parent_id;
            new_comment.level =content.level;
            new_comment.cid = content.comment_id;

            if (button.attr('direction') === 'next') {
                nth_sibling = content.sid + 1;
            } else if (button.attr('direction') === 'previous') {
                nth_sibling = content.sid - 1;
            }

            break;
    }

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/comment_system.php");
    ajax.onreadystatechange = function() {

        if (ajaxReturn(ajax) === true) {

            var json = JSON.parse(ajax.responseText);
            
            spinner_comment.stop();

            switch (timing) {
                
                case 'initial':                    

                    //if initial comment load
                    var container = document.getElementById('comment_viewer');
                                        
                    //json[x] is number of conversations
                    //json[x][y] is comment in conversation

                    json.num_comments = parseInt(json.num_comments);

                    if (json.num_comments === 0) {
                        if (window.innerWidth > 500) {
                            if ($('#description_input .starter_comment').attr('status') === 'closed') {
                                $('#description_input .starter_comment').trigger('click');
                            }
                            $('#comment_trigger .centered').text('Be the first to comment.');
                        } else {
                            $('#comment_trigger .centered').text('Comments');
                        }

                    } else {

                        if (json.num_comments === 1) {
                            $('#comment_trigger .centered').text(json.num_comments + ' comment');
                        } else {
                            $('#comment_trigger .centered').text(json.num_comments + ' comments');
                        }
                        
                        for (var i = 0; i < json[0].length; i++) {

                            var conversation_container = document.createElement('div');
                            $(conversation_container).addClass('conversation_container');

                            container.appendChild(conversation_container);

                            for (var j = 0; j < json[0][i].length; j++) {
                                var content = new datawrapper_comment(json[0][i][j]);

                                var input_comment = new comment(content);
                                conversation_container.appendChild(input_comment);
                            }
                        }
                    }
                    
                   
                    break;
                case 'default':                    

                    //if cycling through comments
                    var container = button.parents('.conversation_container'); //current comment's conversation container
                    container.nextAll().remove(); //removes other conversations

                    if (new_comment.level === 1) {
                        container.empty();
                    } else {
                        button.parents('.comment_wrapper').nextAll().remove();
                        button.parents('.comment_wrapper').remove();
                    }
                    
                    for (var i = 0; i < json[0].length; i++) {                      

                        for (var j = 0; j < json[0][i].length; j++) {
                            var content = new datawrapper_comment(json[0][i][j]);

                            var input_comment = new comment(content);
                            container[0].appendChild(input_comment);
                            
                        }

                    }
                    
                    break;
            }

            resizeCommentContainer();

        }
    };
    //alert("action=" + action + "&parent_id=" + new_comment.pid + "&content_id=" + new_comment.content_id + "&sibling_id=" + nth_sibling + "&level=" + new_comment.level + "&timing=" + timing + "&unique_id=" + new_comment.cid);
    ajax.send("action=" + action + "&parent_id=" + new_comment.pid + "&content_id=" + new_comment.content_id + "&sibling_id=" + nth_sibling + "&level=" + new_comment.level + "&timing=" + timing + "&unique_id=" + new_comment.cid);

}
