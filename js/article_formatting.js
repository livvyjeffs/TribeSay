function format_article(article_content, content) {

    //text is passed in as object article_content.title and article_content.text
    var text = unescape(article_content.text);
    text = $('<div/>').html(text).text();

    $('#content_holder').append("<div id='article_header_header'><p><b>Originally published on: <a href=" + content.original_link + " target='_blank'>" + content.host_name + "</a></b></p></div><div class='container' tabindex='-1'><div id='article_header'><h1>" + content.title + "</h1><p>Originally published on: <a href=" + content.original_link + " target='_blank'>" + content.host_name + "</a></p></div>" + text + "</div>");

    var patt_img = new RegExp("<img");
    var res_img = patt_img.test(text);

    var patt_iframe = new RegExp("<iframe");
    var res_iframe = patt_iframe.test(text);
    
    clean_article_images(content);

    $('#content_holder .container').focus();

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
        //
        //var height = parseInt($(this).attr('height'), 10);
        //var width = parseInt($(this).attr('width'), 10);
        //$(this).css({'height': $(this).width() * height / width});
        //$(this).css({'height': 450});
    });

    $('#content_holder .container').each(function() {
        if ($(this).overflow() === true) {
            $(this).css({"overflow-y": "scroll", "overflow-x": "hidden"});
            $(this).children().css({'margin-right': '0.1rem'});
        } else {
            $(this).css({"overflow-y": "hidden", "overflow-x": "hidden"});
            $(this).children().css({'margin-right': ''});
        }

    });

    $('#content_holder .container img').each(function() {
        $(this).load(function() {
            $('#content_holder .container').each(function() {
                if ($(this).overflow() === true) {
                    $(this).css({"overflow-y": "scroll", "overflow-x": "hidden"});
                    $(this).children().css({'margin-right': '0.1rem'});
                } else {
                    $(this).css({"overflow-y": "hidden", "overflow-x": "hidden"});
                    $(this).children().css({'margin-right': ''});
                }

            });
        });

    });

    var last_scroll = 0;

   

    $('#content_holder .container').scroll(function() {
        
        if (window.innerWidth < 500) {
            if ($(this).scrollTop() > 10) {
                $('.modal_center_canvas[type="viewer"]').stop().animate({'top': '0'}, 200);
            } else {
                $('.modal_center_canvas[type="viewer"]').stop().animate({'top': '10%'}, 200);
            }
        }

        var tall = $(this).scrollTop();

        if (tall > last_scroll) {
            if (tall > 250 && $('#content_holder #article_header_header').css('opacity') === '0') {
                //console.log('ENTERING')
                $('#content_holder #article_header_header').animate({'opacity': '1'}, 500);
            }
        }
        if (tall < last_scroll) {
            $('#content_holder #article_header_header').stop();
            if (tall < 100) {
                $('#content_holder #article_header_header').css({'opacity': '0'});
            }
        }
        
        last_scroll = tall;
        
    });



}

function clean_article_images(content) {  
        
    //console.log('front of clean_article_images');

    var dirtyarray = $('#content_holder img, #content_holder iframe').not('.loading_gif');

    var internal_link = content.internal_link;

    if (dirtyarray.length === 0) {
        
        $('#content_holder #article_header').after('<figure style="text-align: center"><img src="' + content.image_thumbnail + '" class="header_image"></figure>');
    }

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
                    if ($('#content_holder img').not('.loading_gif').length === 0) {
                        $('#content_holder #article_header').after('<figure style="text-align: center"><img src="' + content.image_thumbnail + '" class="header_image"></figure>');
                    }
                }

            };

            theImage.onerror = function() {
                
                loaded++;
                screenImage.remove();
                //console.log('error and loaded is: ' + loaded + 'VS dirtyarray: ' + dirtyarray.length);
                if (loaded === dirtyarray.length) {
                    if ($('#content_holder img').not('.loading_gif').length === 0) {
                        $('#content_holder #article_header').after('<figure style="text-align: center"><img src="' + content.image_thumbnail + '" class="header_image"></figure>');
                    }
                }
            };



        }
    });
}
