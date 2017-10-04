function post_event(event, token) {
    
    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/classifieds/classified_payments.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            alert(ajax.responseText);
            if (ajax.responseText === 'success') {
                var json = encodeURIComponent(JSON.stringify(event));

                var ajaxa = ajaxObj("POST", frenetic.root + "/php_parsers/classifieds/post_classified.php");
                ajaxa.onreadystatechange = function() {
                    if (ajaxReturn(ajaxa) === true) {
                        alert(ajaxa.responseText);
                        if (ajaxa.responseText === 'failure') {
                            alert('There was an error processing your event, please reload the page and try again or contact olivia@tribesay.com for support. Thank you for your patience!')
                        }

                        var load = new Object();
                        load.type = 'fresh_load';
                        load.id = close_the_gate();
                        frenetic.scope = 'tribe';
                        load.media = 'event';
                        load_content(load);

                        frenetic.modal.event_posting.close();

                    }
                }
                ajaxa.send('post_classified=' + json);
            }
        }
    };

    if (token === null) {
        ajax.send('initialize_payment=yes&x=' + frenetic.event.paymentplan);
    } else if (token === 'discount') {
        var json = encodeURIComponent(JSON.stringify(event));
        var ajaxa = ajaxObj("POST", frenetic.root + "/php_parsers/classifieds/post_classified.php");
        ajaxa.onreadystatechange = function() {
            if (ajaxReturn(ajaxa) === true) {
                alert(ajaxa.responseText);
                if (ajaxa.responseText === 'failure') {
                    alert('There was an error processing your event, please reload the page and try again or contact olivia@tribesay.com for support. Thank you for your patience!')
                }
                var load = new Object();
                load.type = 'fresh_load';
                load.id = close_the_gate();
                frenetic.scope = 'tribe';
                load.media = 'event';
                load_content(load);

                frenetic.modal.event_posting.close();
            }
        }
        ajaxa.send('post_classified=' + json);
    } else {
        ajax.send('initialize_payment=yes&x=' + frenetic.event.paymentplan + '&stripeToken=' + token.id + '&stripeEmail=' + token.email);
    }

    
}

(function($)
{
    $.fn.wait = function()
    {
        var wait = document.createElement('div');
        wait.id = 'wait_background';

        this[0].appendChild(wait);
                
        $(wait).animate({opacity: 1}, 500);

        spinner_loading(wait, '#000');

    };
})(jQuery);

function modal_events() {       
    

    $('#next_btn').click(function() {
        frenetic.modal.viewer.next();
    });

    $('#previous_btn').click(function() {
        frenetic.modal.viewer.previous();
    });

    $('#modal_ad .close').click(function(e) {
        $('#modal_ad').removeAttr('style');
        e.stopPropogation();
    });

    $(document).keyup(function(e) {

        if ($('#modal_viewer').hasClass('open')) {

            if (e.currentTarget.activeElement.type === 'textarea') {
                return;
            }

            switch (e.keyCode) {
                case 37 :
                    $('#previous_btn').trigger('click');
                    break;
                case 39 :
                    $('#next_btn').trigger('click');
                    break;
            }
        }
    });


    $('#modal_onboarding .next_button').click(function() {
        var slide = $(this).parents('[slide]');
        var current_slide = parseInt(slide.attr('slide'), 10);
        slide.removeClass('open').addClass('closed');

        $('[slide="' + (current_slide + 1) + '"]').removeClass('closed').addClass('open');

        if (current_slide === 2) {
            $(frenetic.modal.onboarding.background).animate({'top': $('#modal_search').css('top'), 'z-index': '10000'}, 500);
            $('#scope_navigation').css('z-index', '10000');
        }

        if (slide.hasClass('last')) {
            frenetic.modal.onboarding.close();
        }

    });

    $('#comment_trigger').click(function() {
        if ($(this).attr('status') === 'open') {
            $(this).removeAttr('status');
            $('.comment_description_container.container').addClass('closed').removeClass('open');
            $('.content_holder.container, #modal_viewer').removeClass('min');
        } else {
            $(this).attr('status', 'open');
            $('.comment_description_container.container').addClass('open').removeClass('closed');
            $('.content_holder.container, #modal_viewer').addClass('min');
            $('#description_input .starter_comment').focus();
        }
    });

    resizeCommentContainer();
    signup_events();

    event_events();

    upload_module();
    debug_module();
    comment_events();
}

