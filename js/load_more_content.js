//this will be the the ajax function that gets called when people scroll to the bottom of a stream and want more content!
//trigger upon getting to the bottom of the page
//remeber to pass is container and template arguments to adapt to other streams later

//poopmajigger


function loadMore(articleCount, photoCount, videoCount, isOwner, isFriend, pageOwner, streamID, content, explodeID, parser){
    var changeWidth = "no";
    var streamContainer = _("stream_container");
    //later inheret article explode as a variable
    var contentCount;
    if(explodeID === "articleExplode"){
        contentCount = articleCount;
    }else if(explodeID === "photoExplode"){
        contentCount = photoCount;
    }else if(explodeID === "videoExplode"){
        contentCount = videoCount;
    }
    if(isOwner === 1){
        isOwner = "true";
        isFriend = "false";
    }else if(isFriend === 1){
        isOwner = "false";
        isFriend = "true";
    }
    var wrap = _(content); //this will be different for each stream and should represent that streams vertical height
    /*var contentHeight = wrap.offsetHeight; //total height of stream container
    var streamContainer = _(container);
    var yOffset = streamContainer.scrollTop; //amount scrolled so far vertically
    var y = yOffset + streamContainer.offsetHeight; //might want to add to this since content container might now account fo header and footer etc 
    //the if condition below is triggering for everystream on every scroll so far as i checked... it should only trigger when we hit the bottom
    var wrapperClass = document.getElementById(content).getAttribute("class");
    var index = wrapperClass.indexOf("columns-");
     var pxfromtop = 0;

    if (index < 0) {
        pxfromtop = contentHeight;
    } else {
        var numberOfColumns = wrapperClass.substring(index + 8, index + 9);

        var childArray;

        if (content === 'photo_wrapper') {

            childArray = document.querySelectorAll('#' + content + ' .photo_container');
        } else if (content === 'articleContent') {

            childArray = document.querySelectorAll('#' + content + ' .article_container');
        } else if (content === 'videoContent') {

            childArray = document.querySelectorAll('#' + content + ' .video_container');
        }

       

        for (var i = 0; i < numberOfColumns; i++) {
            for (var j = 0; j < childArray.length; j++) {

                if (j % numberOfColumns === i) {
                    var newpx = (childArray[j].offsetTop + childArray[j].offsetHeight);
                    if (newpx > pxfromtop) {
                        pxfromtop = newpx;
                    }
                }
            }
        }
    }*/
    
    /*ONLY WORKS IF LESS THAN 10*/

    //if(y >= pxfromtop){
        //testing to see if loadmoreimages is over triggereing
        //streamContainer.setAttribute("onscroll", "");
        //here is where we spit out the new content blocks using container.innerHTML+= ajax.responseText
        //post to php parser instead, find a way to get the latest article info in...
        var ajax = ajaxObj("POST", parser);
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                if(ajax.responseText === "no_more_articles"){
                    streamContainer.setAttribute("onscroll", "check_if_at_bottom(0,"+photoCount+","+videoCount+");");
                }else if(ajax.responseText === "no_more_photos"){
                    streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+",0,"+videoCount+");");
                }else if(ajax.responseText === "no_more_videos"){
                    streamContainer.setAttribute("onscroll", "check_if_at_bottom(" + articleCount + "," + photoCount + ",0);");
            } else {
                wrap.innerHTML += ajax.responseText;

                var numberOfColumns;
                var margin;
                if (document.getElementById(streamID).className.indexOf("exploded") !== -1) {
                    numberOfColumns = 3;
                    margin = 10;
                    changeWidth = "yes";
                } else {
                    numberOfColumns = 1;
                    margin = 5;
                }

                masonry(numberOfColumns, margin, content);
            }
        }
    };
    //pass throuhg $isOwner and $isFriend info as well
    //pass in $u
        ajax.send("action="+"loadMore"+"&contentCount="+contentCount+"&isOwner="+isOwner+"&isFriend="+isFriend+"&pageOwner="+pageOwner+"&splodeStatus="+changeWidth);
    //}
};

