function scrapeMedia(button) {
    
    

    if (button.attr('status') === 'busy') {
        return;
    }
    
    button.attr('status','busy').text("").loadingdots('button_loading');
    $('.modalBackground').find('*').css('cursor','wait');    

    //define content to be passed in

    var content = new Object();

    //1. Grabbing URL and redirecting if blank

    var scrape_url = $('#link_input').val();

    if (scrape_url === "") {
        alert("Please enter a URL");
        button.removeAttr('status');
        button.html('').text('Submit');
        $('.modalBackground').find('*').css('cursor', '');
        return;
    }

    content.url = decodeURIComponent(scrape_url);

    //2. Grabbing Media Type

    content.media = $('.header_icon.selected').attr('type');

    //

    if (content.media === 'sound') {
        SC.initialize({
            client_id: 'ca8c1802896517bc68c9c149f3e9f805'
        });
        SC.get('/resolve', {url: scrape_url}, function(track) {
            content.soundcloud_id = track.id;
            content.soundcloud_username = track.user.username;
            content.soundcloud_art_url = track.artwork_url;
            //set default if null else replace with large
            if (content.soundcloud_art_url === null) {
                content.soundcloud_art_url = frenetic.root + "/sourceImagery/spaceholder.jpg";                       //WE NEED A DEFAULT HERE
            } else {
                content.soundcloud_art_url = content.soundcloud_art_url.replace('http:', 'https:');
                content.soundcloud_art_url = content.soundcloud_art_url.replace('-large', '-t500x500');
            }
            content.soundcloud_title = track.title;

            //syncing with embedly format
            content.title = content.soundcloud_title;

            content.soundcloud_html = '<iframe class="bt soundcloud iframe" width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' + content.soundcloud_id + '"></iframe>';

            $("#picture_selector").html(content.soundcloud_html).removeAttr('style');

            button.removeAttr('status');
             $('#uploadContentContainer form .submit').html('').text('Submit');
             $('.modalBackground').find('*').css('cursor','')

            uploadExpand(content);

        });
    } else if (content.media === 'image') {
        var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/photo_system.php");

        ajax.onreadystatechange = function() {
            if (ajaxReturn(ajax) === true) {

                var json = JSON.parse(ajax.responseText);

                content.images = json.image_sources;

                button.removeAttr('status');
                 $('#uploadContentContainer form .submit').html('').text('Submit');
                 $('.modalBackground').find('*').css('cursor','')

                uploadExpand(content);
            }
            
        };

        ajax.send("imagesLink=" + scrape_url);

    } else {
        
        

        content = extract(content);

        if (content !== 'error') {
            if (content.media === 'article') {

                button.removeAttr('status');
                 $('#uploadContentContainer form .submit').html('').text('Submit');
                 $('.modalBackground').find('*').css('cursor','')

                uploadExpand(content);

            } else if (content.media === 'video') {

                button.removeAttr('status');
                 $('#uploadContentContainer form .submit').html('').text('Submit');
                 $('.modalBackground').find('*').css('cursor','')

                uploadExpand(content);
            }
        } else {
            $('.header_icon.selected').trigger('click');
             $('#uploadContentContainer form .submit').html('').text('Submit');
             $('.modalBackground').find('*').css('cursor','')
        }

    }

}

function updatecount() {
    var description = $("#description_editor").val();
    //console.log(description.length + " of 50 character minimum");
}

