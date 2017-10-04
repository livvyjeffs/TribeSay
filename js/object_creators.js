

function tag_module(name, location, search) {

    var text, action, module;

    text = document.createElement('div');
    $(text).addClass('tag_text button').attr({'title': 'Click for more "' + name + '" news.', 'draggable': 'true', 'object': 'tag', 'tag': name}).text(name);

    var filter_tags = get_filter_tags();
    for (var i = 0; i < filter_tags.length; i++) {
        if (name === filter_tags[i]) {
            $(text).addClass('filter');
        }
    }

    module = document.createElement('div');
    $(module).addClass('tag_module').attr({'object': 'tag'});

    action = document.createElement('div');
    $(action).addClass('button');

    module.appendChild(text);
    module.appendChild(action);

    switch (location) {
        case 'media_container':
            $(text).attr('type', 'stream');

            $(text).hover(function() {
                $(this).addClass('hovering');
            }, function() {
                 $(this).removeClass('hovering');
            });

            text.addEventListener('mouseup', function(e) {
                add_to_filter(e, name);

                //ANALYTICS
                record_content('consumption', 'add_filter_tag', 'from_stream_desktop', {'tag': name});
            });

            text.addEventListener('dragstart', function(e) {
                drag(e);
            });

            return text;
            break;
        case 'mobile':
            text.addEventListener("click", function() {
                add_to_filter($(this));
                //ANALYTICS
                record_content('consumption', 'add_filter_tag', 'from_stream_mobile', {'tag': name});
            });

            $(text).on('vmousedown', function() {
                $(this).addClass('active');
            });

            return text;
            break;
        case 'mobile_search':            
            text.addEventListener("click", function() {
                add_to_filter($(this));
                $('#search_icon').trigger('click');
                
                //ANALYTICS
                record_content('consumption', 'add_filter_tag', 'search_mobile', {'tag': name});
            });
            
            $(text).html(search.html + '<span class="amount"> x ' + search.amount + '</span>');

            return text;
            break;
        case 'desktop_search':
            $(text).click(function(e) {
                
                add_to_filter(e, name);
                mobile_searchbar.close();
                
                //ANALYTICS
                record_content('consumption', 'add_filter_tag', 'search_desktop', {'tag': name});

            }).hover(function() {
                $(this).addClass('active');
            }, function() {
                $(this).removeClass('active');
            });

            $(text).html(search.html + '<span class="amount"> x ' + search.amount + '</span>');

            return text;
            break;
        case 'modal':
            $(text).attr('type', 'modal');

            $(text).hover(function() {
                $(this).addClass('hovering');
            }, function() {
                $(this).removeClass('hovering');
            });

            $(text).click(function(e) {
                var close_info = new Object();
                close_info.upstream = 'modal_filter_tag_click';
                
                frenetic.modal.viewer.close();
                
                add_to_filter(e, name);
                
                //ANALYTICS
                record_content('consumption', 'add_filter_tag', 'from_modal', {'tag': name});
                
            });

            return text;
            break;
        case 'mobile_filter':
            $(text).attr('type', 'filter');
            $(action).addClass('delete_tag').append('<span>x</span>');
            action.addEventListener('click', function() {
                clear_filter();
                
                //ANALYTICS
                record_content('consumption', 'remove_filter_tag', 'filter_mobile', {'tag': name});
            });
            
            $(action).on('vmousedown', function() {
                $(this).addClass('active');
            });
            
            return module;
            break;
        case 'tribe_bar':
            $(text).attr('type', 'filter').removeClass('button');
            $(action).addClass('delete-tag').text('x');
            action.addEventListener('click', function() {
                $(module).remove();
                remove_from_filter(name);
                
                //ANALYTICS
                record_content('consumption', 'remove_filter_tag', 'filter_desktop', {'tag': name});
            });
            return module;
            break;
        case 'upload':
            $(text).attr('type', 'upload');
            $(action).addClass('delete-tag').text('x').click(function() {
                $(this).parents('.tag_module').remove();
                resize_tag_suggestor();
            });
            return module;
            break;       
    }

}

