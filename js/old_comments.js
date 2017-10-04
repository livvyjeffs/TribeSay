//adds functionality to reply area on main description
function reply(elem) {
    //if closed AND a starter comment
    if (elem.attr("status") === "closed" && elem.hasClass('starter_comment')) {
        ////console.log('closed starter_comment clicked');
        //starter comment
        elem.attr({"status": "open", "rows": 5});
        elem.siblings('.comment_options').css("display", "block");

        //closes the starter reply on click out
        $('.modalBackground').on("click", function(event) {
            //if what you click is a .starter_comment...
            if ($(event.target).hasClass('starter_comment') === false) {
                ////console.log(event.target.getAttribute('class'))
                close_reply('starter');
            } else {
                $(event.target).focus();
            }

            if ($(event.target).hasClass('reply')) {
                //alert('detecting')
                //alert($(event.target).parent().siblings('textarea').attr('rows'))
                $(event.target).parent().siblings('textarea').focus();
                return;
            }

            if (event.target.type === 'textarea') {
                $(event.target).focus();
                return;
            }
        });

    } else if(elem.hasClass('reply')) {
        ////console.log('comment reply button clicked');
        //reply to comment comments
        if (elem.attr("status") === "closed") {
            elem.text("Cancel").attr("status", "opened");
            elem.parent().parent().append("<textarea rows='4' autofocus></textarea><div type='submit' class='submit_comment_reply' onclick='submit_comment($(this))'>Submit</div>");

        } else if (elem.attr("status") === "opened") {
            elem.text("Reply").attr("status", "closed");
            elem.parent().parent().children('.comment textarea, .submit_comment_reply').remove();
        }
    }
    ;

    //console.log('reply onResize(simple)');
    onResize('simple');
}

function close_reply(type) {

    if (type === 'starter') {
        $('.starter_comment').attr({"status": "closed", "rows": 1});
        $('.comment_options').css("display", "none");
    }

    //console.log('close_reply onResize(simple)');
    onResize('simple');
}

function submit_comment(elem, type, id, comment, media) {
    var pid; //comment id
    var value;
    var level;
    var tags = $('#content_tags .tag_text');
    
    if (type === 'starter') {
        
        ga('send', 'event', 'single_click', 'post_comment', 'start');
        tags.each(function(){
            ga('send', 'event', 'post_comment', 'start', $(this).attr('title'));
        });
        
        
        pid = elem.parents('#description_input').find('textarea').attr('cid');
        value = elem.parents('#description_input').find('textarea').val();
        level = parseInt(elem.parents('#description_input').find('textarea').attr('level'), 10);
    } else if (elem !== null) {
        
        ga('send', 'event', 'single_click', 'post_comment', 'reply');
        tags.each(function(){
            ga('send', 'event', 'post_comment', 'reply', $(this).attr('title'));
        });
        
        pid = elem.parent().children('.comment').attr('cid');
        value = elem.siblings('textarea').val();
        level = parseInt(elem.parent().children('.comment').attr('level'), 10);
    } else {
        pid = id;
        uid = pid;
        value = comment;
        level = 0;
    }
    if(type === "starter" || elem !== null){
        value = value.replace(/\n/g, "<br />");
        var vis_level = level + 1;
        var uid = $('.modalBackground').attr('uid'); //content unique
        var media = $('.modalBackground').attr('media');
        var comment_text = "<div class='comment_container just_added' level=" + vis_level + ">\n\
                            <div class='vote_container'><div class='upvote' onclick='voteComment($(this))' previous='no' token='UP'></div>\n\
                            <div class='downvote' onclick='voteComment($(this))' previous='no' token='DOWN'></div></div>\n\
                            <div class='comment' votes='1' level=" + vis_level + ">\n\
                            <span class='comment_info'>posted by you just now, <span class='vote_state'>1</span> vote</span><br>\n\
                            " + value + "</div></div>";
        var container;
        if (level === 0) {
            container = $('#comment_viewer');
            container.prepend(comment_text);
        } else {
            container = elem.parents('.comment');
            container.after(comment_text);
            container.find('.reply').trigger('click');
        }
    }
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/comment_system.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if(ajax.resposeText === "return"){
                return;
            }    
            var container;
            if (level === 0) {
                close_reply('starter');
                $('.starter_comment').removeAttr('value');
            } else {
                container.find('.reply').trigger('click');
            }
        }
    };
    ajax.send("action=" + "comment_post" + "&parent_unique=" + pid + "&content_unique=" + uid + "&data=" + value + "&content_type=" + media + "&level=" + level);
}

