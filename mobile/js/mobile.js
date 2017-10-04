//using same loadmore as desktop, changes behavior accordingly
set_loadmore_type('mobile');

var infinite = 'no';

function getAllUniques() {

    var type_uniques_array = "";
    var types = get_media_types();

    type_uniques_array += 'mixed_stream,';

    for (var i = 0; i < types.length; i++) {
        var uniques = $('.media_container[media="' + types[i] + '"]').not('.loadmore_image, .upload_more_image');
        type_uniques_array += types[i] + '||' + uniques.length + '||count,';
        uniques.each(function() {
            type_uniques_array += $(this).attr('uid') + ",";
        });

    }



    return type_uniques_array;
    /*
     
     
     var type_uniques_array = "";
     var uniques = $('.media_container[media="' + media + '"]').not('.loadmore_image, .upload_more_image');
     type_uniques_array += media + 'Stream,' + media + '||' + uniques.length + '||count,';
     
     uniques.each(function() {
     type_uniques_array += $(this).attr('uid') + ",";
     });
     
     //return type_uniques_array;*/


}

function clear_filter() {
    ga('send', 'event', 'mobile', 'single_click', 'filter_clear');
    ga('send', 'event', 'mobile', 'filter_clear', 'tag_' + $('#header .tag_module').attr('title'));
    ga('send', 'event', 'mobile', 'filter_clear', 'media_' + get_content_media());

    $('#stream_holder').empty();
    $('#tribe_bar').empty();
    $('#logo_words').removeAttr('style');
    if (frenetic['user'].login_status === 'not_logged_in') {
        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/set_filter_session.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                load_content();
            }
        };
        ajax.send("clear_all=yes");
    } else {
        var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {             
                load_content();
            }
        };
        ajax.send("clear_all=" + "true");
    }

}

function toggle_search() {

    $('#search_container, #stream').stop();

    if ($('#search_icon').attr('status') === 'open') {
        $('#search_icon').removeAttr('status');
        $('#search_container').animate({'bottom': '100%'}, 1000, function() {
            $('#search_container,#search_icon_container').removeClass('open');

        });
        $('#stream').animate({'top': '0'}, 1000);
    } else {
        $('#search_icon').attr('status', 'open');
        $('#search_icon_container').addClass('open');
        $('#search_container').addClass('open').animate({'bottom': '0.5rem'}, 1000);
        $('#stream').animate({'top': '100%'}, 1000);
    }

    if ($('#searchResults').overflow()) {
        $('#searchResults').css('overflow-y', 'scroll');
    } else {
        $('#searchResults').removeAttr('style');
    }

}

function get_filter_tags() {
//same as in standardhead.php
    var array = [];

    $('#tribe_bar .tag_text').each(function() {
        array.push($(this).attr('tag'));
    });

    return array;
}
;

function add_to_filter(tag) {

    $('#logo_words').css('display', 'none');
    $('#tribe_bar').empty();
    $('#tribe_bar')[0].appendChild(tag_module(tag.attr('tag'), 'mobile_filter'));
    //$('#header').append('<div class="tag_module" title="' + tag.attr('title') + '" type="tag">' + tag.attr('title') + '</div><div class="delete_tag" title="' + tag.attr('title') + '"><span>x</span></div>');
    $('#stream_holder').empty();

    if (frenetic['user'].login_status === 'not_logged_in') {
        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/set_filter_session.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                infinite = 'no';
                load_content();
            }
        };
        ajax.send("mobile_filter=" + tag.attr('tag'));

    } else {
        
        var ajax = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {
                
                infinite = 'no';
                load_content();
            }
        };
        ajax.send("add_remove=" +  tag.attr('tag'));
        
    }


}