function notification(data) {

    var icon = new Image();

    var wrapper, logo_container, notification, note_actions;
 

    wrapper = document.createElement('div');
    $(wrapper).addClass('notification_wrapper').attr({'nid': data.note_id});

    logo_container = document.createElement('div');
    $(logo_container).addClass('logo_container ' + data.category);

    note_actions = document.createElement('div');
    $(note_actions).addClass('notification_actions');

    var mark_button = document.createElement('div');
    $(mark_button).addClass('mark button');
    $(mark_button).text('mark as ' + data.mark_status);
    $(mark_button).click(function() {
        var note_data = $(this).parents('.notification');
        if (note_data.hasClass('new')) {
            mark_as_read(note_data);
            $(this).text('mark as new');
        } else {
            mark_as_unread(note_data);
            $(this).text('mark as read');
        }
    });

    note_actions.appendChild(mark_button);

    notification = document.createElement('div');
    $(notification).addClass('notification ' + data.read_status + ' ' + data.category);
    notification.appendChild(note_actions);

    wrapper.appendChild(logo_container);
    wrapper.appendChild(notification);    

    if (data.category === 'comment') {

        icon.src = frenetic.root + "/sourceImagery/navigation/notifications_icon.png";

        $(notification).append('<p>&#8211; your ' + data.target + ' ' + data.title + '</p>\n\
                            <div class="time_ago">' + data.time_ago + '</div>\n\
                            <img class="profile" src="' + data.avatar + '"> <a href="index.php?p=' + data.poster + '">' + data.poster + '</a> said: <br> "' + data.text + '"\n\
                            <div class="go_to_comment"><a href="' + data.url + '" target="_blank">go to comment &#10141;</a></div>');


    } else if (data.category === 'vote') {
        
        icon.src = frenetic.root + "/sourceImagery/black_heart.png";

        $(notification).append('<div class="time_ago">' + data.time_ago + '</div>\n\
                                        <img class="profile" src="' + data.avatar + '"> <a href="index.php?p=' + data.poster + '">' + data.poster + '</a> liked your ' + data.target + ' ' + data.title + '\n\
                                        <div class="go_to_comment" target="_blank"><a href="' + data.url + '" target="_blank">go to your ' + data.target + ' &#10141;</a></div>');
    }

    $(notification).hover(function() {
        $(this).parents('.notification_wrapper').find('.notification_actions').css('display', 'block');
    }, function() {
        $(this).parents('.notification_wrapper').find('.notification_actions').removeAttr('style');
    });

    $(notification).find('.go_to_comment').click(function() {
        if ($(this).parents('.notification').hasClass('new')) {
            $(this).parents('.notification').find('.mark').trigger('click');
        }
    });

    return wrapper;
    
}

function profile_tile() {

    var container, image, info, clip, follow;      

    follow = document.createElement('div');
    $(follow).addClass('follow button');

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/friend_checker.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
           
            switch (ajax.responseText) {
                case 'friend':
                    $(follow).text('-unfollow');
                    frenetic['page_owner'].follow_action = 'unfollow';
                    break;
                case 'stranger':
                    $(follow).text('+follow');
                    frenetic['page_owner'].follow_action = 'follow';
                    break;
                case'self':
                    break;
            }
        }
    };

    follow.addEventListener('click', function() {
                
        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/friend_system.php");
        ajax.onreadystatechange = function() {

            if (ajaxReturn(ajax) === true) {
                
                if (ajax.responseText === "friend_request_sent") {
                    alert("You are now following " + frenetic['page_owner'].username + ".");
                    $(follow).text('-unfollow');
                    frenetic['page_owner'].follow_action = 'unfollow';
                } else if (ajax.responseText === "unfollow_ok") {
                    alert("You are no longer following " + frenetic['page_owner'].username + ".");
                    $(follow).text('+follow');
                    frenetic['page_owner'].follow_action = 'follow';
                } else {
                    //alert(ajax.responseText);
                }
            }
        };
        ajax.send("type=" + frenetic['page_owner'].follow_action + "&user=" + frenetic['page_owner'].username);
    });

    ajax.send("username=" + frenetic['page_owner'].username);


    container = document.createElement('div');
    $(container).addClass('media_container profile_tile');

    clip = new Image();
    $(clip).addClass('paper_clip');
    clip.src = frenetic.root + '/sourceImagery/paperclip.png';

    image = document.createElement('img');
    image.src = frenetic['page_owner'].avatar;


    var height = frenetic['page_owner'].avatar_ratio * (frenetic.column_width - 2);

    //console.log(frenetic['page_owner'].avatar_ratio + 'xx' + height);

    $(image).addClass('button').css({'height': frenetic['page_owner'].avatar_ratio * (frenetic.column_width - 22)});
    image.onload = function() {
        $(this).css('height', 'auto');
    };

    info = document.createElement('div');
    $(info).addClass('info_nugget').html(frenetic['page_owner'].username + '\'s page');// &#8226; ' + frenetic['page_owner'].score + ' tgems');    
     
    container.appendChild(image);
    container.appendChild(clip);
    container.appendChild(info);
    container.appendChild(follow);    
    
    return container;

}