function postMedia(content) {

    var button = $('#post_to_stream_btn');
    $('.modalBackground').find('*').css('cursor', 'wait');

    if (button.attr('status') === 'posting') {

        return;
    }

    button.attr('status', 'posting');

    content.tags = get_upload_tags();

    ga('send', 'event', 'post_media', content.media, content.tags[0]);
    if (content.tags[0] === "null") {
        alert("Please add at least one tag by typing it in and pressing enter.");
        button.removeAttr('status');
        $('.modalBackground').find('*').css('cursor','');
        $('#post_to_stream_btn').one('click', function() {
            postMedia(content);
        });
        return;
    }

    content.title = $("#title_editor").val();
    content.title = encodeURIComponent(content.title);

    if (content.title === null || content.title === "") {
        if (content.media !== 'image') {
            alert("Please add a title before posting your content.");
            button.removeAttr('status');
            $('.modalBackground').find('*').css('cursor','');
            return;
        }
    }

    content.description = $("#description_editor").val();
    content.description = encodeURIComponent(content.description);


    $('#post_to_stream_btn').text("").loadingdots('button_loading'); 


    //gotta change selection to ID not class

    content.hw_ratio = $('.selectedPicture').height() / $('.selectedPicture').width();

    var img = document.createElement('img');
    img.crossOrigin = '';
    img.src = $(".selectedPicture").attr("src");

    var source = $(".selectedPicture").attr("src");
    source = encodeURIComponent(source);

    var rgb;
    var filepath;

    switch (content.media) {
        case 'article':
            filepath = "/php_parsers/" + content.media + "_system.php";
            break;
        case 'image':
            filepath = "/php_parsers/photo_system.php";
            break;
        case 'video':
            filepath = "/php_parsers/" + content.media + "_system.php";
            break;
        case 'sound':
            filepath = "/php_parsers/audio_system.php";
            img.src = content.soundcloud_art_url;
            break;
    }


    var ajax = new ajaxObj("POST", frenetic.root + filepath);

    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax) === true) {
            
            //DO NOT DELETE THIS COMMENT [postMedia]

            var response = ajax.responseText.split("||");
            if (response[0] === "success") {
                
                //remove filter and load new content in single view, all media types

                var ajaxa = ajaxObj("POST", frenetic.root + "/php_includes/update_favorites.php");
                ajaxa.onreadystatechange = function() {
                    if (ajaxReturn(ajaxa) === true) {
                        $('#tribe_bar .tag_module').remove();
                        set_media_type('mixed');
                        $('#scope_navigation .scope.single').trigger('click');
                    }
                };
                ajaxa.send("clear_all=" + "true");
                
                //submit description as first comment

                var unique_id = response[1];
                var new_comment = new Object();
                new_comment.text = content.description;
                new_comment.content_id = unique_id;
                new_comment.media_type = content.media;
                new_comment.pid = unique_id;
                new_comment.level = 0;
                new_comment.tags = content.tags;

                if (new_comment.text.length > 1) {
                    submit_comment(new_comment, 'fake');
                }
                
                //make things ready for the next update                       
                                
                frenetic.modal.upload.close('no_prompt');
                
                button.removeAttr('status');
                $('.modalBackground').find('*').css('cursor','');

                $('#post_to_stream_btn').html('').text('Post');
                $('#uploadContentContainer form .submit').html('').text('Submit');

            } else {
                alert("failed to put into database: " + ajax.responseText);
                button.removeAttr('status');
                $('.modalBackground').find('*').css('cursor','');
                $('#post_to_stream_btn').html('').text('Post');
                $('#uploadContentContainer form .submit').html('').text('Submit');
            }
        }
    };



    img.onload = function() {

        rgb = getAverageRGB(img);
        content.hw_ratio = img.height / img.width;
        switch (content.media) {
            case 'article':
                //alert('sending content: ' + content.text);
                ajax.send("source=" + img.src + "&title=" + content.title + "&description=" + content.description + "&url=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g + "&content=" + content.text);
                break;
            case 'image':
                ajax.send("chosenLink=" + img.src + "&description=" + content.description + "&url=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g);
                break;
            case 'video':
                //alert('sending')
                ajax.send("title=" + content.title + "&img_src=" + img.src + "&videoHTML=" + encodeURIComponent(content.video) + "&description=" + content.description + "&videoURL=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g);
                break;
            case 'sound':
                ajax.send("title=" + content.title + "&description=" + content.description + "&audioURL=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&audioCode=" + content.soundcloud_id + "&sc_user=" + content.soundcloud_username + "&art_url=" + content.soundcloud_art_url + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g);
                break;
        }
    };

    img.onerror = function() {

        rgb = getAverageRGB(img);
        content.hw_ratio = $('.selectedPicture').height() / $('.selectedPicture').width();
        switch (content.media) {
            case 'article':
                //alert('sending content: ' + content.text);
                ajax.send("source=" + img.src + "&title=" + content.title + "&description=" + content.description + "&url=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g + "&content=" + content.text);
                break;
            case 'image':
                ajax.send("chosenLink=" + img.src + "&description=" + content.description + "&url=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g);
                break;
            case 'video':
                //alert('sending')
                ajax.send("title=" + content.title + "&img_src=" + img.src + "&videoHTML=" + encodeURIComponent(content.video) + "&description=" + content.description + "&videoURL=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g);
                break;
            case 'sound':
                ajax.send("title=" + content.title + "&description=" + content.description + "&audioURL=" + content.url + "&tag1=" + content.tags[0] + "&tag2=" + content.tags[1] + "&tag3=" + content.tags[2] + "&tag4=" + content.tags[3] + "&tag5=" + content.tags[4] + "&audioCode=" + content.soundcloud_id + "&sc_user=" + content.soundcloud_username + "&art_url=" + content.soundcloud_art_url + "&ratio=" + content.hw_ratio + "&rgb_r=" + rgb.r + "&rgb_b=" + rgb.b + "&rgb_g=" + rgb.g);
                break;
        }
    };
}


function get_upload_tags() {

    //console.log('get_upload_tags()');
    var tagList = new Array();
    var tags = document.querySelectorAll('.selected_tags .tag_text');
    for (var i = 0; i < 5; i++) {
        if (i < tags.length) {
            tagList[i] = $(tags[i]).attr('tag');
        } else {
            tagList[i] = "null";
        }
    }
    //console.log('>> return: ' + tagList);
    return tagList;
}

function uploadExpand(content) {
    
    $('#post_to_stream_btn').one('click', function() {
        postMedia(content);
    });

    reset_columntops('image_selector');

    //checks if already expanded, stops 'submit' button from double expanding, refreshes new url content
    $('#post_editor, #post_to_stream_btn').addClass('open');

    //formatting and HTML element creation
    $('#modal_upload').addClass('expanded');

    //auto add tags
    if (content.keywords !== undefined) {
        for (var i = 0; i < 3; i++) {
            if (content.keywords[i] !== undefined) {
                add_to_tag_suggestor(content.keywords[i]);
            }
        }
    }

    //disable url
    $('.uploadForm .submit.button').addClass('open');

    //auto add title
    $('#title_editor').val(content.title);

    if (content.media === 'video') {

        var image = new Image();
        image.src = content.images[0];
        $(image).addClass('selectedPicture');
        $('#picture_selector')[0].appendChild(image);

    } else if (content.media === 'sound') {

    } else {

        if (content.media === 'image') {
            $('#title_editor').css('display', 'none');
        }

        for (var i = 0; i < eval(content.images).length; i++) {

            var image = new Image();
            image.src = content.images[i];
            image.alt = 'Tile Picture';

            if (i === 0) {
                $(image).addClass('selectedPicture');
            } else {
                $(image).addClass('notSelectedPicture');
            }

            image.addEventListener('click', function() {
                toggleSelectedPicture($(this));
            });

            if (content.media === 'image') {

                image.onload = function() {

                    if ($(this).prop('naturalHeight') > 50 && $(this).prop('naturalWidth') > 50) {
                        $('#picture_selector')[0].appendChild(this);
                        masonry(3, 10, 'picture_selector');
                        $('#picture_selector img').first().addClass('selectedPicture').removeClass('notSelectedPicture');
                    }
                };

            } else {

                $('#picture_selector')[0].appendChild(image);
                image.onload = function() {
                    masonry(4, 10, 'picture_selector');
                }

            }



        }
    }

}

function toggleSelectedPicture(elem) {
    if (elem.hasClass('notSelectedPicture')) {
        $('.selectedPicture').removeClass('selectedPicture').addClass('notSelectedPicture');
        elem.removeClass('notSelectedPicture').addClass('selectedPicture');
    }
}


//defines function to call embed.ly api serivce with output variables dependent 
//media type being scraped

function clean_images(images) {

    var sorted_images = [];
    sorted_images.push(images[0]);

    for (var i = 1; i < images.length; i++) {

        if (images[i].height > 50 || images[i].width > 50) {
            for (var j = 0; j < sorted_images.length; j++) {

                if (images[i].size > sorted_images[j].size) {
                    sorted_images.splice(j, 0, images[i]);
                    j = sorted_images.length;
                } else if (j === sorted_images.length - 1) {
                    sorted_images.push(images[i]);
                    j = sorted_images.length;
                }
            }

        }
    }

    return sorted_images;

}

var extract = function(content) {

    var key = "130ce75a704544ad9007ea0d381c1d6b";
    var endpoint = "http://api.embed.ly/1/extract?key=" + key + "&url=" + content.url;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", endpoint, false);
    xhr.send();
    var json = JSON.parse(xhr.responseText);
    //console.log(json);

    if (json.type === 'error') {
        alert('That\'s not a valid URL, try something else.');
        return 'error';
    }    
    
    switch (content.media) {
        case 'article':
            if (json.type === 'image') {
                alert('No articles here, try the image stream. Click OK to continue.');
                $('.header_icon[type="image"]').trigger('click');
                $('#link_input').val(content.url);
                return 'error';
            } else if (json.content === null) {
                alert("No article text was found. Try another link to share.");
                //command prompt into the database                
                return 'error';
            } else if (json.images.length === 0) {
                //eventually add upload feature 
                //or add 'you can still post' but it will only be a tile with a header
                alert('No images found. Try another link to share.');
            } else {
                var cleaned = clean_images(json.images);
                content.images = [];
                for (var i = 0; i < cleaned.length; i++) {
                    eval(content.images).push(cleaned[i].url);
                }
            }
            content.text = encodeURIComponent(json.content);
            content.hostname = json.provider_display;
            content.hostname_url = json.provider_url;
            content.language = json.language;
            break;
        case 'video':
            
             

            if (json.type === 'image') {
                alert('No videos here, try the image stream. Click OK to continue.');
                $('.header_icon[type="image"]').trigger('click');
                $('#link_input').val(content.url);
                return 'error';
            }else if (json.type === 'html' && json.media.type !== 'video') {
                alert('No videos here, try the article stream. Click OK to continue.');
                $('.header_icon[type="article"]').trigger('click');
                $('#link_input').val(content.url);
                return 'error';
            }else if (json.media.html === undefined) {
                alert("No video was found.");
                return 'error';
            }

            content.video = json.media.html;
            content.images = collect_images(json);
            
            break;
        case 'image':
            //this should be a backup of regular image scrape which pulls images
            content.images = collect_images(json);
            break;
        case 'sound':
            content.images = collect_images(json);
            content.media = json.media.html;
            break;
    }
    //universal fields
    content.title = json.title;
    content.authors = json.authors;
    content.keywords = collect_keywords(json);

    if (content.media !== "image") {
        if (content.title.length > 80) {
            alert("Title is longer than 80 characters, please edit.");
        }
    }
    

    return content;

};

var collect_images = function(json) {
    //returns an array of image sources
    var images = [];
    var length = json.images.length;
    for (i = 0; i < length; i++) {
        images.push(json.images[i].url);
    }
    return images;
};

var collect_keywords = function(json) {
    //returns an array of image sources
    var keywords = [];
    var length = json.keywords.length;
    for (i = 0; i < length; i++) {
        keywords.push(json.keywords[i].name);
    }
    return keywords;
};

var collect_authors = function(json) {
    //returns an array of image sources
    var authors = [];
    var length = json.authors.length;
    for (i = 0; i < length; i++) {
        authors.push(json.authors[i].name);
    }
    return authors;
};

var cleanse_text = function(content) {
    if (content === null) {
        //for stackoverflow and other websites not conducive to article scraping, we'll need to display as a link, reddit.com style
        return '';
    } else {
        //Output string of html text content without images or iframes.
        var patt1 = /<img.+">/igm;
        var text = content.replace(patt1, "");
        var patt2 = /<iframe.+iframe>/igm;
        text = text.replace(patt2, "");
        return text;
    }
};

// encode(decode) html text into html entity
var decodeHtmlEntity = function(str) {
    return str.replace(/&#(\d+);/g, function(match, dec) {
        return String.fromCharCode(dec);
    });
};

var log_error = function(problem_url, user, destination) {
    var log_this = problem_url + "," + user;//append and additional fields to this with ','
    //destination can be: log_modal, no_article_txt or no_media_content
    var ajax = ajaxObj("POST", "logs/log_parsers/log_open_modal.php");
    ajax.onreadystatechange = function() {
        if (ajaxReturn(ajax)) {
            alert("log successful");
            return("sex appeal achieved");
        }
    };
    ajax.send("log_this=" + log_this + "&destination=" + destination);
};
