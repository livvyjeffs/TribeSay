var current_sound;
var current_elem = "begin";
//
//
//var playing_track_url;
//var playing_song;
//var playing_button;
//var playing_order;
//
//var all_songs = [];
//
////function play_song(element) {
//
//    //var play_buttons = $("." + element.attr("class"));
//
//    var link = element.attr("sclink");
//    var status = element.attr('status');
//    var order = parseInt(element.attr('order'), 10);
//
//    pausePlayingSong();
//
//    switch (status) {
//        case 'playing':
//            //simple pause
//
//            break;
//        case 'not_playing':
//            //pause then play
//
//            playing_order = order;
//            load_sound(link);
//
//            playing_button = element;
//            playing_button.attr("status", "playing");
//
//
//            break;
//        case 'paused':
//            //pause then play a paused
//
//            if (link === playing_track_url) {
//                playing_song.resume();
//
//                playing_button = element;
//                playing_button.attr("status", "playing");
//
//
//            } else {
//                playing_song = all_songs[order];
//                playing_track_url = link;
//
//                all_songs[order].resume();
//                playing_button = element;
//                playing_button.attr("status", "playing");
//            }
//
//            break;
//    }
//}
//
//function pausePlayingSong() {
//    if (playing_song !== undefined) {
//        playing_song.pause();
//        playing_button.attr("status", "paused");
//    }
//}

function play_all(element) {

    if (element.attr('status') === 'not_playing') {
         $('.play_song[order="1"]').trigger("click");
        element.attr('status', 'playing');
    } else if (element.attr('status') === 'playing') {
        element.attr('status', 'paused');
        //element.text('Play');

        current_sound.pause();
        current_elem.setAttribute("status", "not_playing");
    } else {
        element.attr('status', 'playing');
        
        current_sound.resume();
        current_elem.setAttribute("status", "playing");
    }
}

//function load_sound(track_url) {
//    SC.initialize({client_id: 'ca8c1802896517bc68c9c149f3e9f805'});
//    SC.get('/resolve', {url: track_url}, function(track) {
//        SC.stream("/tracks/" + track.id, function(sound) {
//            playing_track_url = track_url;
//            playing_song = sound;
//            all_songs[playing_order] = sound;
//
//            sound.play({
//                whileplaying: function() {
//                    var sofar = sound.position / 60000;
//                    var total = sound.duration / 60000;
//                }
//            });
//
//        });
//    });
//}
//
function load_sound(track_url, elem) {
    elem.setAttribute("status", "loading");
    var opts = {
        lines: 9, // The number of lines to draw
        length: 7, // The length of each line
        width: 13, // The line thickness
        radius: 25, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#ffffff', // #rgb or #rrggbb or array of colors
        speed: 0.5, // Rounds per second
        trail: 95, // Afterglow percentage
        shadow: true, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent in px
        left: '50%' // Left position relative to parent in px
    };

    var target = elem;
    //var target = document.getElementsByClassName('play_song')[0];
    var spinner_fresh = new Spinner(opts).spin(target);


    SC.initialize({client_id: 'ca8c1802896517bc68c9c149f3e9f805'});
    SC.get('/resolve', {url: track_url}, function(track) {
        SC.stream("/tracks/" + track.id, function(sound) {
            var is_playing = true;
            $('.play_song[status="playing"]').trigger("click");
            elem.setAttribute("status", "playing");        
            var play_all = $('#play_all');
            spinner_fresh.stop();
            play_all.attr('status', 'playing');
            //play_all.text('Pause');    
            //update toggle button up top
            current_sound = sound;
            current_elem = elem;
            sound.play(                    
                    {onfinish: function() {
                            var order = parseInt($(elem).attr("order"), 10) + 1;
                            $('.play_song[order=' + order + ']').trigger("click");
                        }
                    });
            elem.onclick = function() {
                if (is_playing === false) {
                    $('.play_song[status="playing"]').trigger("click"); //triggers playing song to pause
                    elem.setAttribute("status", "playing");
                    //pause other tracks by triggering those that are playing status
                    sound.resume();
                    current_sound = sound;
                    current_elem = elem;
                    is_playing = true;
                    var play_all = $('#play_all');
                    play_all.attr('status', 'playing');
                    //play_all.text('Pause');
                } else if (is_playing === true) {
                    sound.pause();
                    elem.setAttribute("status", "not_playing");
                    is_playing = false;
                    var play_all = $('#play_all');
                    play_all.attr('status', 'paused');
                    //play_all.text('Play');
                }               
            };
        });
    });
}

