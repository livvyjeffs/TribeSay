//New Functions

/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 * 
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */

/*$("#imagegallery").touchwipe({
 wipeLeft: function() { alert("left"); },
 wipeRight: function() { alert("right"); },
 wipeUp: function() { alert("up"); },
 wipeDown: function() { alert("down"); },
 min_move_x: 20,
 min_move_y: 20,
 preventDefaultEvents: true
 });*/


function record_content(category, action, label, dimensions) {

    for (var key in dimensions) {

        var dim_index;
        switch (key) {
            case 'media':
                dim_index = 'dimension3';
                break;
            case 'uid':
                dim_index = 'dimension6';
                break;
            case 'tag':
                var tag_string = '';

                for (var tag in dimensions[key]) {
                    tag_string += dimensions[key][tag] + ',';
                }

                tag_string = tag_string.substring(0, tag_string.length - 1);

                dimensions[key] = tag_string;

                dim_index = 'dimension7';
                break;
            case 'poster':

                dim_index = 'dimension8';

                dimensions[key] = get_tribesayer(dimensions[key]);

                break;
            case 'order':
                dim_index = 'dimension9';
                break;
        }

        ga('set', dim_index, dimensions[key]);

    }

    if (label === null) {
        ga('send', 'event', category, action);
    } else {
        ga('send', 'event', category, action, label);
    }

}

function get_tribesayer(poster) {

    var tribesayer = {'olivia': ['healthnut0230', 'isaahjoe', 'vegan4life', 'forthekids', 'LivvyJeffs'],
        'greg': ['maximumgallop', 'freerunner', 'ripcity', 'audiolife', 'deparma', 'copter', 'equestrian', 'cmetsbeltran15', 'cmetsbeltran15'],
        'paulina': ['jules14', 'sunfun2121', 'redone55', 'KatB1992', 'yogalife02', 'healthyteen789', 'jlm0306', 'paleoprincess555', 'Mkp4566', 'GFgirlx34', 'GFgirlx34'],
        'jp': ['OPstrang3', 'goosht3', 'meenz', 'laxbb87', 'sangakz2', 'hashtag', 'geraldP', 'BROSEIDON', 'shots44', 'slangin8', 'glutenfreespirit', 'jamsheed', 'mawsheen', 'mustachio', 'chromelion', 'darkness', 'dunkening', 'Awesome', 'DonDraper', 'leethacks', 'Watermelon', 'funsocks', 'browneye', 'hihats', 'sheerkan', 'rinzler', 'eightyPRO', 'eightyPRO'],
        'luci': ['LazyDayz2', 'gnomey1', 'nomad13', 'mellowyellow', 'foodie45', 'dancingqueen17', 'savvyy99', 'surfboart', 'coffeecat', 'MLL93', 'MLL93'],
        'michelle': ['dyun225', 'mmont22', 'tshelley25', 'schwartz13', 'ambfox13', 'jayouu5', 'pchan23', 'hgvogs3', 'michellehe', 'michellehe']};

    for (var i in tribesayer) {
        for (var j = 0; j < tribesayer[i].length; j++) {
            if (poster === tribesayer[i][j]) {
                return i;
            }
        }
    }
    return poster;

}