function mobile_media_container(data) {

    var content = new datawrapper_media_container(data);

    var container, header, image, glass, votes, upvote, share, info, tag_block, poster;

    container = document.createElement('div');
    $(container).addClass('media_container').attr({
        'order': content.order,
        'uid': content.uid,
        'media': content.media,
        'score': content.score
    });

    header = document.createElement('h1');
    $(header).addClass('button').text(content.title);
    header.addEventListener('click', function() {
        open_content(content);
        //openModal('viewer', content);
    });

    image = document.createElement('img');
    image.src = content.image_thumbnail;

    $(image).addClass('modal_trigger').css({'background-color': 'rgb(' + content.rgb_r + ',' + content.rgb_g + ',' + content.rgb_b + ')'});

    $(image).click(function() {
        open_content(content);
        //openModal('viewer', content);
    }).on('vmousedown',function(){
         $(this).addClass('active');
    });

    glass = document.createElement('div');

    $(glass).addClass('glass');


    votes = document.createElement('div');
    $(votes).addClass('vote_container').attr('type', 'content');

    upvote = document.createElement('div');
    
    if(content.previous_vote === 'DOWN'){
        content.previous_vote = 'no_vote';
    }
    
    //upvote.src = frenetic.root + '/sourceImagery/hearted.png';
    $(upvote).addClass('upvote').attr({
        'token': 'UP',
        'previous': content.previous_vote
    });

    upvote.addEventListener('click', function() {
        postVote(content, 'UP');
        $(this).removeClass('active');
    });
   
    
    $(upvote).on('vmousedown',function(){
         $(this).addClass('active');
    });

    upvote.addEventListener('mouseout', function() {
        $(this).removeClass('active');
    });

    votes.appendChild(upvote);

    share = document.createElement('div');
    $(share).addClass('share');

    glass.appendChild(image);
    glass.appendChild(votes);
    glass.appendChild(share);

    var vote_plural;
    if (content.vote_state === 1) {
        vote_plural = '';
    } else {
        vote_plural = 's';
    }

    poster = document.createElement('span');
    $(poster).addClass('button').text(content.poster);
    poster.addEventListener('click', function() {
        if (frenetic['user'].login_status === 'not_logged_in') {
            content.label = 'follow ' + content.poster + ' on TribeSay';
            openModal('login', content);
            return;
        }
        go_to_person(content.poster);
    });

    info = document.createElement('div');
    $(info).addClass('info_nugget');
    $(info).append('<span>posted by ' + content.poster + ', <span class="vote_tally">' + content.vote_state + '</span> vote' + vote_plural + '</span>');

    glass.appendChild(info);
    //$(info).append(content.description);

    tag_block = document.createElement('div');

    for (var i = 0; i < eval(content.tags).length; i++) {
        tag_block.appendChild(tag_module(content.tags[i], 'mobile'));
    }

    $(tag_block).addClass('tag_container');

    container.appendChild(glass);
    container.appendChild(header);

    container.appendChild(info);
    container.appendChild(tag_block);

    return container;

}

var load_ready = true;

function get_load_ready_status() {
    return load_ready;
}

function detect_loadmore() {

    var bottom = $('#stream').outerHeight(true) + $('#stream').scrollTop();

    //alert('detecing')
    //alert($('.loadmore').length)
    //alert(( $('.loadmore').position().top +  $('.loadmore').outerHeight(true))+ "vs" +(bottom + $(window).height() * 0.5))
    ////console.log(bottom + "=" + container.offsetHeight + " + " + container.scrollTop);
    $('.loadmore').each(function() {
        if (load_ready) {
            if ($(this).position().top + $(this).outerHeight(true) < (bottom + $(window).height() * 0.5)) {
                //alert('removein')
                $(this).remove();
                load_content();
            }
        }
    });
}

function mobile_go_to_desktop(content) {

    var media_container, information;

    var filter_tag = $('#header .tag_module').attr('title');
    var filter_media = $('#media_menu div.selected').attr('media');
    if ($('#header .tag_module').length <= 0) {
        varying_text = "some " + filter_media + "s";
    } else {
        varying_text = "about " + filter_tag;
    }

    about = ' about <strong style="font-size: inherit">' + filter_tag + '</strong>';
    media_container = document.createElement('div');
    $(media_container).addClass('media_container go_to_desktop');
    information = document.createElement('p');
    $(information).addClass('info_nugget').html('We\'re out of ' + filter_media + 's' + about + '!<br><br>Help build your tribe and go to our desktop version to start posting ' + varying_text + '. &#9786;');

    var email = document.createElement('div');
    var email_button = document.createElement('a');

    $(email).css('text-align', 'center');
    $(email_button).addClass('email_button').attr('href', 'mailto:olivia@tribesay.com?subject=Remind Me about TribeSay on Desktop').text('Want us to remind you by email?');
    email.appendChild(email_button);

    email_button.addEventListener('click', function() {

    });

    media_container.appendChild(information);
    media_container.appendChild(email);

    return media_container;
}

