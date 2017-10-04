
$(document).ready(function() {
   
    //instantiates mobile searchbar
    var mobile_searchbar = new searchbar($('#searchBox')[0], $('#searchResults')[0], 'mobile_search');
    
    //scroll reduce header
    $('#stream').scroll(function(){
        if(this.scrollTop > 40){
            $('#header').stop().animate({'top': '-35'}, 200, function() {
                $(this).addClass('pulled_up');
            });
            $('#main').stop().animate({'top': '15'}, 200);
        } else {
           $('#header').stop().animate({'top':'0'}, 200, function(){
                $(this).removeClass('pulled_up');
           });
           $('#main').stop().animate({'top':'50'},200);
        }
    });
    
    $('#pull_down').click(function() {
        $('#header').stop().animate({'top': '0'}, 200, function() {
            $(this).removeClass('pulled_up');
        });
        $('#main').stop().animate({'top': '50'}, 200);
    });

    $('.login_message img').click(function() {
        //ANALYTICS
        
        record_content('conversion', 'facebook_login', 'mobile', {});
    });

    $('#logo_container').click(function() {
        $('#modalBackground').addClass('open');
        $(this).removeClass('active');
        //ANALYTICS
        record_content('conversion', 'view_login', 'mobile', {});
    }).on('vmousedown',function(){
         $(this).addClass('active');
    });

    $('.logout_message').click(function() {
        window.location = frenetic.root + '/logout.php';
    });

    $('#modalBackground').click(function(e) {
        if (e.target.id === 'modalBackground') {
            $('#modalBackground').removeClass('open');
        }
    });

    $('.close').click(function() {
        ga('send', 'event', 'mobile', 'close_content');
        close_content();
    }).on('vmousedown',function(){
         $(this).addClass('active');
    });
    
    $('#search_icon').click(function(e) {
        
        if(!e.isTrigger){
            record_content('consumption', 'search_icon', 'mobile', {});
        }
        
        $('#search_icon_container').removeClass('active'); 
        
        toggle_search();
        
    });
    
     $('#search_icon').on('vmousedown',function(){
         $('#search_icon_container').addClass('active'); 
    });

    $('#logo .icon').click(function() {
        clear_filter();
    });

    $('#menu_icon').click(function() {
        if ($('#media_menu').attr('status') === 'opened') {
            ga('send', 'event', 'mobile', 'menu', 'close_menu');
            $('#media_menu').css('display', 'none').attr('status', 'closed');
        } else {
            ga('send', 'event', 'mobile', 'menu', 'open_menu');
            $('#media_menu').css('display', 'block').attr('status', 'opened');
        }
    });

    $('#media_menu div').click(function() {
        $('#stream_holder').empty();
        $('#media_menu').css('display', 'none').attr('status', 'closed');
        $('#media_menu div.selected').removeClass('selected');
        $(this).addClass('selected');
        set_content_media($(this).attr('media'));
        ga('send', 'event', 'mobile', 'menu', 'media_' + $(this).attr('media'));
        load_content();
    });

    if ($('.article_content').length > 0) {
        format_article();
    }

    $("#content_holder").touchwipe({
        wipeLeft: function() {
            var elem = $('.content_container');
            go(elem.attr('media'), (parseInt(elem.attr('order'), 10) + 1));
        },
        wipeRight: function() {
            var elem = $('.content_container');
            go(elem.attr('media'), (parseInt(elem.attr('order'), 10) - 1));
        },
        wipeUp: function() {
        },
        wipeDown: function() {
        },
        min_move_x: 40,
        min_move_y: 40,
        preventDefaultEvents: false
    });

    $('#action_bar .next').click(function() {

        var elem = $('.content_container');

        //ANALYTICS
        record_content('consumption', 'next_button', 'mobile', {'order': (parseInt(elem.attr('order'), 10) + 1), 'media': elem.attr('media')});

        go(elem.attr('media'), (parseInt(elem.attr('order'), 10) + 1));
        
      
        
    }).on('vmousedown',function(){
         $(this).addClass('active');
    });

    $('#action_bar .previous').click(function() {
        var elem = $('.content_container');

        //ANALYTICS
        record_content('consumption', 'previous_button', 'mobile', {'order': (parseInt(elem.attr('order'), 10) - 1), 'media': elem.attr('media')});

        go(elem.attr('media'), (parseInt(elem.attr('order'), 10) - 1));
    
        
    }).on('vmousedown',function(){
         $(this).addClass('active');
    });

    $('.delete_tag').click(function() {
        clear_filter();
    });

});