function event_events(){
    $(document).on('change', '#event-title', function() {
        frenetic.event.title = this.value;
    });

    $(document).on('change', '#event-description', function() {
        frenetic.event.description = this.value;
    });

    $('input[name="payment-plan"]').change(function() {
        frenetic.event.paymentplan = this.value;
    });

    $('input[name="event-cost"][value="free"]').change(function() {
        if ($(this).prop('checked')) {
            $('#event-ticket-price').val('');
        }
    });

    $('#event-ticket-price').focus(function() {
        $('input[name="event-cost"][value="free"]').prop('checked', false);
    }).focusout(function() {
        if ($(this).val() === '') {
            $('input[name="event-cost"][value="free"]').prop('checked', true);
        }
    });

    var post_handler = StripeCheckout.configure({
        key: "pk_test_RQ4ns97m3O0ghCg8tAKrSbJL",
        allowRememberMe: 'false',
        image: frenetic.root + "/sponsors/style/logo_128.png",
        token: function(token) {
            post_event(frenetic.event, token);
        }
    });

    //$('input[type="radio"]').attr('checked', 'checked').trigger('change');

    $(document).on('change', '#event-image', function(e) {

        e.preventDefault();

        if (this.files.length === 0) {
            return;
        }

        $(this).parents('.modal_center_canvas').wait();

        var formData = new FormData();

        formData.append("image", this.files[0]);

        $.ajax({
            type: 'POST',
            url: frenetic.root + "/php_parsers/classifieds/image_parser.php",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#event-image-thumbnail').remove();
                var json = JSON.parse(data);
                var img = new Image();
                img.src = frenetic.s3root + '/' + json.thumbnail_location;
                img.id = 'event-image-thumbnail';

                img.onload = function() {
                    var rgb = getAverageRGB(img);
                    frenetic.event.rgb_r = rgb.r;
                    frenetic.event.rgb_g = rgb.g;
                    frenetic.event.rgb_b = rgb.b;
                };

                img.onerror = function() {
                    var rgb = getAverageRGB(img);
                    frenetic.event.rgb_r = rgb.r;
                    frenetic.event.rgb_g = rgb.g;
                    frenetic.event.rgb_b = rgb.b;
                };

                $('#wait_background .spinner').animate({'opacity': '0'}, 250, function() {
                    $(this).remove();
                });

                $('#wait_background').append($('<div class="centered"></div>').append($(img)).append("<div><div class='option button' name='no'></div><div class='option button' name='yes'></div></div>"));

                $('#wait_background .option[name="yes"]').click(function() {
                    frenetic.event.thumbnail_location = json.thumbnail_location;
                    frenetic.event.img_location = json.original_image;
                    frenetic.event.ratio = json.ratio;

                    $('#wait_background').remove();

                });

                $('#wait_background .option[name="no"]').click(function() {

                    $('#wait_background').remove();
                    $('#event-image').trigger('click');

                });


            },
            error: function(data) {
                alert('error: ' + data);
            }
        });

    });

    $('#modal_event_posting .action-buttons .button').click(function() {

        for (var i = 0; i < $('#modal_event_posting [required]').length; i++) {
            if ($('#modal_event_posting [required]')[i].value === '') {
                $('#modal_event_posting [required]').each(function(){
                    if($(this).val() === ''){
                        $(this).css('outline', 'red solid medium');
                    }else{
                        $(this).css('outline', '');
                    }
                });
                return;
            }
        }

        //first slide information
        var tags = get_upload_tags();
        frenetic.event.tag1 = tags[0];
        frenetic.event.tag2 = tags[1];
        frenetic.event.tag3 = tags[2];

        frenetic.event.payment_link = $('#event-ticket-link').val();
        frenetic.event.ticket_price = $('#event-ticket-price').val();
        frenetic.event.type = 'event';

        if ($(this).hasClass('pay-button')) {
            if (frenetic.event.paymentplan === 'pinned_post') {
                post_handler.open({
                    amount: "10000",
                    name: "$100 Regular Post",
                    panelLabel: 'Post a Pinned Event',
                });
            } else {
                post_handler.open({
                    amount: "500",
                    name: "$5 Regular Post",
                    panelLabel: 'Post a Regular Event',
                });
            }
        } else if ($(this).hasClass('post-button')) {
            post_event(frenetic.event, null);
        } else if ($(this).hasClass('discount-button')) {
            var code = prompt('What\'s the discount code?');
            if (code === 'tribesay') {
                post_event(frenetic.event, 'discount');
            }
        }

    });

    $('#event-begin-date-time').datetimepicker({
        format: 'F j, Y, g:i a',
        lang: 'en',
        closeOnDateSelect: false,
        startDate: new Date(),
        minDate: new Date(),
        minTime: new Date(),
        onChangeDateTime: function() {

            frenetic.event.event_begin = $('#event-begin-date-time').val();

            $('#event-end-date-time').datetimepicker({
                format: 'F j, Y, g:i a',
                lang: 'en',
                closeOnDateSelect: false,
                startDate: new Date(frenetic.event.event_begin),
                minDate: new Date(frenetic.event.event_begin),
                minTime: new Date(),
                onChangeDateTime: function() {                    
                    frenetic.event.event_end = $('#event-end-date-time').val();
                }
            });


        }
    });

    $('#event-end-date-time').datetimepicker({
        format: 'F j, Y, g:i a',
        lang: 'en',
        closeOnDateSelect: false,
        startDate: new Date(),
        minDate: new Date(),
        minTime: new Date(),
        onChangeDateTime: function() {            
            frenetic.event.event_end = $('#event-end-date-time').val();
        }
    });



    $('#event-location').geocomplete().bind("geocode:result", function(event, result) {

        frenetic.event.radius = '';

        for (var i in result.address_components) {
            for (var j in result.address_components[i]) {
                for (var k in result.address_components[i]['types']) {

                    var longname = result.address_components[i]['long_name'];

                    switch (result.address_components[i]['types'][k]) {
                        case 'street_number':
                            frenetic.event.street_number = longname;
                            break;
                        case 'route':
                            frenetic.event.street_name = longname;
                            break;
                        case 'locality':
                            frenetic.event.city = longname;
                            break;
                        case 'administrative_area_level_1':
                            frenetic.event.state = longname;
                            break;
                        case 'country':
                            frenetic.event.country = longname;
                            break;
                        case 'postal_code':
                            frenetic.event.zip = longname;
                            break;
                    }
                }
            }
        }

        frenetic.event.lat = result.geometry.location.k;
        frenetic.event.long = result.geometry.location.B;

        //new field
        frenetic.event.location_html = result.adr_address;
        frenetic.event.location_formatted = result.formatted_address;

    }).bind("geocode:error", function(event, result) {
        frenetic.event.location = $('#event-location').val();
    });
}