function load_content() {

    // alert('loading')

    //var media = get_content_media();
    var current_id_list = getAllUniques();

    var ajax = ajaxObj("POST", frenetic.root + "/php_includes/stream_generator.php");
    //var ajax = ajaxObj("POST", frenetic.root + "/mobile/m_parsers/m_stream_generator.php");

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {

            var json = JSON.parse(ajax.responseText);

            if (json.length === 0) {

                if (infinite === 'no') {
                    $('#stream .spinner').remove();
                    load_ready = true;
                    infinite = 'yes';
                    load_content();
                } else {
                    $('#stream .spinner').remove();
                    load_ready = true;
                    $('#stream_holder')[0].appendChild(mobile_go_to_desktop());
                }
                return;
            }

            for (var i = 0; i < json.length; i++) {
                $('#stream_holder')[0].appendChild(mobile_media_container(json[i]));
                //create_media_container(json[i]);
            }

            $('#stream .spinner').remove();
            $('#stream_holder').loadingdots('loadmore');
            load_ready = true;

        }
    };

    if (load_ready === true) {
        load_ready = false;
        //console.log("scope=tribe&current_id_list=" + current_id_list + "&page_owner=undefined&splode_status=no&stream_media_type=" + media + "&trigger=scrolling&column_width=undefined");
        ajax.send("scope=tribe&current_id_list=" + current_id_list + "&page_owner=undefined&splode_status=mixed&stream_media_type=mixed&trigger=scrolling&infinite=" + infinite + "&column_width=undefined");
    }
}

function close_content() {
    $('#stream').css('visibility', 'visible');
    $('#content_container').css('display', 'none');
    $('#search_icon, #search_icon_container').removeAttr('style');
    $('#content_holder').empty();
    $('#main').removeAttr('style');

    window.history.pushState('object or string', 'Title', frenetic.root);
    
    $('#action_bar .close').removeClass('active');
    
    //ANALYTICS
    record_content('consumption', 'close_content', 'mobile', {});
}

function go(media, order) {

    load_content();

    $('#main').removeAttr('style');
    $('#content_holder').empty();

    if ($('.media_container[order="' + order + '"]').length > 0) {
        $('.media_container[order="' + order + '"] img').trigger('click');
    } else {
        close_content();
    }

}

//function open_content(media, uid) {
function open_content(content) {

    $('.active').removeClass('active');

    $('#stream').css('visibility', 'hidden');
    $('#content_container').css('display', 'block');

    if ($('#search_icon').attr('status') === 'open') {
        $('#search_icon').trigger('click');
    }

    $('#search_icon').css('visibility', 'hidden').removeAttr('status');

    window.history.pushState('object or string', 'Title', frenetic.root + "/" + content.media + "/" + content.uid);

    //ANALYTICS
    record_content('consumption', 'open_content', 'from_stream_mobile', {'media': content.media, 'uid': content.uid, 'tag': content.tags});


    var opts = {
        lines: 13, // The number of lines to draw
        length: 20, // The length of each line
        width: 10, // The line thickness
        radius: 30, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#e9e9e9', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent in px
        left: '50%' // Left position relative to parent in px
    };

    var spinner = new Spinner(opts).spin($('#content_holder')[0]);

    ////////////////////////////////////OLD CODE//////////////////////////////////////

    var ajax = ajaxObj("POST", frenetic.root + "/mobile/m_includes/m_load_modal.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            var html = ajax.responseText;
            //console.log(html);


            $('#content_holder').append(html);

            //var order = parseInt($('.media_container[uid="' + content.uid + '"]').attr('order'), 10);

            $('.content_container').attr('order', content.order);

            if ($('.article_content').length > 0) {
                format_article();
                spinner.stop();
            }

            $('.video_content iframe').load(function() {
                spinner.stop();
            });

            $('.sound_content iframe').load(function() {
                spinner.stop();
            });

            $('.image_content img').load(function() {
                spinner.stop();
            });
        }
    };
    //console.log("load_modal=yes&m=" + media + "&u=" + uid);
    ajax.send("load_modal=yes&m=" + content.media + "&u=" + content.uid);

}

function openModal(viewtype, content) {
    if (viewtype === 'login') {
        $('#logo_container').trigger('click').removeClass('active');
    }
}

function closeModal(elem) {
    //SUPER HACK
    $('#modalBackground').removeClass('open');
}

load_content();