function retrieve_comments(timing, elem, direction, cid) {
    var uid; //content unique
    var pid; //parent comment
    var level;
    var nth_sibling;

    if (cid === "") {
        //comment unique
        timing = 'initial';
    }

    uid = $('.modalBackground').attr('uid'); //content id
    var action = "retrieve_comments";
    switch (timing) {
        case 'link_load':
            action = "retrieve_specific_convo";
            break;
        case 'initial':
            pid = uid;
            level = 0;
            nth_sibling = 1;
            break;
        case 'default':
            //triggered on next or previous buttons
            var comment = elem.siblings('.comment');
            pid = comment.attr('pid');
            level = comment.attr('level');
            if (direction === 'next') {
                nth_sibling = parseInt(comment.attr('sid'), 10) + 1;
            } else if (direction === 'previous') {
                nth_sibling = parseInt(comment.attr('sid'), 10) - 1;
            }


            break;
    }


    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/comment_system.php");
    ajax.onreadystatechange = function() {

        if (ajaxReturn(ajax) === true) {
            switch (timing) {
                case 'link_load':
                    $('#comment_viewer').append(ajax.responseText);
                    break;
                case 'initial':
                    $('#comment_viewer').append(ajax.responseText);
                    break;
                case 'default':

                    var container = elem.parents('.conversation_container');
                    elem.parents('.conversation_container').nextAll().remove();

                    if (elem.parent().attr('level') === '1') {
                        elem.parents('.conversation_container').children().remove();
                    } else {
                        elem.parent().nextAll().remove();
                        elem.parent().remove();
                    }
                    container.append(ajax.responseText);
                    break;
            }
            onResize();
            

        }
    };

    ajax.send("action=" + action + "&parent_id=" + pid + "&content_id=" + uid + "&sibling_id=" + nth_sibling + "&level=" + level + "&timing=" + timing + "&unique_id=" + cid);



}