function size_login() {

    if (window.innerWidth < 500) {
        return;
    }

    var background = $('#login_btn').parents('.modalBackground');

    var login_class = $('#modal_login').attr('class');
    var signup_class = $('#modal_signup').attr('class');

    if ($('#modal_login').hasClass('invisible')) {
        login_class = 'modal_container closed';
    }

    //background can either be .open .closed or .invisible, needs to be either .open or .invisible for size_login() to run correctly


    //begins as .modalBackground.invisible and #modal_login.modal_container.invisible

    var previous = $('#modal_login').outerWidth(true);

    if ($('#modal_login').outerWidth(true) === previous) {

        $('#login_btn').css({'width': $('#modal_login').outerWidth(false) - $('#login_with_facebook').outerWidth(true) - 2});
        $('#modal_login').attr('class', login_class);

        $('#modal_signup').addClass('invisible');
        $('#signup_btn').css({'width': $('#modal_signup').outerWidth(false) - $('#signup_with_facebook').outerWidth(true) - 2});
        $('#modal_signup').attr('class', signup_class);

        if (!background.hasClass('open')) {
            background.removeClass('invisible');
        }

    } else {
        previous = $('#modal_login').outerWidth(true);
        size_login();
    }

}

//inside modal()
function modal_share(content) {

    var container = document.createElement('div');
    container.id = 'modal_share';

    var fb = document.createElement('div');
    $(fb).addClass('fb-share-button').attr({'data-href': frenetic.root + '/' + content.media + '/' + content.uid, 'data-type': 'button', 'data-width': '80'});

    var pin = document.createElement('a');
    $(pin).attr({'href': '//www.pinterest.com/pin/create/button/?url=http%3A%2F%2Ftribesay.com%2F' + content.media + '%2F' + content.uid + '&media=' + content.image_src + '&description=' + content.description,
        'data-pin-do': 'buttonPin', 'data-pin-config': 'none', 'data-pin-color': 'white', 'data-pin-height': '28', 'target': '_blank'});
    var pin_image = document.createElement('img');
    pin_image.src = '//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_28.png';

    container.appendChild(fb);
    container.appendChild(pin);

    return container;
}

