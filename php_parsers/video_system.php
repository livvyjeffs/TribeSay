<?php

error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/checkTags.php");
include_once("../scraping/posting_modules/photo_post.php");
include_once ("../scraping/main_scraper.php");
include_once("../php_includes/image_resize.php");//this could be used as a trait as well
?><?php

if (isset($_POST['audioLink'])) {
    $audioLink = $_POST['audioLink'];
    //parse link
    $parse = explode("tracks/", $audioLink);
    $parse1 = $parse[1];
    //$parse1 = array();
    if (strpos($parse1, "&") !== false) { //adudio link should be parse one
        $parse1 = explode("&", $parse1);
    }
    $soundCode = $parse1;

    //generate html
    $soundHTML = '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/' . $soundCode . '"></iframe>';
    //echo data
    $soundHTML = "";
    $responseText = $soundHTML;
    $responseText .= "|delimiter|";
    $responseText .= "Not a real title";
    $responseText .= "|delimiter|";
    $responseText .= $soundCode;
    echo $responseText;
    exit();
}
?><?php

if (isset($_POST['videoLink']) && $_POST['videoLink'] !== "") {
    $responseText = "";
    //extract relevant article info from link
    $titleArray = array();
    $link = urldecode($_POST['videoLink']);
    //generate new DOMdoc
    $article = new DOMDocument;
    $article->loadHTMLFile($link);
    //get the articles title
    $titles = $article->getElementsByTagName("title");
    foreach ($titles as $title) {
        $articleTitle = $title->textContent;
        array_push($titleArray, $articleTitle);
    }
    $TITLE = $titleArray[0];
    //eventuall check for playlists and make this and if else
    //check what url type and henerate sourceHTML
    if (strpos($link, "v=") !== false) {
        $linkArray = explode("v=", $link);
        $videoID = $linkArray[1];
        if (strpos($videoID, "&list=") !== false) {
            $linkArray = explode("&list=", $videoID);
            $videoID = $linkArray[0];
        }
        $id = "image0";
        $sourceHTML = '<img id="' . $id . '" class="selectedPicture" src="http://img.youtube.com/vi/' . $videoID . '/0.jpg" alt="alt">';
        //$sourceHTML = '<img id="'.$id.'" class="notSelectedPicture" src="http://img.youtube.com/vi/'.$videoID.'/0.jpg" onclick="toggleSelectedPicture(\''.$id.'\');" alt="alt">';
    } else {
        echo "failure";
        exit();
    }
    //need to echo back title and image array and display in the uploader html 
    $responseText .= $sourceHTML;
    $responseText .= "|delimiter|";
    $responseText .= $TITLE;
    echo $responseText;
    exit();
}
?><?php

//get posted youtube link
//check to make sure that it's actually a youtube link
//isolate video id into variable
if (isset($_POST['videoURL'])) {
    $tag1 = $_POST['tag1'];
    $tag2 = $_POST['tag2'];
    $tag3 = $_POST['tag3'];
    $tag4 = $_POST['tag4'];
    $tag5 = $_POST['tag5'];
    $youtubeLink = $_POST['videoURL'];
    $videoHTML = $_POST["videoHTML"];
    $videoID = null;
    if (strpos($youtubeLink, "v=") !== false) {
        $linkArray = explode("v=", $youtubeLink);
        $videoID = $linkArray[1];
        if (strpos($videoID, "&list=") !== false) {
            $linkArray = explode("&list=", $videoID);
            $videoID = $linkArray[0];
        }
    }

    $description = htmlentities($_POST['description']);
    $description = stripcslashes($description);
    $description = mysqli_real_escape_string($db_conx, $description);

    $title = htmlentities($_POST['title']);
    $title = stripslashes($title);
    $title = mysqli_real_escape_string($db_conx, $title);

    $rgb_r = $_POST["rgb_r"];
    $rgb_g = $_POST["rgb_g"];
    $rgb_b = $_POST["rgb_b"];
    $ratio = $_POST["ratio"];

    $postLink = urldecode($_POST['img_src']);
    
    ///
    if ($postLink !== "sourceImagery/spaceholder.jpg") {
        $post_this = new image_to_post($postLink, $log_username, "stream");
        $post_this->remove_url_variables();
        $post_this->make_tmp();
        $post_this->file_get_and_put();
        $post_this->check_size();
        if($post_this->loop_tracker === "exit"){
                $post_this->check_size();
            }
        $post_this->gen_db_name();
        $post_this->resize_image();       
        $post_this->move_to_permanent();
        $db_file_name = $post_this->db_file_name;
        $post_this->get_img_specs();
        if ($_POST["message"] === "api_ping_successful") {
            $ratio = $post_this->img_ratio;
        }
    } else {
        $db_file_name = $postLink;
    }
    
    $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
    //check/update tags and add if necessary
    $content_type = "videos";
    checkForTag($tag1, $content_type, $db_conx, $log_username);
    checkForTag($tag2, $content_type, $db_conx, $log_username);
    checkForTag($tag3, $content_type, $db_conx, $log_username);
    checkForTag($tag4, $content_type, $db_conx, $log_username);
    checkForTag($tag5, $content_type, $db_conx, $log_username);
    //now send video link into database
    $postVideo = "INSERT INTO videos (poster, videoID, uniqueID, title, postdate, description, tag1, tag2, tag3, tag4, tag5, ratio, rgb_r, rgb_g, rgb_b, videoHTML, img_src)
                    VALUES ('$log_username', '$videoID', '$uniqueID', '$title', now(), '$description', '$tag1', '$tag2', '$tag3', '$tag4', '$tag5','$ratio', '$rgb_r', '$rgb_g', '$rgb_b', '$videoHTML', '$db_file_name')";
    $query = mysqli_query($db_conx, $postVideo);
    //autoupvote by poster
    $log_vote = "INSERT INTO videovotes (content_id, voter, token, postdate) VALUES ('$uniqueID', '$log_username', 'UP', now())";
    $query = mysqli_query($db_conx, $log_vote);

    //update vote tally in appropriate DB
    $update_count = "UPDATE videos SET vote_state=(vote_state+1) WHERE uniqueID='$uniqueID' LIMIT 1";
    $query = mysqli_query($db_conx, $update_count);
    echo "success||";
    echo $uniqueID;
    echo "||";

    //internal email notification
    $content_post_message = "Posted by: " . $log_username . " at (http://www.tribesay.com/index.php?u=" . $uniqueID . "&m=video)";
    $headers = 'From: notifications@tribesay.com';

    $user_email = get_user_email($db_conx, $log_username);
    if (strpos($user_email, '@japes.com') === false) {
        mail("martin@tribesay.com, olivia@tribesay.com, jp@tribesay.com", "New Content Posted", $content_post_message, $headers);
    }
    exit();
}
mysqli_close($db_conx);
exit();
?>
    