function friends_tile() {

    var container, div, clip;

    container = document.createElement('div');
    $(container).addClass('media_container friend_tile');
    
    div = document.createElement('div');
    $(div).html('Your Tribe');
    
    clip = new Image();
    $(clip).addClass('paper_clip');
    clip.src = frenetic.root + '/sourceImagery/paperclip.png';
    
    container.appendChild(div);
    container.appendChild(clip)
    
    return container;

}

function media_container(data) {

    var content = new datawrapper_media_container(data);
        
    var POSTER = new Object();
    POSTER.username = content.poster;
    POSTER.avatar = content.avatar;
    POSTER.ratio = content.avatar_ratio;

    var container, header, delete_button, image, glass, votes, upvote, share, info, tag_block, poster, comment, action_display, link;

    container = document.createElement('div');
    $(container).addClass('media_container').attr({
        'order': content.order,
        'uid': content.uid,
        'media': content.media,
        'score': content.score
    });

    if (content.media === 'comment') {

        image = document.createElement('img');
        $(image).addClass('button');
        image.src = content.avatar;

        image.addEventListener('click', function() {
            go_to_person(POSTER);
        });

        poster = document.createElement('span');
        $(poster).addClass('button poster').text(content.poster + ', ' + content.vote_state + ' vote' + content.vote_state_plural);
        poster.addEventListener('click', function() {
            go_to_person(POSTER);
        });

        comment = document.createElement('div');
        comment.appendChild(image);
        comment.appendChild(poster);
        $(comment).addClass('comment button modal_trigger').append('<br>'+content.comment_text);             
        
        comment.addEventListener('click', function(e) {
            if ($(e.target).hasClass('comment')) {

                var ajax = new ajaxObj("POST", frenetic.root + "/php_parsers/get_link_data.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {

                        var json = JSON.parse(ajax.responseText);
                        var content = new datawrapper_media_container(json);
                        
                        content.order = content.order;
                        content.stream_type = content.stream_type; 
                       
                        frenetic.modal.viewer.open(content);
                    }
                };
                ajax.send("uid=" + content.content_id + "&media=" + content.content_type + "&cid=" + content.uid);

            }
        });       
        
        container.appendChild(comment);

        return container;

    }else if (content.media === 'event'){
        
    }

    link = document.createElement('a');
    link.href = frenetic.root + '/' + content.media + '/' + content.uid;
    link.target = '_blank';

    //disable left click directing away
    $(link).click(function(e) {
        e.preventDefault();
    });   
  
    header = document.createElement('h1');
    $(header).addClass('button').text(content.title);
    
   
    
//    header.addEventListener('click', function() {
//        frenetic.modal.viewer.open(content);
//    });

    image = document.createElement('img');
    image.src = content.image_thumbnail;
    $(image).addClass('modal_trigger').css({'background-color': 'rgb(' + content.rgb_r + ',' + content.rgb_g + ',' + content.rgb_b + ')',
        'height': Math.ceil(data.ratio * (frenetic.column_width - 2))});
    
    console.log(frenetic.column_width)
    
    
