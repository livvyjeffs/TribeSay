var splode_status;
var stream_types;

function get_splode_status() {

    var splode = $('.stream.exploded:visible').attr('type'); // 'no' 'article' 'image' 'video' 'sound' 'mixed'

    if (splode === undefined) {
        return 'no';
    } else {
        return splode;
    }
}

function getAllUniques(media) {
    
//    if (media === 'mixed') {
        var type_uniques_array = "";
        var types = get_media_types();

        type_uniques_array += 'mixed_stream,';

        for (var i = 0; i < types.length; i++) {
            var uniques = $('#mixed_stream').find('.media_container[media="' + types[i] + '"]').not('.loadmore_image, .upload_more_image');
            type_uniques_array += types[i] + '||' + uniques.length + '||count,';
            uniques.each(function() {
                type_uniques_array += $(this).attr('uid') + ",";
            });

        }

        return type_uniques_array;

//    } else {
//
//        var type_uniques_array = "";
//        var streams = $('.stream[type="' + media + '"]');
//        var uniques = streams.find('.media_container').not('.loadmore_image, .upload_more_image');
//        type_uniques_array += streams.attr('id') + ',' + media + '||' + uniques.length + '||count,';
//
//        uniques.each(function() {
//            type_uniques_array += $(this).attr('uid') + ",";
//        });
//
//        return type_uniques_array;
//
//    }

}

function detect_loadmore() {
    
    if(frenetic.scope !== 'tribe' && $('#content .media_container').length === 0){
        return;
    }

    if ($('#content .stream_loader').length > 0) {
        
        if ($('#stream_container').height() - 0.5 * $(window).height() < $(window).scrollTop() + $(window).height()) {

            var load = new Object();
            load.type = 'scrolling';
            load.event_time = 'anytime';
            load.id = frenetic.gate_id;

            load_content(load);

        }
    }

    


}

function spinner_loading(target, color) {
    //$('.logo').addClass("rotate");
    var opts = {
        lines: 13, // The number of lines to draw
        length: 20, // The length of each line
        width: 10, // The line thickness
        radius: 30, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: color, // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent in px
        left: '50%' // Left position relative to parent in px
    };

    var spinner_fresh = new Spinner(opts).spin(target);

    return spinner_fresh;
}


function clean_for_repeats(media) {
    var seen = [];
    $('.media_container[media="' + media + '"]').not('.loadmore_image').each(function() {
        if ($.inArray($(this).attr('order'), seen) === -1) {
            seen.push($(this).attr('order'));
        } else {
            $(this).remove();
            if (get_splode_status() !== 'no') {
                masonry(get_columns(), 10, media);
            }
        }
    });
}

function format_check(media, columns) {
    var status = get_splode_status();
    //console.log('formatting');
    if (status === 'no') {
        masonry(1, 5, media);
        setTimeout(function() {
            if (status !== get_splode_status()) {
                     format_check(media, columns);
            } else {
                masonry(1, 5, media);
            }
        }, 250);
    } else {
        masonry(columns, 10, media);
        setTimeout(function() {
            if (status !== get_splode_status()) {
                     format_check(media, columns);
            } else {
                masonry(columns, 10, media);
            }
        }, 250);
    }
    setImageBlock();
}

var old_time;

function timer(label) {
    var new_time = new Date().getTime();
    //console.log(label + " : " + (new_time - old_time));
    old_time = new_time;
}

function set_timer(label) {
    //console.log("SET TIMER: " + label + " : " + (old_time));
    old_time = new Date().getTime();
}

var infinite = false;

function set_infinite(value) {
    if (value) {
        infinite = 'yes';
    } else {
        infinite = 'no';
    }
}

function close_the_gate() {

    var id = Math.round(Math.random() * 1000).toString();

    frenetic.gate_id = id;

    return id;
}


var ready = true;

