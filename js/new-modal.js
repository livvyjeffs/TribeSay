function initialize(lat, long, html, title) {

    var a = $(html);
    var zoom = 8;

    if (a.hasClass('street-address')) {
        zoom = 17;
    }

    var myLatlng = new google.maps.LatLng(lat, long);

    var mapOptions = {
        zoom: zoom,
        center: myLatlng
    };

    var map = new google.maps.Map($('#modal_viewer .event-map div')[0],
            mapOptions);
            
    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: title
    });
}

/////////////////////GOOGLE MAPS API ABOVE/////////////////

function modal(type) {

    this.type = type;

    this.element = document.getElementById('modal_' + this.type);
    this.background = $(this.element).parents('.modalBackground')[0];
    this.center_canvas = $(this.element).parents('.modal_center_canvas')[0];

}
;

modal.prototype = {
    
    open: function(content) {

        $('#media_options').addClass('hidden');

        if (this.type === 'upload') {

            if (frenetic['user'].login_status === 'not_logged_in') {
                frenetic.modal.login.open('post content');
                return;
            }

        }

        $(this.background).add($(this.center_canvas)).attr('type', this.type);

        $(this.background).add($(this.element)).removeClass('closed invisible').addClass('open');

        if (this.type === 'viewer') {
            if (frenetic.content === undefined) {
                frenetic.content = new Object();
            }

            frenetic.content.uid = content.uid;
            frenetic.content.media = content.media;
            frenetic.content.order = content.order;

            this.content = content;

            $(this.background).attr({'media': this.content.media, 'uid': this.content.uid});
            
            populate_modal(content);            

            //is frenetic.content used?


            if (content.media === 'event') {
                $('.comment_description_container.container').removeClass('open').addClass('closed');
                initialize(content.lat, content.long, content.location_html, content.title);
                $('.event_container .event-map div').css({'height': $('.event_container').height() - $('.event_container .event-map div').position().top});
            }

            updateURL('content');

            //ANALYTICS
            if (frenetic['link'].uid === content.uid && frenetic['link'].load_status === 'yes') {
                record_content('consumption', 'open_content', 'from_link', {'media': content.media, 'uid': content.uid, 'tag': content.tags, 'poster': content.poster});
            } else {
                record_content('consumption', 'open_content', 'internal', {'media': content.media, 'uid': content.uid, 'tag': content.tags, 'poster': content.poster});
            }

        } else if (this.type === 'signup') {

            //ANALYTICS
            record_content('conversion', 'view_signup', null, {});

            if (window.innerWidth > 500) {
                $('#username').focus();
            }

        } else if (this.type === 'login') {

            if (content !== undefined) {
                $('.login_action').text('Login to ' + content + '.').css('display', 'block');
                //$('#modal_login').css('top', '30%');
                //ANALYTICS
                record_content('conversion', 'view_login', content, {});
            } else {
                //ANALYTICS
                record_content('conversion', 'view_login', 'direct', {});
            }

            if (window.innerWidth > 500) {
                $('#username').focus();
            }

        } else if (this.type === 'change_pw') {
            if (window.innerWidth > 500) {
                $('#change_password_form input').first().focus();
            }
        } else if (this.type === 'upload') {
            if (window.innerWidth > 500) {
                $('#link_input').focus();
            }
        } else if (this.type === 'event_posting') {
            frenetic.event = new Object();
            frenetic.event.paymentplan = $('input[name="payment-plan"][checked]').val();
        }

    },
    close: function(no_prompt) {

        if (this.type === 'signup' || this.type === 'upload') {

            if (no_prompt !== 'no_prompt') {
                if (!confirm("Are you sure you want to close this?")) {
                    return;
                }
            }

        } else if (this.type === 'viewer') {
            updateURL();
        } else if (this.type === 'onboarding') {
            $('#modal_onboarding [slide]').removeClass('open').addClass('closed');
            $('#modal_onboarding [slide="1"]').addClass('open');
            $(this.background).add($('#scope_navigation')).removeAttr('style');
        }

        this.clear();

        $(this.background).removeAttr('media type uid');
        $(this.background).add($(this.element)).removeClass('open expanded').addClass('closed');

    }, clear: function() {

        switch (this.type) {
            case 'upload':
                $('#post_editor .empty').find('*').not('.no_empty').remove();

                //prevents posting old content
                $('#post_to_stream_btn').unbind();

                $('#modal_upload .open').removeClass('open');

                $('#modal_upload').removeClass('open');
                break;
            case 'login':
                $(this.element).removeClass('open').addClass('closed');
                break;
            case 'signup':
                $(this.element).removeClass('open').addClass('closed');
                break;
            case 'event_posting':
                delete frenetic.event;
                break;

        }
        $(this.background).find('[style]').not('#comment_container, #login_btn, #signup_btn, .content_holder.container, .event-description').removeAttr('style');
        $(this.background).find('.empty').find('*').not('.no_empty').remove();
        $(this.background).find('input, textarea').not('.submit, [type="submit"]').val('');

        if ($('#description_input .starter_comment').attr('status') === 'closed') {
            $('#description_input .cancel_comment').trigger('click');
        }



    },
    next: function() {

        this.clear();

        if (this.content.order > $('#content .media_container').length - 3) {
            var load = new Object();
            load.type = 'scrolling';
            load.id = $('.content').attr('gate_id');
            load_content(load);
        }

        if ($('#content [order="' + (this.content.order + 1) + '"] .modal_trigger').first().length === 0) {
            this.close();
            return;
        } else {
            $('#content [order="' + (this.content.order + 1) + '"] .modal_trigger').first().trigger('click');
        }



        //ANALYTICS
        record_content('consumption', 'next_button', null, {'media': this.content.media, 'uid': this.content.uid, 'tag': this.content.tags});
    },
    previous: function() {

        this.clear();

        if (this.content.order === 1) {
            this.close();
            return;
        }

        $('#content [order="' + (this.content.order - 1) + '"] .modal_trigger').first().trigger('click');

        //ANALYTICS
        record_content('consumption', 'previous_button', null, {'media': this.content.media, 'uid': this.content.uid, 'tag': this.content.tags});

    }, minimize: function() {

    }, expand: function() {

    }
};