function check_if_at_bottom_NULL(articleCount, photoCount, videoCount){
    var changeWidth = "no";
    //define scroll down amount
    var streamContainer = _("stream_container");
    var yOffset = streamContainer.scrollTop;
    var y = yOffset + streamContainer.offsetHeight;
    //
    ////console.log("y= "+y);
    //
    var articleHeight = _("articleStream").offsetHeight;
    var photoHeight = _("photoStream").offsetHeight;
    var videoHeight = _("videoStream").offsetHeight;
    //log the heights:
    ////console.log("articleStream: "+articleHeight+" count: "+articleCount);
    ////console.log("photoStream: "+photoHeight+" count: "+photoCount);
    ////console.log("videoStream: "+videoHeight+" count: "+videoCount);
    if(_("articleExplode").className === "insplodeBtn"){
        changeWidth = "yes";
        articleHeight = _("articleStreamContainer").offsetHeight;
        masonry(3,10,'article');
    }else if (_("photoExplode").className === "insplodeBtn"){
        changeWidth = "yes";
        photoHeight = _("photoStream").offsetHeight;
        masonry(3,10,'photo_wrapper');
    }else if (_("videoExplode").className === "insplodeBtn"){
        changeWidth = "yes";
        videoHeight = _("videoStreamContainer").offsetHeight;
        masonry(3,10,'videoContent');
    }else{
        masonry(1,5,'photo_wrapper');
        masonry(1,5,'articleContent');
        masonry(1,5,'videoContent');
    }
    //check if scrolled down to bottom
    if(y >= articleHeight && articleCount !== 0){
        ////console.log("article Pull");
        //load more articles
        loadMore(articleCount, photoCount, videoCount,1,0,'TRIBE', 'articleStreamContainer', 'articleContent', 'articleExplode', 'php_parsers/load_more_articles.php', changeWidth);
        var new_articleCount = articleCount + 12;
        streamContainer.setAttribute("onscroll", "check_if_at_bottom("+new_articleCount+","+photoCount+","+videoCount+");");
    }
    if(y >= photoHeight && photoCount !== 0){
        ////console.log("photo Pull");
        //load more photos
        loadMore(articleCount, photoCount, videoCount,1,0,'TRIBE', 'photoStreamContainer', 'photo_wrapper', 'photoExplode', 'php_parsers/load_more_photos.php', changeWidth);
        var new_photoCount = photoCount + 12;
        streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+","+new_photoCount+","+videoCount+");");
    }
    if(y >= videoHeight && videoCount !== 0){
        ////console.log("video Pull");
        //load more videos
        loadMore(articleCount, photoCount, videoCount,1,0,'TRIBE', 'videoStreamContainer', 'videoContent', 'videoExplode', 'php_parsers/load_more_videos.php', changeWidth);
        var new_videoCount = videoCount + 12;
        streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+","+photoCount+","+new_videoCount+");");
    }
}

function check_if_at_bottom(articleCount, photoCount, videoCount) {
    
    masonize_appropriately();
         
    ////console.log("article: " + articleCount);
    ////console.log("photo: " + photoCount);
    ////console.log("video: " + videoCount);
    var streamContainer = _("stream_container");
    var heightFromTop = streamContainer.scrollTop + streamContainer.offsetHeight;
    ////console.log("heighFromTop: "+heightFromTop);
    var streamArray = document.querySelectorAll('.stream');
    for (i = 0; i < streamArray.length; i++) {
        ////console.log(streamArray[i].id+" "+streamArray[i].offsetTop + streamArray[i].offsetHeight);
        if ((streamArray[i].offsetTop + streamArray[i].offsetHeight) <= heightFromTop) {
            //change masonry to be unistream/tristream independent]
            //if already exploded then all content containers are unistream
            switch (streamArray[i].id) {
                case 'articleStream':
                    if(articleCount !== 0){
                        ////console.log("article triggered: "+ articleCount);
                        loadMore(articleCount, photoCount, videoCount,1,0,'TRIBE', 'articleStream', 'articleContent', 'articleExplode', 'php_parsers/load_more_articles.php');
                        articleCount += 12;
                        //streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+","+photoCount+","+videoCount+");");
                    }
                    break;
                case 'photoStream':
                     if(photoCount !== 0){
                        ////console.log("photo triggered: "+ photoCount);
                        loadMore(articleCount, photoCount, videoCount,1,0,'TRIBE', 'photoStream', 'photo_wrapper', 'photoExplode', 'php_parsers/load_more_photos.php');
                        photoCount += 12;
                        //streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+","+photoCount+","+videoCount+");");
                     }
                    break;
                case 'videoStream':
                     if(videoCount !== 0){
                        ////console.log("video triggered: "+ videoCount);
                        loadMore(articleCount, photoCount, videoCount,1,0,'TRIBE', 'videoStream', 'videoContent', 'videoExplode', 'php_parsers/load_more_videos.php');
                        videoCount += 12;
                        //streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+","+photoCount+","+videoCount+");");
                     }
                    break;
            }
        }
    }
    streamContainer.setAttribute("onscroll", "check_if_at_bottom("+articleCount+","+photoCount+","+videoCount+");");
}

function masonize_appropriately(){
    if(_("articleExplode").className === "insplodeBtn"){
        masonry(3, 10, 'articleContent');
    }else if (_("photoExplode").className === "insplodeBtn"){
        masonry(3, 10, 'photo_wrapper');
    }else if (_("videoExplode").className === "insplodeBtn"){
        masonry(3, 10, 'videoContent');
    }else{
        masonry(1, 5, 'articleContent');
        masonry(1, 5, 'photo_wrapper');
        masonry(1, 5, 'videoContent');   
    }
}