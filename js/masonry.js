var media_type, columnMargin, numberOfColumns, wrapper, tallest, visibility, verticalmargin, horizontalmargin, shortest;

var columntops = new Object();

columntops.page = [];
columntops.image_selector = [];
columntops.friend_viewer = [];

var shortest = 0;

function reset_columntops(type) {   
    

    switch (type) {
        case 'all':

            for (var i in columntops) {
                columntops[i] = [];
            }

            $('[masonized]').removeAttr('masonized');

            break;
        case 'page':
            columntops[type] = [];
            $('#content [masonized]').removeAttr('masonized');
            break;
        case 'image_selector':
            columntops[type] = [];
            $('#picture_selector [masonized]').removeAttr('masonized');
            break;
        case 'friend_viewer':
            columntops[type] = [];
            $('#friendStream [masonized]').removeAttr('masonized');
            break;
    }

    shortest = 0;

}

function masonry(a, b, c, v) {

    numberOfColumns = frenetic.column_count;
    
    columnMargin = verticalmargin = horizontalmargin = 15;
    
    media_type = c;
    tallest = 0;
    visibility = v;

    var holdingStream;
    var content_container;
    var contentArray;
    
    wrapper = $('#content');

    if (media_type === 'picture_selector' && visibility !== 'invisible') {
        wrapper = $('#picture_selector');

        contentArray = $('#picture_selector img:not(".loading_gif")');
        columntops.image_selector = [];
        setValues(contentArray, columntops.image_selector);
    } else if (media_type === 'friendPanel_large') {
        content_container = 'friend_large';
        holdingStream = $('#friendStream');
        contentArray = $('.' + content_container);
        
        columntops.friend_viewer = [];
        setValues(contentArray, columntops.friend_viewer);
        
    } else {

        holdingStream = $('.stream[media_type="' + media_type + '"]');
        contentArray = wrapper.find('.media_container:not(".loadmore_image")').not('[masonized]');

        if (numberOfColumns === 1) {
            reset_columntops('page');
            //use relative default formatting, remove absolute formatting
            contentArray = wrapper.find('.media_container:not(".loadmore_image")');

            //contentArray = $('.media_container[media="' + media_type + '"]:not(".loadmore_image")');
            contentArray.each(function() {
                //$(this).removeClass('unistream').addClass('tristream').removeAttr('style');
                $(this).removeAttr('style').removeAttr('masonized');
            });

            $('.loadmore_image').removeAttr('style');

        } else if (numberOfColumns > 1) {

            setValues(contentArray, columntops.page);

            if (media_type === 'friendPanel_large') {
                $('#friendStream').css("height", tallest);
            }

            $('.stream').each(function() {
                $(this).css('height', tallest);
            });            

        }

    }

}



function setValues(contentArray, specific_columntops) {
            
    var containerWidth = wrapper.innerWidth(true);
    //console.log('normal load width: ' + containerWidth)
    if(wrapper.length === 0){
        containerWidth = window.innerWidth * 0.8 - 14;
        //console.log('preload width: ' + containerWidth);
    }

    var left = 0;
    var scrollbar = 0;
    var padding = 0;

    if (media_type === 'friendPanel_large') {
        padding = 20;
        scrollbar = 20;
    } else if (media_type === 'picture_selector') {
        scrollbar = 15;
    }

    var width = ((containerWidth - scrollbar - horizontalmargin * (numberOfColumns + 1)) / numberOfColumns).toFixed(2) - padding;
    //frenetic.column_width = width;

    for (i = 0; i < numberOfColumns; i++) {
        if (specific_columntops[i] === undefined) {
            specific_columntops[i] = verticalmargin;
        }
    }

    $('#content .loading_container').css({'position': 'absolute'});

    contentArray.each(function() {
        
        for (var i = 0; i < numberOfColumns; i++) {
            if (specific_columntops[i] < specific_columntops[shortest]) {
                shortest = i;
            }
        }
        
        $(this).css({'position':'absolute','margin':0});

        if (visibility !== 'invisible') {
            //console.log('setting to visible')
            $(this).css('visibility', '');
        }

        left = columnMargin * (shortest + 1) + (width + padding) * (shortest);
        $(this).css({'width': width, 'height': 'auto', 'top': specific_columntops[shortest], 'left': left}).attr('masonized', 'yes');

        if ($(this).hasClass('profile_tile') || $(this).hasClass('friend_tile')) {
            //$(this).css({'position': 'fixed', 'top': 48, 'left': -15, 'margin': 0});
            $(this).css({'left': 3, 'top': 15, 'width': width - 20});
        }

        specific_columntops[shortest] += $(this).outerHeight(true) + verticalmargin;

        if (specific_columntops[shortest] > tallest) {
            tallest = specific_columntops[shortest];
            //$('.content[media="' + media_type + '"]').loadingdots(); 
            $('.loadmore_image[media="' + media_type + '"], #content .loading_container').css({'top': tallest, 'width': '100%', 'height': 10});
           $('#stream_container').css('height',tallest);
        }
        
       

    });
    
    $('#picture_selector .loading_gif').remove();
    $('#picture_selector').removeAttr('style');    
}

function get_tallest_column_height(){
    return tallest;
}