(function($) {
    $.fn.touchwipe = function(settings) {
        var config = {min_move_x: 20, min_move_y: 20, wipeLeft: function() {
            }, wipeRight: function() {
            }, wipeUp: function() {
            }, wipeDown: function() {
            }, preventDefaultEvents: true};
        if (settings)
            $.extend(config, settings);
        this.each(function() {
            var startX;
            var startY;
            var isMoving = false;
            function cancelTouch() {
                this.removeEventListener('touchmove', onTouchMove);
                startX = null;
                isMoving = false
            }
            function onTouchMove(e) {
                if (config.preventDefaultEvents) {
                    e.preventDefault()
                }
                if (isMoving) {
                    var x = e.touches[0].pageX;
                    var y = e.touches[0].pageY;
                    var dx = startX - x;
                    var dy = startY - y;
                    if (Math.abs(dx) >= config.min_move_x) {
                        cancelTouch();
                        if (dx > 0) {
                            config.wipeLeft()
                        } else {
                            config.wipeRight()
                        }
                    } else if (Math.abs(dy) >= config.min_move_y) {
                        cancelTouch();
                        if (dy > 0) {
                            config.wipeDown()
                        } else {
                            config.wipeUp()
                        }
                    }
                }
            }
            function onTouchStart(e) {
                if (e.touches.length == 1) {
                    startX = e.touches[0].pageX;
                    startY = e.touches[0].pageY;
                    isMoving = true;
                    this.addEventListener('touchmove', onTouchMove, false)
                }
            }
            if ('ontouchstart'in document.documentElement) {
                this.addEventListener('touchstart', onTouchStart, false)
            }
        });
        return this
    }
})(jQuery);

jQuery.fn.outerHTML = function() {
    return jQuery('<div />').append(this.eq(0).clone()).html();
};

jQuery.fn.overflow = function() {
    //console.log(this.outerHeight(true) + " <==> " + this.prop('scrollHeight'))
    //alert(this.outerHeight(true) + " <==> " + this.prop('scrollHeight'))
    if (this.outerHeight(true) < this.prop('scrollHeight')) {

        //||this.outerWidth(true) < this.prop('scrollWidth')
        return true;
    }
    else {
        return false;
    }
};

function get_media_types() {
    var types = ['article', 'image', 'sound', 'video', 'event'];
    return types;
}

function get_stream_types() {
    var types = ['article', 'image', 'sound', 'video', 'comment'];
    return types;
}

function getHREF() {
    //return 'http://localhost/myStream'; 
    //return 'http://tribesay.com';
    var r = window.location.href.split('/index.php')[0];
    return r;
}


function delete_content(UID, content_type) {

    var conf = confirm("Are you sure you want to delete this content?");
    if (conf !== true) {
        return false;
    }
    var ajax = new ajaxObj("POST", frenetic.root + "/php_parsers/delete_content.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "delete_successful") {
                $('.media_container[uid="' + UID + '"], .comment_wrapper[comment_id="' + UID + '"]').remove();
                reset_columntops('page');
                if (get_splode_status() !== 'no') {
                    masonry(get_columns(), 10, get_splode_status());
                }
            }
            else {
                alert(ajax.responseText);
            }
        }
    };
    ajax.send("UID=" + UID + "&content_type=" + content_type);
}