function isoverflowing(child, parent) {
    if (child.height() > parent.height()) {
        parent.css({"overflow-y": "scroll", "overflow-x": "hidden"});
        parent.children().css({'margin-right': '0.1rem'});
    } else {
        parent.css({"overflow-y": "hidden", "overflow-x": "hidden"});
        parent.children().css({'margin-right': ''});
    }

}

function format_article() {

    var text = $('.container').html();

    var patt_img = new RegExp("<img");
    var res_img = patt_img.test(text);
    var patt_iframe = new RegExp("<iframe");
    var res_iframe = patt_iframe.test(text);

    var dirtyarray = $('#content_holder img, #content_holder iframe').not('.loading_gif, .header_image');

    if (dirtyarray.length > 0) {
        $('.header_image').remove();
    }

    dirtyarray = $('#content_holder img, #content_holder iframe').not('.loading_gif, .header_image');

    var loaded = 0;

    dirtyarray.each(function() {

        if ($(this).is('iframe')) {

        } else {

            //console.log($(this).attr('src'));
            var screenImage = $(this);

            var theImage = new Image();
            var imageWidth;
            var imageHeight;
            theImage.src = screenImage.attr('src');

            theImage.onload = function() {
                //console.log('image loaded')
                loaded++;
                imageWidth = theImage.width;
                imageHeight = theImage.height;

                if (imageWidth < 150 || imageHeight < 50) {
                    screenImage.remove();
                    //console.log('REMOVED ' + imageWidth + 'x' + imageHeight)
                } else {
                    screenImage.attr({'natural-width': imageWidth, 'natural-height': imageHeight});
                }

                //console.log('loaded: ' + loaded + ' VS dirtyarray: ' + dirtyarray.length + '|| ' + imageWidth + 'x' + imageHeight);
                if (loaded === dirtyarray.length) {
                    if ($('#content_holder[media="article"] img').not('.loading_gif').length === 0) {

                        //$('#content_holder #article_header').after('<figure style="text-align: center"><img src="' + internal_link + '" class="header_image"></figure>');
                    }
                }

            };

            theImage.onerror = function() {
                loaded++;
                screenImage.remove();
                //console.log('error and loaded is: ' + loaded + 'VS dirtyarray: ' + dirtyarray.length);
                if (loaded === dirtyarray.length) {
                    if ($('#content_holder[media="article"] img').not('.loading_gif').length === 0) {
                        // $('#content_holder #article_header').after('<figure style="text-align: center"><img src="' + internal_link + '" class="header_image"></figure>');
                    }
                }
            };



        }
    });


    $('#content_holder a').attr('target', '_blank');

    if ($('.header_image').height() > $('#content_holder').height() * 0.5) {
        $('.header_image').css({'height': $('#content_holder').height() * 0.5, 'width': 'auto'});
    }

    $('#content_holder img, #content_holder iframe').not('.header_image').wrap('<div style="text-align: center"></div>');

    $('#content_holder figcaption').each(function() {
        var original_text = $(this).text();
        $(this).text("(" + original_text + ")");
    });

    $('#content_holder iframe').each(function() {
        var height = parseInt($(this).attr('height'), 10);
        var width = parseInt($(this).attr('width'), 10);
        $(this).css({'height': $(this).width() * height / width});
    });

    isoverflowing($('.container'), $('.content_container'));

    $('#content_holder .container img').each(function() {
        //console.log('2')
        $(this).load(function() {
            isoverflowing($('.container'), $('.content_container'));
        });

    });

    var last_scroll = 0;

    $('#content_holder .article_content').scroll(function() {
        $('#article_header_header a').click(function() {
            ga('send', 'event', 'mobile', 'to_original_link', 'from_article_header');
        });

        var tall = $(this).scrollTop();
        if (tall > last_scroll) {
            if (tall > 250 && parseInt($('#main').css('top'), 10) === 50) {
                //if (tall > 250 && $('#article_header_header').css('opacity') === '0') {
                //console.log('ENTERING');
                //$('#article_header_header').animate({'opacity': '1'}, 500);
                $('#main').animate({'top': 10}, 250);
            }
        }
        if (tall < last_scroll) {
            $('#main').stop();
            if (tall < 100) {
                //$('#article_header_header').css({'opacity': '0'});
                $('#main').animate({'top': 50}, 250);
            }
        }
        last_scroll = tall;
    });

    //console.log('3')

    isoverflowing($('.container'), $('.content_container'));



}