//
//    $(image).click(function() {
//        frenetic.modal.viewer.open(content);
//    });

       
     $(header).add($(image)).click(function() {
        frenetic.modal.viewer.open(content);
    });

    image.onload = function() {
        $(this).css('height', 'auto');
    };

    delete_button = document.createElement('div');
    $(delete_button).addClass('delete button');
    delete_button.addEventListener('click', function() {
        delete_content(content.uid, content.media);
    });

    glass = document.createElement('div');

    /////////////////////////////////////////////////logging for martin

    var conlog = new Object();
    conlog.score = content.score;
    conlog.media = content.media;
    conlog.stream_type = content.stream_type;
    conlog.votes = content.vote_state;
    conlog.recency = content.time_ago;
    
    /////////////////////////////////////////////////end logging

    $(glass).addClass('glass');//.text(content.score + ': ' + content.vote_state + ' vote' + content.vote_state_plural + ', ' + content.time_ago);

    votes = document.createElement('div');
    $(votes).addClass('vote_container').attr({'type': 'content', 'previous': content.previous_vote, 'token': 'UP'});

    upvote = document.createElement('div');
    $(upvote).addClass('upvote').attr({
        'token': 'UP',
        'previous': content.previous_vote
    });

    upvote.addEventListener('click', function() {
        postVote(content, 'UP');
    });


    share = document.createElement('div');
    $(share).addClass('share');
    
    
    glass.appendChild(image);
    //glass.appendChild(votes);
    //glass.appendChild(share);
    
    
    if(frenetic['user'].username === content.poster){
         glass.appendChild(delete_button);
    }
    
      var vote_plural;
    if (content.vote_state === 1) {
        vote_plural = '';
    } else {
        vote_plural = 's';
    }

    poster = document.createElement('span');
    $(poster).addClass('button poster').html('<b>' + content.poster + '</b>');
    poster.addEventListener('click', function() {
        if (frenetic['user'].login_status === 'not_logged_in') {
            frenetic.modal.login.open('follow ' + content.poster + ' on TribeSay');
            return;
        }
        go_to_person(POSTER);
    });

    $(poster).hover(function() {
        $(this).css({'color': 'rgba(25,25,25,0.5)'});
    }, function() {
        $(this).removeAttr('style');
    });

    info = document.createElement('div');
    $(info).addClass('info_nugget');
    $(info).append('<span>posted by ');
    info.appendChild(poster);
    $(info).append('<br>' + content.time_ago + ', <span class="vote_tally">' + content.vote_state + '</span> vote' + vote_plural + '</span>');

    info.addEventListener('click', function(e) {
        if (!$(e.target).parent().hasClass('poster')) {
            frenetic.modal.login.open(content);
        }
    });


    // action_display = $(document.createElement('div')).addClass('action_display').append($('<div class="no_empty pseudo_before vmiddle"></div><div class="vote_tally centered">' + content.vote_state + '</div><label>vote' + vote_plural + '</label>')).append($(votes));
//this.event_today = this.event_tonight = this.event_this_weekend
    tag_block = document.createElement('div');

    for (var i = 0; i < eval(content.tags).length; i++) {
        tag_block.appendChild(tag_module(content.tags[i], 'media_container'));
    }

    if (content.media === 'event') {
        
        console.log(content);

        if (content.event_tonight) {
            $(glass).append($('<div class="calendar" style="padding: 0 0.5rem"><div class="no_empty pseudo_before vmiddle"></div>Tonight</div>'));
        } else if (content.event_today) {
            $(glass).append($('<div class="calendar" style="padding: 0 0.5rem"><div class="no_empty pseudo_before vmiddle"></div>Today</div>'));
        } else if (content.event_tomorrow) {
            $(glass).append($('<div class="calendar" style="padding: 0 0.5rem"><div class="no_empty pseudo_before vmiddle"></div>Tomorrow</div>'));
        } else if (content.event_this_weekend) {
            $(glass).append($('<div class="calendar" style="padding: 0 0.5rem"><div class="no_empty pseudo_before vmiddle"></div>This Weekend</div>'));
        } else {
            $(glass).append($('<div class="calendar"><div class="month">' + content.event_month + '</div><div class="day">' + content.event_day + '</div></div>'));
        }
    } else {
        $(votes).append($('<div class="vote_tally centered">' + content.vote_state + '</div><div class="vote_thumb centered"></div>')).append($(upvote));

        $(tag_block).addClass('tag_container');
        $(glass).append($(votes));
    }

    $(header).append($('<div class="slider"></div>').append('<span>posted by ').append($(poster)).append(' ' + content.time_ago + ', <span class="vote_tally">' + content.vote_state + '</span> vote' + vote_plural + '</span>'));
    $(glass).hover(function() {
        $(this).parents('.media_container').find('.slider').addClass('opaque');
    }, function() {
        $(this).parents('.media_container').find('.slider').removeClass('opaque');
    });

    $(header).hover(function() {
        $(this).parents('.media_container').find('.slider').addClass('opaque');
    }, function() {
        $(this).parents('.media_container').find('.slider').removeClass('opaque');
    });