//inside content_holder()



//inside right_side()

function send_ad_analytics(type, ad, content) {

    ga('set', 'dimension3', content.media);
    ga('set', 'dimension4', ad.customer_id);
    ga('set', 'dimension5', ad.ad_id);

    ga('send', 'event', 'advertisement', type, 'SINGLE_ACTION');

//    ga('send', 'event', 'advertisement', type, 'customer_' + ad.customer_id);
//    ga('send', 'event', 'advertisement', type, 'ad_' + ad.ad_id + '[customer_'+ad.customer_id+']');
//    ga('send', 'event', 'advertisement', type, 'uid_' + content.uid);
//    ga('send', 'event', 'advertisement', type, 'media_' + content.media);
//    ga('send', 'event', 'advertisement', type, 'title_' + content.title);
//    ga('send', 'event', 'advertisement', type, 'username_' + frenetic['user'].username);

    for (var i = 0; i < eval(content.tags).length; i++) {
        ga('send', 'event', 'advertisement', type, 'tag_' + content.tags[i]);
    }

    if (type === 'hover') {
        ga('send', 'event', 'advertisement', type, 'duration_' + ad.duration);
    }

}

function ad_module(content) {

    var size;

    if (window.innerWidth < 500) {
        size = 'small';
    } else {
        size = 'big';
    }

    var container = $('#modal_ad .advertisement')[0];

    var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/get_modal_advert.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {

            if (ajax.responseText === "") {
                $('.content_holder.container').removeAttr('style');
                return;
            }

            var ad = JSON.parse(ajax.responseText);


            var link = document.createElement('a');
            $(link).attr({'href': ad.link, 'target': '_blank'});

            var img = document.createElement('img');
            img.src = frenetic.s3root + "/banner_ads/" + ad.img_src;

            img.addEventListener('click', function() {

                send_ad_analytics('click', ad, content);

            });

            $(img).load(function() {
                ad_resize(this);
            });


            var old_time;

            $(img).hover(function() {
                old_time = new Date().getTime();
            }, function() {
                var new_time = new Date().getTime();
                ad.duration = new_time - old_time;

                send_ad_analytics('hover', ad, content);
            });

            img.onload = function() {
                send_ad_analytics('impression', ad, content);
            };

            link.appendChild(img);
            container.appendChild(link);

            if ($('#content_holder *').length > 0) {
                $('#modal_ad').css('display', 'block');
            }

        }
    };

    ajax.send("tag_array=" + content.tags + "&size=" + size);

}

function ad_resize(img) {
    if (window.innerWidth > 500) {
        if (img === undefined) {
            img = $('#modal_ad img');
        }
        var new_height = $('#modal_viewer').height() * 0.95 - $(img).height();
        $('.content_holder.container').height(new_height);
    }
}

