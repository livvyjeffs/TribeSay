<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/checkTags.php");
?><?php
if(isset($_POST['audioLink'])){
    
    
    $soundCode = $_POST["id"];
    $title = $_POST["title"];
    
    //generate html
    $soundHTML = '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$soundCode.'"></iframe>';
    //echo data
    echo $soundHTML;

    exit();
}
?><?php
//get posted youtube link
//check to make sure that it's actually a youtube link
//isolate video id into variable
if (isset($_POST['audioURL']) && isset($_POST['audioCode'])) {
    $tag1 = $_POST['tag1'];
    $tag2 = $_POST['tag2'];
    $tag3 = $_POST['tag3'];
    $tag4 = $_POST['tag4'];
    $tag5 = $_POST['tag5'];
    

    $description = htmlentities($_POST['description']);
    $description = stripcslashes($description);
    $description = mysqli_real_escape_string($db_conx, $description);

    $rgb_r = $_POST["rgb_r"];
    $rgb_g = $_POST["rgb_g"];
    $rgb_b = $_POST["rgb_b"];
    $ratio = $_POST["ratio"];
    
    $title = htmlentities($_POST['title']);
    $title = stripslashes($title);
    $title = mysqli_real_escape_string($db_conx, $title);

    $audioURL = $_POST['audioURL'];
    $audioCode = $_POST['audioCode'];
    
    $sc_user = $_POST["sc_user"];
    $art_url = $_POST["art_url"];

    $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
    //check/update tags and add if necessary
    $content_type = "sound";
    checkForTag($tag1, $content_type, $db_conx, $log_username);
    checkForTag($tag2, $content_type, $db_conx, $log_username);
    checkForTag($tag3, $content_type, $db_conx, $log_username);
    checkForTag($tag4, $content_type, $db_conx, $log_username);
    checkForTag($tag5, $content_type, $db_conx, $log_username);
    //now send video link into database
    //XX--ADD LINK and HOSTNAME to POSTED VALUES --XX
    $postAudio = "INSERT INTO audio (poster, audioCode, uniqueID, title, postdate, description, tag1, tag2, tag3, tag4, tag5, sc_user, art_url, link, ratio, rgb_r, rgb_g, rgb_b)
                    VALUES ('$log_username', '$audioCode', '$uniqueID', '$title', now(), '$description', '$tag1', '$tag2', '$tag3', '$tag4', '$tag5', '$sc_user', '$art_url', '$audioURL','$ratio', '$rgb_r', '$rgb_g', '$rgb_b')";
    $query = mysqli_query($db_conx, $postAudio);
    //autoupvote by poster
    $log_vote = "INSERT INTO audiovotes (content_id, voter, token, postdate) VALUES ('$uniqueID', '$log_username', 'UP', now())";
    $query = mysqli_query($db_conx, $log_vote);

    //update vote tally in appropriate DB
    $update_count = "UPDATE audio SET vote_state=(vote_state+1) WHERE uniqueID='$uniqueID' LIMIT 1";
    $query = mysqli_query($db_conx, $update_count);
    echo "success||";
    echo $uniqueID;
    echo "||";

    //internal email notification
    $content_post_message = "Posted by: " . $log_username . " at (http://www.tribesay.com/index.php?u=" . $uniqueID . "&m=sound)";
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