//
//    experiment_nugget.addEventListener('click', function(e) {
//        if (!$(e.target).parent().hasClass('poster')) {
//            frenetic.modal.viewer.open(content);
//        }
//    });
//    
//    experiment = document.createElement('div');
//    $(experiment).addClass('experiment');
//    
//    experiment.appendChild(votes);
//    $(experiment).append('<span class="vote_tally">' + content.vote_state + '</span>');
//    experiment_container.appendChild(experiment);
//    experiment_container.appendChild(experiment_nugget);
    
    
    //$(info).append(content.description);

    

    //container.appendChild(glass);
    //container.appendChild(header);

    link.appendChild(header);
    link.appendChild(glass);


    container.appendChild(link);

    //container.appendChild(info);
    container.appendChild(tag_block);


    return container;

}

function event_description(content){
    
    var container;
    container = document.createElement('div');
    $(container).addClass('event_container');

    var title, location, beginning_time, end_time, beginning_date, end_date, description, image, add_to_calendar, location_container;

    add_to_calendar = '<a href="http://example.com/link-to-your-event" title="Add to Calendar" class="addthisevent">\n\
            Add to Calendar\n\
            <span class="_start">' + content.event_begin + '</span>\n\
            <span class="_end">' + content.event_end + '</span>\n\
            <span class="_zonecode">35</span>\n\
            <span class="_summary">' + content.title + '</span>\n\
            <span class="_description">' + content.description + '</span>\n\
            <span class="_location">' + content.location + '</span>\n\
            <span class="_organizer">' + content.poster + '</span>\n\
            <!--<span class="_organizer_email">Organizer e-mail</span>\n\
            <span class="_facebook_event">http://www.facebook.com/events/160427380695693</span>-->\n\
            <span class="_all_day_event">false</span>\n\
            <span class="_date_format">DD/MM/YYYY</span>\n\
        </a>\n\
        <script type="text/javascript" src="http://js.addthisevent.com/atemay.js"></script>';

        
    title = document.createElement('h1');
    $(title).text(content.title);
    
    image = new Image();
    image.src = content.image_thumbnail;
    
    location_container = document.createElement('div');
    $(location_container).addClass('location_container');    
    $(location_container).append(add_to_calendar + '<div>' + content.event_begin + ' to ' + content.event_end + '</div>');

    location = document.createElement('div');

    description = document.createElement('div');
    $(description).text(content.description);

    container.appendChild(title);
    container.appendChild(image);
    container.appendChild(location_container);
    container.appendChild(description);
    return container;
    
}