var BrowserDetect = {
    /*http://www.quirksmode.org/js/detect.html*/

    init: function() {
        this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
        this.version = this.searchVersion(navigator.userAgent)
                || this.searchVersion(navigator.appVersion)
                || "an unknown version";
        this.OS = this.searchString(this.dataOS) || "an unknown OS";
    },
    searchString: function(data) {
        for (var i = 0; i < data.length; i++) {
            var dataString = data[i].string;
            var dataProp = data[i].prop;
            this.versionSearchString = data[i].versionSearch || data[i].identity;
            if (dataString) {
                if (dataString.indexOf(data[i].subString) != -1)
                    return data[i].identity;
            }
            else if (dataProp)
                return data[i].identity;
        }
    },
    searchVersion: function(dataString) {
        var index = dataString.indexOf(this.versionSearchString);
        if (index == -1)
            return;
        return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
    },
    dataBrowser: [
        {
            string: navigator.userAgent,
            subString: "Chrome",
            identity: "Chrome"
        },
        {string: navigator.userAgent,
            subString: "OmniWeb",
            versionSearch: "OmniWeb/",
            identity: "OmniWeb"
        },
        {
            string: navigator.vendor,
            subString: "Apple",
            identity: "Safari",
            versionSearch: "Version"
        },
        {
            prop: window.opera,
            identity: "Opera",
            versionSearch: "Version"
        },
        {
            string: navigator.vendor,
            subString: "iCab",
            identity: "iCab"
        },
        {
            string: navigator.vendor,
            subString: "KDE",
            identity: "Konqueror"
        },
        {
            string: navigator.userAgent,
            subString: "Firefox",
            identity: "Firefox"
        },
        {
            string: navigator.vendor,
            subString: "Camino",
            identity: "Camino"
        },
        {// for newer Netscapes (6+)
            string: navigator.userAgent,
            subString: "Netscape",
            identity: "Netscape"
        },
        {
            string: navigator.userAgent,
            subString: "MSIE",
            identity: "Explorer",
            versionSearch: "MSIE"
        },
        {
            string: navigator.userAgent,
            subString: "Gecko",
            identity: "Mozilla",
            versionSearch: "rv"
        },
        {// for older Netscapes (4-)
            string: navigator.userAgent,
            subString: "Mozilla",
            identity: "Netscape",
            versionSearch: "Mozilla"
        }
    ],
    dataOS: [
        {
            string: navigator.platform,
            subString: "Win",
            identity: "Windows"
        },
        {
            string: navigator.platform,
            subString: "Mac",
            identity: "Mac"
        },
        {
            string: navigator.userAgent,
            subString: "iPhone",
            identity: "iPhone/iPod"
        },
        {
            string: navigator.platform,
            subString: "Linux",
            identity: "Linux"
        }
    ]

};
//BrowserDetect.init();

Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
    for (var i = 0, len = this.length; i < len; i++) {
        if (this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
}

function check_if_enter(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        changeWelcomeText();
    }
}

function changeWelcomeText() {
    var email = $('#mailing_list input').val(); //collect from page
    var ajax = ajaxObj("POST", "splashpage.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            $('#mailing_list').empty().append("<p>Thanks, talk to you soon!</p>");
        }
    };
    ajax.send("email=" + email);
}

function reset_page(username) {

    frenetic['user'].username = username;
    if (frenetic['user'].username === '') {
        ga('set', 'dimension1', 'stranger_' + Math.round(Math.random() * 10000000000));
        ga('set', 'dimension2', 'not_logged_in');
    } else {
        frenetic['user'].user_id = frenetic['user'].username;
        frenetic['user'].login_status = 'logged_in';
        ga('set', 'dimension1', frenetic['user'].user_id);
        ga('set', 'dimension2', frenetic['user'].login_status);
    }

    set_pageowner(username);

    set_user(username);

    $('.not_logged_in').removeClass('not_logged_in').addClass('logged_in');

    $('.close_prompt').removeClass('close_prompt');
    frenetic.modal.login.close();

    $('#profile_image').removeAttr('style');

    move_furniture();

}

function onboarding() {
    frenetic.modal.onboarding.open();
    $(frenetic.modal.onboarding.background).find('[slide="1"]').removeClass('closed').addClass('open');
}

function login(e, p) {

    if (e === undefined && p === undefined) {
        var e = $('#username').val();
        var p = $('#password').val();
    }

    var ajax = ajaxObj("POST", frenetic.root + "/login.php");
    ajax.onreadystatechange = function() {

        if (ajaxReturn(ajax) === true) {

            if (ajax.responseText === "success") {

                reset_page(e);

                if (frenetic.new_signup) {
                    onboarding();
                }

            } else if (ajax.responseText === "reset_pass") {
                ga('send', 'event', frenetic['user'].username, 'reset_password', 'single_click');
                window.location = frenetic.root + "/forgotPassword.php?reset";
            } else {
                alert(ajax.responseText);
            }
        }
    };

    ajax.send("e=" + e + "&p=" + p);

}

function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (!emailReg.test($email)) {
        return false;
    } else {
        return true;
    }
}