function share_module(content) {

    var container = $('#modal_share')[0];

    var fb = document.createElement('div');
    $(fb).addClass('fb-share-button').attr({'data-href': frenetic.root + '?rn=tribe&m=' + content.media + '&u=' + content.uid + '&f1=' + content.tags[0],
        'data-type': 'button', 'data-width': '80'});

    var pin_link = document.createElement('a');
    $(pin_link).attr({'href': '//www.pinterest.com/pin/create/button/?url=http%3A%2F%2Ftribesay.com%2F' + content.media + '%2F' + content.uid + '&media=' + content.image_thumbnail + '&description=' + content.description,
        'data-pin-do': 'buttonPin', 'data-pin-config': 'none', 'data-pin-color': 'white', 'data-pin-height': '28', 'target': '_blank'});

    var pin_img = document.createElement('img');
    pin_img.src = '//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_28.png';

    pin_link.appendChild(pin_img);

    container.appendChild(fb);
    container.appendChild(pin_link);

    if (get_facebook_sdk() === true) {
        FB.XFBML.parse(document, function() {
            $('#modal_share').animate({'opacity': '1'}, 500);
        });
    }

}

function upload_module() {



    $('.uploadForm .submit.button').click(function() {

        scrapeMedia($(this));
    });

    $('#modal_upload .header_icon').click(function() {

        frenetic.modal.upload.clear();

        var media = $(this).attr('type');

        $('#modal_upload h1').text($(this).attr('title'));

        $('#modal_upload .header_icon').removeClass('selected').addClass('not_selected');
        $(this).removeClass('not_selected').addClass('selected');

        var placeholder_text = 'Enter full URL here...';

        if (media === 'sound') {
            placeholder_text = 'Only SoundCloud links for now.';
        }

        $('#picture_selector').attr('media', media);

        $('#link_input').val('').attr({'placeholder': placeholder_text});

    });

    $('#link_input').keypress(function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('.uploadForm .submit.button').trigger('click');
        }
    });


//    var ul = document.getElementById('tag_selector');
//    var input = document.getElementById('tag_input');
//    input.addEventListener("keyup", function(event) {
//        getSearchIndex(input, ul);
//        var a = navigate(event, this);
//        if (a !== 'stop') {
//            search();
//        }
//    }, false);

}


//signing up

function signup_events() {

    $('#login_here').click(function() {

        frenetic.modal.login.open();
    });

    $('#signup_here').click(function() {
        frenetic.modal.signup.open();
    });

    $('#signup_btn').click(signup);

    $('.create_an_account').click(function() {
        if ($(this).hasClass('login')) {
            frenetic.modal.signup.clear();
            frenetic.modal.login.open();
        } else {
            frenetic.modal.login.clear();
            frenetic.modal.signup.open();
        }
    });
}
function comment_events() {

    $('#description_input .starter_comment').click(function() {
        $(this).attr('rows', '8');
        $('.comment_options').css('display', 'block');
        resizeCommentContainer();
    });

    $('.comment_options .cancel_comment').click(function() {
        $('#description_input .starter_comment').attr('rows', '1').removeAttr('style').val('');
        $('.comment_options').css('display', 'none');
        resizeCommentContainer();
    });
}


////debug

function debug_module() {

    var form = $('#debug_form')[0];

    $(form).on('submit', (function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        var browser = BrowserDetect.browser;
        var mobile = detectmob(); //'true' or 'false'
        var width = $(window).width();
        var height = $(window).height();

        formData.append("browser", browser);
        formData.append("mobile", mobile);
        formData.append("width", width);
        formData.append("height", height);

        $.ajax({
            type: 'POST',
            url: 'php_parsers/save_debug_img.php',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                alert("Thank you for your input! :)\n\nWant to have a conversation? Contact olivia@tribesay.com.");
                //console.log(data);
                $('#info_here').html(data);
                $('#debug_form input, #debug_form textarea').not('[type="submit"]').val("");
            },
            error: function(data) {
                alert("There was an error submitting your debug new_comment.\n\nSend an email to olivia@tribesay.com");
                //console.log(data);
            }
        });
    }));
}