jQuery(document).ready(function($) {

//1. detect width limit
//2. set view based on width limit
//3. set view options based on available views
//4. adjust formatting


//////////////////////////////////////////FUNCTION DEFINITIONS/////////////////////////////////////////////////////////////////////////////

    //define width limit
    var width_limit = 1520;

    //define set view function
    function setView(view) {
        $('#modal_viewer, #modal_viewer *').attr('view', view);
    }

    //define clear function
    //options for input are #comment_description_container and #content_description_container
    function clear(list) {
        //console.log('clear('+list+')');
        for (i = 0; i < list.length; i++) {
            if ($(list[i]).length > 0) {
                switch (list[i]) {
                    case '#comment_description_container':
                        $('#comment_container, #description_container').unwrap();
                        break;
                    case '#content_description_container':
                        $('#content_holder, #description_container').unwrap();
                        break;
                }
            }
        }
    }
    ;

    function wrap(list) {
        //console.log('wrap('+list+')');

        var wrapstring = "";
        var wrapcontainer = "";

        for (i = 0; i < list.length; i++) {
            switch (list[i]) {
                case 'comment':
                    wrapstring += '#comment_container';
                    wrapcontainer += 'comment_';
                    break;
                case 'content':
                    wrapstring += '#content_holder';
                    wrapcontainer += 'content_';
                    break;
                case 'description':
                    wrapstring += '#description_container';
                    wrapcontainer += 'description_';
                    break;
            }
            if (i < (list.length - 1)) {
                wrapstring += ', ';
            }
        }

        /*check if the wrapping container exists already*/
        if ($('#' + wrapcontainer + 'container').length === 0) {
            $(wrapstring).wrapAll("<div id='" + wrapcontainer + "container'></div>");
        }
    }
    ;

///////////////////////////////////////////////////////BEGIN ACTIONS///////////////////////////////////////////////////////////////////

    //set initial views based on screen size
    
    setView('aview');
    
    /*if ($(window).width() < width_limit) {
        setView('nview');
        //default views on every modal loadare 'nview' and 'cview'
    } else {
        setView('aview');
        $('.view_buttons[type="nview"]').attr('type', 'aview');
    }*/

    //define resize function, runs 1) once on load and 2) every time the screen is resized or 3) when called

    onResize = function(type) {
        //console.log('onResize('+type+') BEGIN');

        //view is default set by 1) window size then 2) a button the user clicks
        var view = $('#modal_viewer').attr("view");
        
        //a 'simple' resize is when the view is constant and things need to be minorly adjusted
        if (type !== 'simple') {
            //console.log('REMOVING STYLE');
            $('#modal_viewer * :not(".image_content")').removeAttr("style");
        }

        //if in nview move to aview on resize and vice-versa
        //cview is not changed by resizing
        /*if (view === 'nview' && $(window).width() > width_limit) {
            $('.view_buttons[type="nview"]').attr('type', 'aview');
            setView('aview');
            view = 'aview';
        } else if (view === 'aview' && $(window).width() < width_limit) {
            $('.view_buttons[type="aview"]').attr('type', 'nview');
            setView('nview');
            view = 'nview';
        }*/

        switch (view) {
            case 'aview': //largest view in large screen

                //clear all wrapped items
                clear(['#comment_description_container', '#content_description_container']);

                //re-wrap items
                wrap(['comment', 'description']);

                //change from png to iframe in ARTICLE
                $('#content_holder[media="article"] iframe, #content_holder[media="article"] .iframe_link').css('display', 'block');
                //$('#content_holder[media="article"] img').css({'display': 'none', 'margin': '0px', 'width': '100%', 'height': 'auto', 'visibility': 'visible'});

                //set image size of image
                if ($('#content_holder').attr('media') === 'image') {
                    //sets content image size for media type image
                    setImageSize();
                }

                //add or remove items
                $('#go_to_comments').remove();
                
                 if ($('#comment_container #description_input').length !== 0) {
                    $('#comment_container #description_input').remove();
                    $('#description_container #description_input').css('display', '');
                }
                
                //change heights
                $('#comment_container').css("height", ($('#comment_description_container').innerHeight() - $('#description_container').outerHeight(true)));

                break;

            case 'nview': //view with only content and description

                //clear wrapped items
                clear(['#comment_description_container', '#content_description_container']);

                //re-wrap items
                //no items to wrap

                //change from png to iframe in ARTICLE
                $('#content_holder[media="article"] iframe, #content_holder[media="article"] .iframe_link').css('display', 'block');
                //$('#content_holder[media="article"] img').css({'display': 'none', 'margin': '0px', 'width': '100%', 'height': 'auto', 'visibility': 'visible'});

                //set image size of image
                if ($('#content_holder').attr('media') === 'image' && $('.image_content[sized]').length !== 0) {
                    //sets content image size for media type image
                    setImageSize();
                }

                //add items
                if ($("#go_to_comments").length === 0) {
                    $('#modal_viewer').append('<div id="go_to_comments"><div class="centered"><span id="number_viewers">10</span> braintribers are viewing now - <span id="join_the_conversation">join the conversation!</span></div></div>');
                }

                $('#join_the_conversation').click(function() {
                    $('.view_buttons[type="cview"]').trigger('click');
                });
                
                if ($('#comment_container #description_input').length !== 0) {
                    $('#comment_container #description_input').remove();
                    $('#description_container #description_input').css('display', '');
                }

                //change heights
                $('.photo_viewerContainer #description_text, .video_viewerContainer #description_text').css("height", $('#description_container').height());
                $('#go_to_comments').css("height", ($('#modal_viewer').height() - $('#content_holder').outerHeight(true) - $('#description_container').outerHeight(true)));

                break;
            case 'cview': //small view with all comments

                //clear wrapped items
                clear(['#comment_description_container']);

                //wrap items if not already wrapped
                wrap(['content', 'description']);

                //change from iframe to png in ARTICLE
                $('#content_holder[media="article"] iframe, #content_holder[media="article"] .iframe_link').css('display', 'none');
                $('#content_holder[media="article"] img').css({'display': 'block', 'margin': '0px', 'width': '100%', 'height': 'auto', 'visibility': 'visible'});

                //set image size of image
                if ($('#content_holder').attr('media') === 'image') {
                    //sets content image size for media type image
                    setImageSize();
                }
                
                //add/remove items
                $('#go_to_comments').remove();
                
                if ($('#comment_container #description_input').length === 0) {
                    $('#comment_container').prepend($('#description_input').clone(true));
                }
                
                $('#description_container #description_input').css('display', 'none');
                
                                 
                //change heights
                $('#comment_viewer').css("height", ($('#comment_container').height() - $('#comment_container #description_input').outerHeight(true)));
                $('#description_container').css({"height": ($('#content_description_container').height() - $('#content_holder').outerHeight(true)),'max-height': 'none'});
                
                break;
        }

        //gives scrolling based on overflow
        $('#description_text, #comment_viewer').each(function() {
            //alert($(this).attr('id') + " is overflowing: " + $(this).overflow());
            if ($(this).overflow() === true) {
                $(this).css("overflow-y", "scroll");
            }

        });

    //console.log('onResize('+type+') END')    
    };

//view state
    $('.view_buttons').click(function() {
        setView($(this).attr("type"));
        onResize();
    });

    $(document).ready(onResize);
    $(window).resize(onResize);

});