//instantiating modal types

function create_modals() {

    var modal_list = ['viewer', 'upload', 'debug', 'search', 'signup', 'login', 'onboarding', 'change_pw', 'change_profile', 'event_posting'];

    frenetic.modal = new Object();
    for (var i = 0; i < modal_list.length; i++) {
        frenetic['modal'][modal_list[i]] = new modal(modal_list[i]);
    }

    $('.modal_center_canvas').click(function(e) {
        if ($(e.target).attr('class') === $(this).attr('class')) {
            frenetic.modal[$(this).attr('type')].close();
        }
    });

    $('.modalBackground .close').click(function() {
        frenetic.modal[$(this).parents('.modalBackground').attr('type')].close('no_prompt');
    });

}

function populate_modal(content) {

    share_module(content);

    //next buttons
    navigation_buttons_module(content);

    //action values
    $('.modal_container.viewer').parents('.modalBackground').attr('media', content.media);

    for (var i = 0; i < eval(content.tags).length; i++) {
        $('#content_tags')[0].appendChild(tag_module(content.tags[i], 'modal'));
    }

    $('.modal_container .vote_container .upvote').attr('previous', content.vote).click(function() {
        postVote(content, 'UP');
        //record_content('modal_content_vote', content);
    });

    $('.modal_container .vote_container .downvote').attr('previous', content.vote).click(function() {
        postVote(content, 'DOWN');
        //record_content('modal_content_vote', content);
    });

    $('.modalBackground .vote_tally').text(content.vote_state);
    $('.vote_container[type="content"] .upvote').attr('previous', content.previous_vote);


    $('.submit_starter').click(function() {

        if (frenetic['user'].login_status === 'not_logged_in') {
            frenetic.modal.login.open();

        } else {

            var new_comment = new Object();
            //comment-specific info
            new_comment.level = 0;
            new_comment.text = $(this).parents('#description_input').find('.starter_comment').val();
            new_comment.content_id = content.uid;

            //content-wide info
            new_comment.media_type = content.media;
            new_comment.tags = content.tags;
            new_comment.pid = content.uid;

            $(this).siblings('.cancel_comment').trigger('click');

            if (new_comment.text !== "") {
                submit_comment(new_comment, 'starter');
                $(this).parents('#description_input').find('.starter_comment').val("");
            }

            //record_content('modal_starter_comment', content);

        }
    });

    //end action values
    writeMedia(content);
    retrieve_comments(content, 'initial');

    if (content.media !== 'event') {
        ad_module(content);
    }

}


function navigation_buttons_module(content) {

    if (content.order === 1) {
        $('#previous_btn').css('visibility', 'hidden');
        $('#next_btn').css('visibility', 'visible');
    } else if (content.order === $('.media_container[media]').length) {
        $('#next_btn').css('visibility', 'hidden');
    } else if (content.order > 0) {
        $('#previous_btn').css('visibility', 'visible');
        $('#next_btn').css('visibility', 'visible');
    } else if (content.order === undefined) {
        $('#previous_btn').css('visibility', 'hidden');
        $('#next_btn').css('visibility', 'hidden');
    }

}

function openModal(modalview, content) {


    if (modalview === 'viewer') {



    } else if (modalview === 'upload') {

        $('#link_input').removeAttr('disabled');

        if (get_splode_status() !== 'no') {
            $('#modal_upload .header_icon.button[type=' + get_splode_status() + ']').trigger('click');
            ga('send', 'event', 'post_button', 'exploded', get_splode_status());
        }

    } else if (modalview === 'login' || modalview === 'signup') {
        if (modalview === 'signup') {
            $('.modal_container.signup').parents('.modalBackground').addClass('close_prompt');


        } else {


        }

    }

    $('.modal_center_canvas.' + modalview).parents('.modalBackground').addClass('open');
    $('.modal_center_canvas.' + modalview).addClass('open');



}