function signup() {

    if ($('#signup_btn').attr('status') === 'signing_up') {
        return;
    }

    $('#signup_btn').attr('status', 'signing_up');

    $('#signup_btn').text("").loadingdots('button_loading');

    $('#signupform input').prop('disabled', true);

    var u, e, p, p2;

    u = $('#signup_username').val();
    e = $('#signup_email').val();
    p = $('#p1').val();
    p2 = $('#p2').val();

    if (u === "") {
        alert('What will people call you? You need a username to sign up.');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    } else if (e === "") {
        alert('How do we know you\'re a real person? You need an email to sign up.');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    } else if (!validateEmail(e)) {
        alert('Please enter a valid email.');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    } else if (p === "") {
        alert('You need a password to sign up and login!');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    } else if (p2 === "") {
        alert('Please confirm your password.');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    } else if (p !== p2) {
        alert('Passwords must match.');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    } else if (p.length < 8) {
        alert('Please make your password at least 8 characters long.');
        $('#signup_btn').removeAttr('status');
        $('#signup_btn .loading_container').remove();
        $('#signup_btn').text("Sign Up");
        $('#signupform input').prop('disabled', false);
        return;
    }


    var ajax = ajaxObj("POST", "signup.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            if (ajax.responseText === "signup_success") {
                $('#signup_btn').removeAttr('status');
                $('#signup_btn .loading_container').remove();
                $('#signup_btn').text("Sign Up");
                $('#signupform input').prop('disabled', false);
                frenetic.new_signup = true;
                reset_page(u);
                onboarding();
                return true;
            } else {
                alert(ajax.responseText);
                $('#signup_btn').removeAttr('status');
                $('#signup_btn .loading_container').remove();
                $('#signup_btn').text("Sign Up");
                $('#signupform input').prop('disabled', false);
                return false;
            }
        }
    };
    ajax.send("e=" + e + "&p=" + p + "&u=" + u);

}
//explode and insplode

function hideStream(streamId) {

    var content = document.getElementById(streamId).querySelectorAll('*');

    for (var i = 0; i < content.length; ++i) {
        content[i].setAttribute("style", "display: none;");
    }

}

function unHideStream(streamId) {
    var content = document.getElementById(streamId).querySelectorAll('*');

    document.querySelector('.stream_header.first').removeAttribute("style");
    document.querySelector('.stream_header.middle').removeAttribute("style");
    document.querySelector('.stream_header.last').removeAttribute("style");

    for (var i = 0; i < content.length; ++i) {
        content[i].removeAttribute("style");
    }
    masonry(1, 5, 'image');
    masonry(1, 5, 'article');
    masonry(1, 5, 'video');
    masonry(1, 5, 'sound');
    masonry(1, 5, 'comment');
}

var filter_drop_count = 0;

function set_filter_drop_count() {
    filter_drop_count++;
}

