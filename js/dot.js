var detect_loadmore_type = 'desktop';

function set_loadmore_type(type) {
    detect_loadmore_type = type;
}

(function($)
{
    $.fn.loadingdots = function(options)
    {

        var i = 0, settings = $.extend({}, {duration: 250}, options),
                bucle = function() {
                    var $el = $(this), cycle, timing = i * settings.duration, first = true;

                    i++;

                    cycle = function()
                    {

                        if (detect_loadmore_type === 'mobile') {

                            detect_loadmore();
                        }

                        // if it's not the first time the cycle is called for a dot then the timing fired is 0
                        if (!first)
                            timing = 0;
                        else {
                            first = false;
                            if (detect_loadmore_type === 'desktop' &&options === 'stream_loader' ) {
                                

                                detect_loadmore();
                            }

                        }
                        // delay the animation the timing needed, and then make the animation to fadeIn and Out the dot to make the effect
                        $el.delay(timing)
                                .fadeTo(500, 0.4)
                                .fadeTo(500, 0, cycle);
                    };

                    cycle(first);
                };
        // for every element where the plugin was called we create the loading dots html and start the animations
        return this.each(function()
        {
            $(this)
                    .append('<div class="loading_container ' + options + '"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>')
                    .find('.dot')
                    .each(bucle);
        });

    };
})(jQuery);



(function($)
{
    $.fn.loadmore = function()
    {

        switch (frenetic.scope) {
            case 'tribe':

                if (frenetic.media === 'mixed') {

                } else {
                    var div = document.createElement('div');
                    $(div).addClass('media_container load_more').attr('media', $(this).attr('media'));

                    var text_a = document.createTextNode('We\'re out of ' + $(this).attr('media') + ' content. Click this button ');
                    var text_b = document.createTextNode(' to post content and grow your tribe.');

                    var button = document.createElement('img');
                    button.src = frenetic.root + "/sourceImagery/post_button.png";
                    button.addEventListener('click', function() {
                        $('#post_button').trigger('click');
                    });

                    var header = document.createElement('h1');

                    header.appendChild(text_a);
                    header.appendChild(button);
                    header.appendChild(text_b);

                    div.appendChild(header);

                    $(this)[0].appendChild(div);
                }

                break;
            case 'friends':

                var wait = document.createElement('div');
                $(wait).addClass('wait_background');

                var message = document.createElement('div');

                if (get_filter_tags().length > 0) {
                    var tag = $('#tribe_bar .tag_text').first().attr('tag');
                    $(message).addClass('modal_container').html('Your tribe hasn\'t posted any <b>' + tag + '</b> content yet, try clearing the filter to see more.<br><span class="button">Back to the Tribe</span>');

                } else {
                    $(message).addClass('modal_container').html('You\'re not following anybody yet, try following some people!<br><span class="button">Back to the Tribe</span>');

                }

                wait.appendChild(message);

                $('#main')[0].appendChild(wait);

                $('#wait_background .modal_container .button').click(function() {
                    $('.scope.tribe').trigger('click');
                });

                $(wait).animate({opacity: 1}, 500);

                $('#content .stream_loader').remove();

                break;

            case 'single':
                var instruction;
                var tag = $('#tribe_bar .tag_text').first().attr('tag');
                if (tag === undefined) {
                    tag = "content";
                    instruction = "changing the media type";
                    switch ($('#media_filter span').text()) {
                        case 'All Types':
                            tag = "content";
                            break;
                        case 'Images':
                            tag = "images";
                            break;
                        case 'Video':
                            tag = "videos";
                            break;
                        case 'Sound':
                            tag = "music or sound";
                            break;
                        case 'Articles':
                            tag = "articles";
                            break;
                    }
                } else {
                    instruction = "clearing the filter";
                    tag = tag + ' content'
                }

                var wait = document.createElement('div');
                $(wait).addClass('wait_background');
                
                $(wait).append('<div class="no_empty pseudo_before vmiddle"></div>');

                var message = document.createElement('div');
               

                wait.appendChild(message);

                $('#main')[0].appendChild(wait);

                $(wait).animate({opacity: 1}, 500);

                $('#content .stream_loader').remove();

                if (frenetic['page_owner'].username === frenetic['user'].username) {
                    $(message).addClass('modal_container').html('You haven\'t posted any<b> ' + tag + ' </b> yet, click the <img class="button centered" src="' + frenetic.root + '/sourceImagery/post_button.png"> to grow your tribe.<br><span class="button">Back to the Tribe</span>');

                } else {

                    $(message).addClass('modal_container').html(frenetic['page_owner'].username + ' hasn\'t posted any <b>' + tag + '</b> yet, try ' + instruction + ' to see more.<br><span class="button">Back to the Tribe</span>');

                }

                $('.wait_background .modal_container span.button').click(function() {
                    $('.scope.tribe').trigger('click');
                    $(wait).remove();
                });

                $('.wait_background .modal_container img.button').click(function() {
                    $('#post_button').trigger('click');
                    $(wait).remove();
                });

                break;
        }




    };
})(jQuery);

