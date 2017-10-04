<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/php_includes/check_login_status.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/php_includes/convert_date.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/php_includes/detect_link.php");

if ((isset($_GET["m"]) && isset($_GET["u"])) || (isset($_POST["m"]) && isset($_POST["u"]))) {
    $load_modal_post = $_POST["load_modal"]; //post this as "yes"
//get URL variabels
    if (isset($_POST["m"])) {
        $media_type = $_POST["m"];
        $uid = $_POST["u"];
        $menu_style = "";
    } else {
        $media_type = $_GET["m"];
        $uid = $_GET["u"];
        $menu_style = "style='visibility: hidden;'";
    }
//format query according to media type
    switch ($media_type) {
        case 'image':
            $sql = "SELECT * FROM photostream WHERE uniqueID='$uid' LIMIT 1";
            break;
        case 'video':
            $sql = "SELECT * FROM videos WHERE uniqueID='$uid' LIMIT 1";
            break;
        case 'article':
            $sql = "SELECT * FROM articles WHERE uniqueID='$uid' LIMIT 1";
            break;
        case 'sound':
            $sql = "SELECT * FROM audio WHERE uniqueID='$uid' LIMIT 1";
            break;
    }
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        //must ensure that database schemas are universal to content types
        //any idiosyncratic fields my be accounted for in type dependent
        //switch statement below                                        
        $uploadDate = $row['postdate'];
        $title = html_entity_decode($row['title']);
        $postedUser = $row['poster'];
        //get poster image path
        $query_avatar = "SELECT * FROM users WHERE username='$postedUser' LIMIT 1";
        $get_avatar = mysqli_query($db_conx, $query_avatar);
        while ($row_a = mysqli_fetch_array($get_avatar, MYSQLI_ASSOC)) {
            $avatar = $row_a["avatar"];
            if ($avatar === null) {
                $avatar = $root . "/sourceImagery/default_avatar.png";
            } else {
                $avatar = $s3root . "/user/" . $postedUser . "/" . $avatar;
            }
        }
        //for advert, $voteState = # servs, and score is equal to this.
        if ($media_type === "advert") {
            $voteState = $row['serv_count'];
            $score = $voteState;
        } else {
            $voteState = $row['vote_state'];
            $score = convert_date($uploadDate, $voteState);
        }
        $time_ago = convert_date_timeago($uploadDate);
        $uniqueID = $row['uniqueID'];
        $tag1 = $row['tag1'];
        $tag2 = $row['tag2'];
        $tag3 = $row['tag3'];
        $tag4 = $row['tag4'];
        $tag5 = $row['tag5'];
        $description = html_entity_decode($row['description']);
        $description = nl2br($description);
        $originaLink = $row['link'];
        $hostName = $row['hostname'];
        $rgb_r = $row["rgb_r"];
        $rgb_g = $row["rgb_g"];
        $rgb_b = $row["rgb_b"];

        //include any non universal database fields here and generate unit array
        switch ($media_type) {
            case "sound":
                $ratio = 1.0; //soundcloud constant                       
                $audio_code = $row['audioCode'];
                $sc_user = $row["sc_user"];
                $art_url = $row["art_url"];
                break;
            case "video":
                $imageName = $row['img_src'];
                if ($imageName === "sourceImagery/spaceholder.jpg") {
                    $imageSource = $root . "/" . $imageName;
                } else {
                    $imageSource = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                }

                $ratio = $row["ratio"];
                $videoHTML = $row['videoHTML'];
                break;
            case "article":
                //$content = html_entity_decode($row['content']);
                //$content_unit["content"] = nl2br($content);
                $ratio = $row["ratio"];
                $content = $row["content"];
                $content = html_entity_decode($content);
                $content = nl2br($content);
                $frame_stat = $row["frame_stat"];
                $imageName = $row['imagesrc'];
                if ($imageName === "sourceImagery/spaceholder.jpg") {
                    $imageSource = $root . "/" . $imageName;
                } else {
                    $imageSource = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                    $imageSource = $imageSource;
                }
                break;
            case "image":
                $ratio = $row["ratio"];
                $imageName = $row['filename'];
                if ($imageName === "sourceImagery/spaceholder.jpg") {
                    $imageLocation = $root . "/" . $imageName;
                } else {
                    $imageLocation = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                    $imageLink = $row['imageLink'];
                }
                break;
        }
    }
    //print html
    $article_header = "";
    switch ($media_type) {
        case 'image':
            $content_html = "<div class='image_content content_container' media='image' order='1'><a href='".$originaLink."' target='_blank'><img src=" . $imageLink . "></a></div>";
            break;
        case 'video':
            $content_html = "<div class='video_content content_container' media='video' order='1'>" . $videoHTML . "</div>";
            break;
        case 'article':
            $article_header = "";
            //$article_header = "<div id='article_header_header'><p><b>Originally published on: <a href=" . $originaLink . " target='_blank'>" . $hostName . "</a></b></p></div>";
            $content_html = "<div class='article_content content_container' media='article' order='1'><div class='container'><div id='article_header'><h1>" . $title . "</h1><p>Originally published on: <a href=" . $originaLink . " target='_blank'>" . $hostName . "</a></p></div><figure style='text-align: center'><img src='" . $imageSource . "' class='header_image'></figure>";
            $content_html .= $content . "</div></div>";
            break;
        case 'sound':
            $content_html = "<div class='sound_content content_container' media='sound' order='1'><iframe width='100%' height='450' scrolling='no' frameborder='no' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/" . $audio_code . "&amp;auto_play=false&amp;hide_related=false&amp;visual=true'></iframe></div>";
            break;
    }
    if ($load_modal_post === "yes") {
        echo $content_html;
        exit();
    }
    $content_display_style = "";
    $stream_display_style = "style='visibility: hidden'";
    $loading_html = "";
} else {
    $content_display_style = "style='display: none'";
    $stream_display_style = "";
    $loading_html = '<div class="spinner" role="progressbar" style="position: absolute; width: 0px; z-index: 2000000000; left: 50%; top: 50%;"><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-0-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(0deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-1-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(27deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-2-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(55deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-3-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(83deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-4-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(110deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-5-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(138deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-6-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(166deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-7-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(193deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-8-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(221deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-9-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(249deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-10-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(276deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-11-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(304deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div><div style="position: absolute; top: -5px; opacity: 0.25; -webkit-animation: opacity-60-25-12-13 1s linear infinite;"><div style="position: absolute; width: 30px; height: 10px; background-color: rgb(255, 255, 255); -webkit-box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 1px; -webkit-transform-origin: 0% 50%; -webkit-transform: rotate(332deg) translate(30px, 0px); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-position: initial initial; background-repeat: initial initial;"></div></div></div>';
    //$content_html = "<div class='article_content content_container'><div class='container'><div id='article_header'><h1>Welcome to TribeSay</h1></div><figure style='text-align: center'><img src='".$root."/sourceImagery/mobile/welcome.png' class='header_image'></figure>";
    //$content_html .= "<p>Great to see you here! We're not on mobile yet, but coming soon.</p> <p><a href='mailto:jp@tribesay.com?subject=Please Add Me to the List'>Contact us</a> and we'll keep you updated by email.</p><p>Love is Love</p><p>Coal is Coal</p><p>Water is Water</p></div></div>";
}
?>

<script>

    var media = '<?php echo $media_type; ?>';
    if (media === "") {
        media = 'article';
    }
    ;

    function get_content_media() {

        return media;
    }

    function set_content_media(type) {
        media = type;
    }

    function get_content_unique() {
        var uid = '<?php echo $uid; ?>';
        return uid;
    }

</script>