function explodeStream(elem) {

    var media = elem.attr('type');

    button = elem;

    //detect_loadmore();

    $('#stream_container').scrollTop(0);

    var columns = get_columns()

    var x;

    var a;
    var b;
    var c;
    var d;
    var e;

    switch (media)
    {
        case 'article':
            x = $('#articleStream');
            a = $('#photoStream, .stream_top[type="image"]');
            b = $('#videoStream, .stream_top[type="video"]');
            c = $('#audioStream, .stream_top[type="sound"]');
            d = $('#commentStream, .stream_top[type="comment"]');
            e = 'article';
            break;
        case 'image':
            x = $('#photoStream');
            a = $('#articleStream, .stream_top[type="article"]');
            b = $('#videoStream, .stream_top[type="video"]');
            c = $('#audioStream, .stream_top[type="sound"]');
            d = $('#commentStream, .stream_top[type="comment"]');
            e = 'image';
            break;
        case 'video':
            x = $('#videoStream');
            a = $('#articleStream, .stream_top[type="article"]');
            b = $('#photoStream, .stream_top[type="image"]');
            c = $('#audioStream, .stream_top[type="sound"]');
            d = $('#commentStream, .stream_top[type="comment"]');
            e = 'video';
            break;
        case 'sound':
            x = $('#audioStream');
            a = $('#articleStream, .stream_top[type="article"]');
            b = $('#photoStream, .stream_top[type="image"]');
            c = $('#videoStream, .stream_top[type="video"]');
            d = $('#commentStream, .stream_top[type="comment"]');
            e = 'sound';
            break;
        case 'comment':
            x = $('#commentStream');
            a = $('#articleStream, .stream_top[type="article"]');
            b = $('#photoStream, .stream_top[type="image"]');
            c = $('#videoStream, .stream_top[type="video"]');
            d = $('#audioStream, .stream_top[type="sound"]');
            e = 'comment';
            break;
    }

    if (button.attr('exploded') === 'no') {

        //$('.media_container[media="'+e+'"]').css('visibility','hidden');

        ga('send', 'event', 'single_click', 'explode', e);

        $('.stream_top').css({'width': '100%', 'max-width': '100%'});

        $('.stream_top[type="' + e + '"]').addClass('last').addClass('exploded');
        $('.stream_top[type="' + e + '"]').trigger('mouseleave');
        $('.stream_top[type="' + e + '"]').trigger('mouseenter');

        $('.corner.left').css('width', 40);

        button.attr('exploded', 'yes');
        x.css({'maxWidth': 'none', 'width': '100%'});

        x.addClass('exploded');

        a.css('display', 'none');
        b.css('display', 'none');
        c.css('display', 'none');
        d.css('display', 'none');

        masonry(columns, 10, e);

    } else if (button.attr('exploded') === 'yes') {

        $('.stream_top').removeAttr('style');

        $('.stream_top[type="' + e + '"]').removeClass('last exploded');
        $('.stream_top[type="' + e + '"]').trigger('mouseleave');
        $('.stream_top[type="' + e + '"]').trigger('mouseenter');

        ga('send', 'event', 'single_click', 'insplode', e);

        $('.corner.left').removeAttr('style');

        button.attr('exploded', 'no');
        x.removeAttr('style');
        x.removeClass('exploded');
        if (filter_drop_count > 0) {
            var load = new Object();
            load.type = 'filter';
            load.id = close_the_gate();
            load.scope = frenetic.scope;
            load_content(load);
        }

        a.removeAttr('style');
        b.removeAttr('style');
        c.removeAttr('style');
        d.removeAttr('style');


        masonry(1, 5, 'article');
        masonry(1, 5, 'image');
        masonry(1, 5, 'video');
        masonry(1, 5, 'sound');
        masonry(1, 5, 'comment');

    }

    filter_drop_count = 0;
    //load_content('scrolling', e);
    //detect_loadmore();
}

function move_furniture() {
    $('#search_icon input').focus();
    $('#header').trigger('click');
    $('#search_icon input').focus();

    if (frenetic['user'].login_status === 'logged_in') {
        get_nofication_count();
    }
}

function toggleNavigation(element, person) {

    alert('toggling navigaiton - evaluate remove old function')


    //console.log('toggleNavigation of ' + element.attr('class'));

    button = element;

    $(document).ready(function() {
        $('#stream_container').scrollTop(0);
        $('.nav_button').removeClass('selected');
        element.addClass('selected');
    });

    if (element.hasClass('scope')) {
        updateURL('scope');
    }

    if (person !== undefined) {

        alert('inside toggle nav - look into function use')

        addProfilePicture('single', person);

        set_pageowner(person);

        var load = new Object();
        load.type = 'scope';
        load.id = close_the_gate();
        load.scope = 'single';
        load_content(load);


        return;
    }

    if (element.attr('scope') === 'friends') {

        set_pageowner(frenetic['user'].username);

        var load = new Object();
        load.type = 'scope';
        load.id = close_the_gate();
        load.scope = 'friends';
        load_content(load);
        addProfilePicture(element.attr("scope"));

        updateURL('scope');
    } else if (element.attr('scope') === 'single') {

        if (frenetic['pageowner'].username !== frenetic['user'].username) {
            set_pageowner(frenetic['user'].username);
        }

        addProfilePicture(element.attr("scope"));
        var load = new Object();
        load.type = 'scope';
        load.id = close_the_gate();
        load.scope = 'single';
        //load.person = frenetic['user'].username;
        load_content(load);

        updateURL('scope');
    } else if (element.attr('scope') === 'tribe') {
        var load = new Object();
        load.type = 'fresh_load';
        load.id = close_the_gate();
        load_content(load);
        removeProfilePicture();

        updateURL('scope');
    }

}

