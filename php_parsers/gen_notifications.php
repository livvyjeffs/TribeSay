<?php
include_once("../php_includes/check_login_status.php");
include_once("../email_tem/comment_notif.php");
require("../libs/sendgrid-php/sendgrid-php.php");
error_reporting(E_PARSE | E_ERROR);
//receive comment id + content id to create notifications
if(isset($_POST["original_poster"])){
    $original_poster = $_POST["original_poster"];
    $comment_id = $_POST["comment_unique"];
    $o_cid = $comment_id;
    $content_id = $_POST["content_unique"];
    $media_type = $_POST["media_type"];
    $parent_unique = $_POST["parent_unique"];
    $add_log;
    $link = $_POST["link"];//write this link
    //get title
    if($media_type !== "image"){
        switch ($media_type) {
        case "sound":
            $conten_db = "audio";
            $votes_db = "audiovotes";
            break;
        case "video":
            $conten_db = "videos";
            $votes_db = "videovotes";
            break;
        case "article":
            $conten_db = "articles";
            $votes_db = "articlevotes";
            break;
        case "image":
            $conten_db = "photostream";
            $votes_db = "imagevotes";
            break;
    }
        $sql = "SELECT title FROM ".$conten_db." WHERE uniqueID='$content_id' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        $title = $row[0];
    }else{
        $title = "Your image";
    }
    //
    //$gen unique ID
    $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
    //generate noti for author
    if($original_poster !== $log_username){
        $sql = "INSERT INTO notifications (did_read, poster, receiver, content_id, comment_id, content_type, post_date, category, uniqueID)
                VALUES('0', '$log_username', '$original_poster', '$content_id', '$comment_id', '$media_type', now(), 'comment', '$uniqueID')";
        $query = mysqli_query($db_conx, $sql);
        $add_log = true;
    }
    //generate notifications for upstream users
    $poster_array = array();
    $level = null;
    do {
        $sql = "SELECT * FROM comments WHERE uniqueID='$comment_id' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $comment_id = $row["parent_id"];
            $level = intval($row["level"]);
            $poster = $row["poster"];
        }
        if($poster !== $log_username && $poster !== $original_poster){
            array_push($poster_array, $poster);
        }
    } while ($level !== 1);
    
    //purge poster array for uinuques
    $poster_array = array_unique($poster_array);
    //generate sql row values
    $values =  "";
    foreach($poster_array as $p){
        $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
        $values.= "('0', '$log_username', '$p', '$content_id', '$o_cid', '$media_type', now(), 'comment', '$uniqueID'),";
    }
    //strip ending comma
    $values = rtrim($values, ",");
    //insert rows
    $sql = "INSERT INTO notifications (did_read, poster, receiver, content_id, comment_id, content_type, post_date, category, uniqueID)
            VALUES".$values;
    $query = mysqli_query($db_conx, $sql);
    if($add_log === true){array_push($poster_array, $original_poster);}
    //send email notifications if qualitfied
    //print_r($poster_array);
    foreach($poster_array as $poster){
        //check: if email notifications are enabled - receiver email
        $sql = "SELECT activated, email FROM users WHERE username='$poster' LIMIT 1"; 
        $query = mysqli_query($db_conx ,$sql);
        $row = mysqli_fetch_row($query);
        $notifs_enabled = $row[0];
        $e = $row[1];
        if($notifs_enabled === '1'){
            $msg = comment_notif($poster, $title, $log_username, $link);
            $sendgrid = new SendGrid('TribeSay', 'shitsocial8');
            $mail = new SendGrid\Email();
            $mail->addTo($e)->
                    setFrom('support.com')->
                    setFromName('TribeSay Notifications')->
                    setSubject($log_username . " commented on your post.")->
                    setText($log_username . " commented on your post.")->
                    setHtml($msg);
            $resut = $sendgrid->send($mail);
        }
    }
    //echo and exit
    echo "success";
    exit();
}
?><?php
//UpVote notification
if(isset($_POST["vote_note"])){//if vote is on a comment then media_type = comment
    $content_id = $_POST["vote_note"];
    $media_type = $_POST["media_type"];
    $comment_id = $_POST["comment_id"];
    $category = $_POST["category"];//set up category = vote on what type if voting on comment
    $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
    //get correct db
    switch ($media_type) {
        case "article":
            $dbToUpdate = "articles";
            break;
        case "video":
            $dbToUpdate = "videos";
            break;
        case "image":
            $dbToUpdate = "photostream";
            break;
        case "sound":
            $dbToUpdate = "audio";
            break;
    }
    //get original poster
    $sql = "SELECT poster FROM ".$dbToUpdate." WHERE uniqueID='$content_id' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $original_poster = $row[0];
    if($original_poster === $log_username){
        exit();
    }
    //generate notification
    $sql = "INSERT INTO notifications (did_read, poster, receiver, content_id, comment_id, content_type, post_date, category, uniqueID)
            VALUES('0', '$log_username', '$original_poster', '$content_id', '$comment_id', '$media_type', now(), '$category', '$uniqueID')";
    $query = mysqli_query($db_conx, $sql);
    echo "success";
    exit();
}
//mark notification as read
if(isset($_POST["read_note"])){//values: 'all' or 'uniqueID'
    $uniqueID = $_POST["read_note"];
    if($uniqueID === "all"){
        //mark all as read
        $sql = "UPDATE notifications SET did_read='1' WHERE receiver='$log_username' AND did_read='0'";
    }else{
        //mark unqueID only as read
        $sql = "UPDATE notifications SET did_read='1' WHERE receiver='$log_username' AND did_read='0' AND uniqueID='$uniqueID' LIMIT 1";
    }
    $query = mysqli_query($db_conx, $sql);
    echo "success";
    exit();
}
//mark notification as new
if (isset($_POST["mark_new"])) {
    $uniqueID = $_POST["mark_new"];
    $sql = "UPDATE notifications SET did_read='0' WHERE receiver='$log_username' AND did_read='1' AND uniqueID='$uniqueID' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    echo $uniqueID;
    echo "success";
    exit();
}