function writeMedia(content) {

    var opts = {
        lines: 13, // The number of lines to draw
        length: 20, // The length of each line
        width: 10, // The line thickness
        radius: 30, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#eaeaea', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent in px
        left: '50%' // Left position relative to parent in px
    };

    var target = document.getElementById('content_holder');
    var spinner_article_content = new Spinner(opts).spin(target);

    switch (content.media) {
        case 'image':

            var link = document.createElement('a');
            link.href = content.original_link;
            link.target = '_blank';

            var image_content = document.createElement('img');
            image_content.src = content.image_large;
            image_content.alt = '';

            image_content.onload = function() {
                $('#content_holder .spinner').remove();
                $(this).css('display', 'inline-block');
            };

            link.appendChild(image_content);

            $('#content_holder')[0].appendChild(link);

            break;
        case 'article':

            //$('#content_holder').append('<div class="loading_container"><img class="loading_gif" src="sourceImagery/round_loading_gif_large.gif"></div>');

            var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_article_content.php");
            ajax.onreadystatechange = function() {
                if (ajaxReturn(ajax) === true) {

                    //console.log("///////////////////" + JSON.parse(ajax.responseText));

                    var article_content = JSON.parse(ajax.responseText);
                    format_article(article_content, content);
                    spinner_article_content.stop();
                }
            };

            ajax.send("article_id=" + content.uid);
            //console.log("article_id=" + content.uid)

            break;
        case 'video':

            var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_video_html.php");

            ajax.onreadystatechange = function() {
                if (ajaxReturn(ajax) === true) {
                    //console.log("///////////////////" + JSON.parse(ajax.responseText));
                    content = JSON.parse(ajax.responseText);
                    content.html = unescape(content.html);

                    $('#content_holder').append(content.html);
                    //$('#content_holder').append("<img class='loading_gif' src='" + frenetic.root + "/sourceImagery/dna_loading.gif'>" + content.html);
                    $('#content_holder .loading_gif').animate({'opacity': '1'}, 0);
                    $('#content_holder iframe').load(function() {
                        $(this).animate({'opacity': '1'}, 200);
                        spinner_article_content.stop();
                    });

                }
            };

            ajax.send("video_id=" + content.uid);
            //console.log("video_id=" + content.uid)

            break;
        case 'sound':
            $('#content_holder').append("<iframe class='bt' width='100%' height='450' scrolling='no' frameborder='no' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/" + content.soundcloud_track + "&amp;auto_play=false&amp;hide_related=false&amp;visual=true'></iframe>");
            //$('#content_holder .loading_gif').animate({'opacity': '1'}, 1000);
            $('#content_holder iframe').load(function() {
                $(this).animate({'opacity': '1'}, 1000);
                spinner_article_content.stop();

            });
            break;
        case 'event':
            
            var img = $(document.createElement('img')).attr('src', content.image_large);
            
            $('#modal_viewer .event-image').append(img);
            
            img.load(function() {
                $('#modal_viewer .event-description').css({'top': img.height() + 13, 'height': $('#modal_viewer .event_container').height() - img.height() - 13});
            }); 
          
            $('.event_container .event-go-to-tickets').append($('<a href="' + content.payment_link + '" target="_blank"><div class="no_empty pseudo_before vmiddle"></div>'+content.ticket_text+'</a>'));
            
            $('.event_container .event-title').text(content.title);
            
            $('.event_container .event-image-title-holder').prepend(img.clone());

            $('.event_container .event-map').append($('<div class="map_holder"></div>'));

            $('.event_container .event-location .details').text(content.location_formatted);
            
            $('.event_container .event-date .details').text(content.event_time_text);

            $('.event_container .event-add-to-calendar').append('<a href="' + content.original_link + '" title="Add to Calendar" class="addthisevent">\n\
                                            <div class="no_empty pseudo_before vmiddle"></div>Add to Calendar\n\
                                            <span class="_start">' + content.event_unf_begin + '</span>\n\
                                            <span class="_end">' + content.event_unf_end + '</span>\n\
                                            <span class="_zonecode">35</span>\n\
                                            <span class="_summary">' + content.title + '</span>\n\
                                            <span class="_description">' + content.description + '</span>\n\
                                            <span class="_location">' + content.location_formatted + '</span>\n\
                                            <span class="_organizer">' + content.poster + '</span>\n\
                                            <!--<span class="_organizer_email">Organizer e-mail</span>\n\
                                            <span class="_facebook_event">http://www.facebook.com/events/160427380695693</span>-->\n\
                                            <span class="_all_day_event">false</span>\n\
                                            <span class="_date_format">DD/MM/YYYY</span>\n\
                                        </a>\n\
                                        <script type="text/javascript" src="http://js.addthisevent.com/atemay.js"></script>');

            $('.event_container .event-description').append($('<div>' + content.description + '</div>'));

            break;
    }

}