function close_header() {
    $('#header, #modal_search').addClass('min');
    $('#tribe_bar_positional .logo').addClass('max');
    $('#tribe_bar_dropdown').addClass('opaque');
}

function open_header() {
    $('#header, #modal_search').removeClass('min');
    $('#tribe_bar_positional .logo').removeClass('max');
    $('#tribe_bar_dropdown').removeClass('opaque');
}

function setup_header_events() {

    var menu_timer;

    $(window).scroll(function() {

        if (!$('#header').hasClass('perm')) {
            if ($(this).scrollTop() > 150) {                
                close_header();  
            } else {
                open_header();
            }
        }

        detect_loadmore();
    });

    $('#tribe_bar_positional .logo').click(function() {

        if ($('#header').hasClass('perm')) {
              close_header();
            $('#header').removeClass('perm');
          
        } else {
            open_header();
            $('#header').addClass('perm');
            
        }


    });

    $('#log_in_option').click(function() {
        frenetic.modal.login.open();
    });

    $('#left_navigation').hover(function() {
        $('#left_navigation *').css('display', 'inline-block');
    }, function() {
        $('#left_navigation *').removeAttr('style');
    });

    $('#switch_container .news, #switch_container .events').click(function() {        
        
        var abc = $('#switch_container .selected');
        if (abc.hasClass('news')) {
            
            $('#stream_container').css('height','');

            frenetic.pagename = 'events';
             $('#header.news, #header .selected.news').removeClass('news').addClass('events');

            
            var load = new Object();
            load.type = 'fresh_load';
            load.id = close_the_gate();
            frenetic.scope = 'tribe';
            load.media = 'event';
            load.event_time = 'anytime';
            load_content(load);

        } else {
            $('#stream_container').css('height', '');

            frenetic.pagename = 'news';
            
            $('#header.events, #header .selected.events').removeClass('events').addClass('news');
            
            var load = new Object();
            load.type = 'fresh_load';
            load.id = close_the_gate();
            frenetic.scope = 'tribe';
            load.media = 'mixed';
            update_scope();
            load_content(load);

        }
    });

//    $('#search_icon').hover(function() {
//
//        if (window.innerWidth > 750) {
//            $('#search_icon input').css('display', 'inline-block').focus();
//            $('#search_icon').css('width', '200px');
//        }
//
//    }, function() {
//        if (window.innerWidth > 750) {
//            if ($('#search_icon input').val().length === 0) {
//                $('#search_icon').removeAttr('style');
//                setTimeout(function() {
//                    $('#search_icon input').removeAttr('style');
//                }, 500);
//
//            }
//        }
//    });

    $('#sign_up_option').click(function() {
        frenetic.modal.signup.open();
    });

    $('#menu_icon, #profile_menu').hover(function() {
        clearTimeout(menu_timer);
        $('#profile_menu').stop().css({'display': 'block'}).animate({'opacity': 1}, 100);
    }, function() {
        menu_timer = setTimeout(function() {
            $('#profile_menu').stop().animate({'opacity': 0}, 100, function() {
                $(this).removeAttr('style');
            });
        }, 100);
    });

    $('#tutorial').click(function() {
        onboarding();
    });

    $('#menu_icon').click(function() {
        if ($(this).attr('status') === 'open') {
            $(this).attr('status', 'closed');
            $('#profile_menu').stop().animate({'opacity': 0}, 100, function() {
                $(this).removeAttr('style');
            });
        } else {
            $(this).attr('status', 'open');
            $('#profile_menu').stop().css({'display': 'block'}).animate({'opacity': 1}, 100);
        }
    });

    $('#stream_container').scroll(function() {

        if (window.innerWidth < 500) {

            if ($(this).scrollTop() > 1) {
                $('#header .word_logo').css('top', '-40px');
                $('#header, #menu_icon').css('top', '0');
                $('#main, #profile_menu, #modal_search').css('top', '50px');

            } else {
                $('#header .word_logo, #header, #menu_icon, #main, #profile_menu, #modal_search').removeAttr('style');
            }

        }

    });

    $('#profile_menu .option, #scope_navigation .scope').hover(function() {
        $(this).css({'background-color': 'rgba(255,165,0,0.5)', 'border-color': 'rgb(255,165,0)'});
    }, function() {
        $(this).removeAttr('style');
    });


    $('#search_icon input').keypress(function(e) {
        $('#search_icon').attr('status', 'open');
        mobile_searchbar.open();
        if (e.keyCode === 13) {

        }
    });

    $('#search_icon img, #search_icon input').click(function() {
        if ($('#search_icon').attr('status') === 'open') {
            $('#search_icon').removeAttr('status');
            mobile_searchbar.close();
        } else {
            $('#search_icon').attr('status', 'open');
            mobile_searchbar.open();
        }
    });

    $('#post_button').click(function() {

        if (frenetic['user'].login_status !== 'logged_in') {
            if (frenetic.pagename === 'news') {
                frenetic.modal.login.open('post content');
            } else {
                frenetic.modal.login.open('post an event');
            }

            return;
        }

        if (frenetic.pagename === 'news') {
            frenetic.modal.upload.open();
        } else {
            frenetic.modal.event_posting.open();
        }
    });

    $('#profile_image img').click(function() {

        if ($('#scope_navigation').length > 0) {
            $('#scope_navigation .scope.single').trigger('click');
        } else {
            window.location = frenetic.root + '/?p=' + frenetic.user.username;
        }
    });

    $('#tribe_bar #media_filter').click(function() {
        if ($('#media_options').hasClass('hidden')) {
            $('#media_options').removeClass('hidden');
        } else {
            $('#media_options').addClass('hidden');
        }

    });

    $('#tribe_bar #media_options li').hover(function() {
        $(this).css({'background-color': 'rgba(255,165,0,0.25)'});
    }, function() {
        $(this).removeAttr('style');
    }).click(function() {
        
        if (frenetic.pagename === 'news') {
            $('#media_filter.this-is-news span').text($(this).text())
            frenetic.media = $(this).attr('media');
            var load = new Object();
            load.type = 'scope';
            load.id = close_the_gate();
            load_content(load);

            //ANALYTICS
            record_content('consumption', 'filter_media', null, {'media': $(this).attr('media')});
        } else if (frenetic.pagename === 'events') {
            $('#media_filter.this-is-event span').text($(this).text())
            frenetic.classifieds.type = $(this).attr('type');
            frenetic.classifieds.time = $(this).attr('time');

            //CONTROLLER CLASSIFIEDS//
            //MARTIN//

            var load = new Object();
            load.event_time = $(this).attr('time');
            load.type = 'scope';
            load.id = close_the_gate();
            load_content(load);
        }

    });

}

function setScopeHREFs() {

    $('.nav_button.scope[number="1"]').attr("href", frenetic.root + "/index.php?rn=tribe");
    $('.nav_button.scope[number="2"]').attr("href", frenetic.root + "/index.php?rn=friends");
    $('.nav_button.scope[number="3"]').attr("href", frenetic.root + "/index.php?rn=single");
}

$(document).ready(function() {

    setup_header_events();

});

$(window).resize(function() {
    move_furniture();

    if ($('.modalBackground[media="event"]').hasClass('open')) {
        $('#modal_viewer .event-description').css({'top': $('.event-image img').height() + 13, 'height': $('#modal_viewer .event_container').height() - $('.event-image img').height() - 13});
        $('.event_container .event-map div').css({'height': $('.event_container').height() - $('.event_container .event-map div').position().top});

    }

});