function comment(content) {
    
    var array = ['conversation_container', 'comment_wrapper', 'comment_container', 'wrapper', 'comment_actions', 'vote_container', 'upvote', 'share', 'reply', 'comment_reply', 'reply_action', 'post_reply', 'delete_comment'];
    
    for (var i = 0; i < array.length; i++) {
        eval('var ' + array[i] + ' = document.createElement("div");');
        eval(array[i] + '.setAttribute("class","' + array[i] + '")');
    }

    var buttons = [upvote, share, reply, post_reply, delete_comment];
    for (var i = 0; i < buttons.length; i++) {
        $(buttons[i]).addClass('button');
    }

    conversation_container.appendChild(comment_wrapper);

    comment_wrapper.appendChild(comment_container);
    comment_wrapper.appendChild(comment_actions);
    comment_wrapper.appendChild(comment_reply);

    comment_container.appendChild(wrapper);
    var profile_pic = document.createElement('img');
    profile_pic.src = content.profile_pic;
    $(profile_pic).addClass('button');
    profile_pic.addEventListener('click', function() {
        go_to_person(POSTER);
        frenetic.modal.viewer.close();
    });

    var POSTER = new Object();
    POSTER.username = content.poster;
    POSTER.avatar = content.profile_pic;
    POSTER.avatar_ratio = content.avatar_ratio;

    profile_pic.onload = function() {
        $(comment_wrapper).addClass('loaded');
        resizeCommentContainer();
        if (POSTER.avatar_ratio === undefined) {
            POSTER.avatar_ratio = this.height / this.width;
        }
    };

    //navigation buttons
    
    var previousBtn = document.createElement('div');
    $(previousBtn).addClass('sub_comment_navigation previous_button button').attr({'direction': 'previous', 'sid': content.sid, 'total': content.total});
    
    previousBtn.addEventListener('click',function(){
        //retrieve previous comment
        retrieve_comments(content,'default',$(this));
    });

    var nextContainer = document.createElement('div');
    $(nextContainer).addClass('sub_comment_navigation next_button button').attr({'direction': 'next', 'sid': content.sid, 'total': content.total});
      nextContainer.addEventListener('click',function(){
        //retrieve previous comment
        retrieve_comments(content,'default',$(this));
    });
    

    var nextBtn = document.createElement('div');
    $(nextBtn).addClass('button');
    
    var fraction = document.createElement('div');
    $(fraction).addClass('fraction').html("<sup>" + content.sid + "</sup>&frasl;<sub>" + content.total + "</sub>");
    
    nextContainer.appendChild(nextBtn);
    nextContainer.appendChild(fraction);    

    if (content.sid > 1) {
        wrapper.appendChild(previousBtn);
    }

    if (content.total > content.sid) {
        wrapper.appendChild(nextContainer);
    }

    if (content.total === content.sid && content.total > 1) {
        previousBtn.appendChild(nextBtn);
        previousBtn.appendChild(fraction);
        $(previousBtn).addClass('solo');
        wrapper.appendChild(previousBtn);
    }

//end navigation buttons

    wrapper.appendChild(profile_pic);
    var comment_info = document.createElement('span');
    $(comment_info).addClass('comment_info').html(content.poster + ', <span class="vote_state">' + content.vote_state + ' vote' + content.vote_state_plural + '</span><br>');
    wrapper.appendChild(comment_info);
    $(wrapper).append(content.comment_text);

    comment_actions.appendChild(vote_container);
    $(vote_container).attr('type', 'comment');
    vote_container.appendChild(upvote);
    $(upvote).attr({'token': 'UP', 'previous': content.previous});
    
    //comment_actions.appendChild(share);

    if (frenetic['user'].username === content.poster) {
        $(delete_comment).text('DELETE');
        $(delete_comment).click(function() {
            delete_content(content.comment_id, 'comment');
            $(this).parents('.comment_wrapper').remove();
            resizeCommentContainer();
        });
        comment_actions.appendChild(delete_comment);
    }

    comment_actions.appendChild(reply);
    $(reply).text('Reply');

    var textarea = document.createElement('textarea');
    $(textarea).attr('rows', '5');
    comment_reply.appendChild(textarea);
    comment_reply.appendChild(reply_action);
    reply_action.appendChild(post_reply);

    $(post_reply).text('Submit');

    //events
    reply.addEventListener('click', function() {
        if (frenetic['user'].login_status === 'not_logged_in') {
            frenetic.modal.login.open('reply to comments');
        } else {
            if ($(comment_reply).css('display') !== 'none') {
                $(comment_reply).css('display', 'none');
                $(this).text('Reply');
                $(this).css('background', '');
            } else {
                $(comment_reply).css('display', 'block');
                $(this).text('Cancel');
                $(this).css('background', 'lightgray');
                $(comment_reply).find('textarea').focus();
            }
            resizeCommentContainer();
        }
    });

    upvote.addEventListener('click', function() {
        if (frenetic['user'].login_status === 'not_logged_in') {
            frenetic.modal.login.open('vote on comments');
        } else {
            voteComment(content, $(this));
        }
    });

    post_reply.addEventListener('click', function() {
        resizeCommentContainer();
        //comment-specific info
        
        var wrapper = $(this).parents('.comment_wrapper');
        
        var new_comment = new Object();
        
        new_comment.pid = content.comment_id;
        new_comment.text = wrapper.find('textarea').val();
        new_comment.level = content.level;

        //content-wide info
        new_comment.content_id = content.content_id;
        new_comment.media_type = content.content_type;
        
        new_comment.tags = content.tags;

        new_comment.position = wrapper;

        if (new_comment.text !== "") {
            submit_comment(new_comment, 'reply');
            wrapper.find('textarea').val("");
        }

        $(this).parents('.comment_wrapper').find('.reply').trigger('click');
    });
    
    return comment_wrapper;

}