function load_content(load) {
   
    $('#media_options').addClass('hidden');

    load.scope = frenetic.scope;
    load.media = frenetic.media;
    load.person = frenetic['page_owner'].username;

    var current_id_list;

    var ajax = ajaxObj("POST", frenetic.root + "/php_includes/stream_generator.php");

    ajax.onreadystatechange = function() {

        if (ajaxReturn(ajax) === true) {
            
            if (load.id !== frenetic.gate_id) {
                return;
            }


            var json = JSON.parse(ajax.responseText);
            
            //fade out loading gif
            if (current_id_list === 'non_purge') {
                $('#main #wait_background').remove();
                $('#content').empty();
                $('#stream_container').css('height','');
                $('#stream_container .spinner').animate({'opacity': 0}, 500, function() {
                    $(this).remove();
                });
                $('#content').loadingdots('stream_loader');

                //adding pinned profile picture to single view

                if (load.scope === 'single') {

                    $('#content')[0].insertBefore(profile_tile(), $('#content .stream_loader')[0]);
                    masonry(get_columns(), 10, frenetic.media);

                    // $('.profile_tile').css({'position': 'fixed', 'top': 45, 'left': -25, 'margin': 0});

                } else if (load.scope === 'friends') {

                    $('#content')[0].insertBefore(friends_tile(), $('#content .stream_loader')[0]);
                    masonry(get_columns(), 10, frenetic.media);

                }

            }

            //print new containers

            if ($('#content').length > 0) {

                for (var i = 0; i < json.length; i++) {
                    

                    $('#content')[0].insertBefore(media_container(json[i]), $('#content .stream_loader')[0]);

                    masonry(get_columns(), 10, frenetic.media);


                }
                
                if (load.scope === 'tribe') {
                    
                    if (json.length === 0) {

                        if (infinite !== 'yes') {

                            set_infinite(true);
                                                        
//                            var load_a = new Object();
//                            load_a.type = 'scrolling';
//                            load_a.id = frenetic.gate_id;
//                            load_a.event_time = 'anytime';
//
//                            load_content(load_a);

                        } else {

                            $('#content .stream_loader').remove();
                            if ($('#content .stream_loader').length === 0) {
                                //$('#content').loadmore();
                            }

                            set_infinite(false);
                        }

                    }
                } else {

                    if (json.length === 0) {
                        $('#content .stream_loader').remove();
                        if(current_id_list === 'non_purge'){
                             $('#content').loadmore();
                        }
                    }

                }

                ready = true;

                detect_loadmore();

                masonry(get_columns(), 10, frenetic.media);
            }

        }

    };

    

    if (frenetic.pagename === 'events') {
        load.media = 'event';
    }

    if (load.type !== 'scrolling') {

        ready = false;

        current_id_list = 'non_purge';
        reset_columntops('page');

        set_infinite(false);

        //add spinner
        if ($('#stream_container').length > 0) {
            $('#main').wait();

        }

        if (load.type === 'fresh_load') {

            ajax.send("scope=" + load.scope + "&current_id_list=" + current_id_list + "&page_owner=" + load.person + "&splode_status=" + load.media + "&stream_media_type=" + load.media + "&trigger=" + load.type + "&infinite=" + infinite + "&column_width=" + frenetic.column_width + "&event_filter=" + load.event_time);
            console.log("scope=" + load.scope + "&current_id_list=" + current_id_list + "&page_owner=" + load.person + "&splode_status=" + load.media + "&stream_media_type=" + load.media + "&trigger=" + load.type + "&infinite=" + infinite + "&column_width=" + frenetic.column_width + "&event_filter=" + load.event_time);

        } else {

            ajax.send("scope=" + load.scope + "&current_id_list=" + current_id_list + "&page_owner=" + load.person + "&splode_status=" + load.media + "&stream_media_type=" + load.media + "&trigger=" + load.type + "&infinite=" + infinite + "&column_width=" + frenetic.column_width + "&event_filter=" + load.event_time);
            console.log("scope=" + load.scope + "&current_id_list=" + current_id_list + "&page_owner=" + load.person + "&splode_status=" + load.media + "&stream_media_type=" + load.media + "&trigger=" + load.type + "&infinite=" + infinite + "&column_width=" + frenetic.column_width + "&event_filter=" + load.event_time);

        }

    } else if (ready === true) {

        ready = false;

        current_id_list = getAllUniques(load.media);

        ajax.send("scope=" + load.scope + "&current_id_list=" + current_id_list + "&page_owner=" + load.person + "&splode_status=" + load.media + "&stream_media_type=" + load.media + "&trigger=" + load.type + "&infinite=" + infinite + "&column_width=" + frenetic.column_width + "&event_filter=" + load.event_time);
        console.log("scope=" + load.scope + "&current_id_list=" + current_id_list + "&page_owner=" + load.person + "&splode_status=" + load.media + "&stream_media_type=" + load.media + "&trigger=" + load.type + "&infinite=" + infinite + "&column_width=" + frenetic.column_width + "&event_filter=" + load.event_time);

    }

}

