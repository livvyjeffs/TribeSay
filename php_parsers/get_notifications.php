<?php
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/convert_date.php");
error_reporting(E_PARSE | E_ERROR);
//get the numner of new notifications for a user
if(isset($_POST["get_note_count"])){
    $username = $_POST["get_note_count"];
    $sql = "SELECT COUNT(id) FROM notifications WHERE receiver='$username' AND did_read='0'";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $count = $row[0];
    //echo value and exit
    echo $count;
    exit();
}
?><?php
//generate notifications
if(isset($_POST["generate_notifications"])){
    //first get the notifications into an array
    $sql = "SELECT * FROM notifications WHERE receiver='$log_username' AND did_read='0' ORDER BY post_date DESC LIMIT 50";
    $query = mysqli_query($db_conx, $sql);
    $unit_array = array();
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $unit = array($row["post_date"], $row["content_type"], $row["content_id"], $row["comment_id"], $row["poster"], $row["category"], $row["uniqueID"], $row["did_read"]);
        array_push($unit_array, $unit);
    }
    //declare response array for json encoding
    $response_array = array();
    //differentiate b/w comments and votes
    foreach($unit_array as $unit){
        //set databases
        switch ($unit[1]) {
        case "article":
            $dbToUpdate = "articles";
            $vote_db = "articlevotes";
            break;
        case "video":
            $dbToUpdate = "videos";
            $vote_db = "videovotes";
            break;
        case "image":
            $dbToUpdate = "photostream";
            $vote_db = "imagevotes";
            break;
        case "sound":
            $dbToUpdate = "audio";
            $vote_db = "audiovotes";
            break;
    }
        $note = array();
        if($unit[5] === "comment"){
            //handle as comment
            $sql = "SELECT * FROM comments WHERE content_id='$unit[2]' AND comment_id='$unit[3]' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
            while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
                $note["category"] = "comment";
                $note["date"] = $row["postdate"];
                $note["time_ago"] = convert_date_timeago($row["postdate"]);
                $note["unique_id"] = $row["content_id"];
                $note["comment_id"] = $row["comment_id"];
                $note["media_type"] = $row["content_type"];
                $note["poster"] = $row["poster"];
                $note["text"] = $row["data"];
                //check if starter or not
                if($row["content_id"] === $row["parent_id"]){
                    $note["target"] = "post";
                }else{
                    $note["target"] = "comment";
                }                
            }
        }else{
            if(!isset($vote_count)){
                $vote_count;
            }
            $note["category"] = "vote";
            $note["poster"] = $unit[4];
            $note["media_type"] = $unit[1];
            $note["unique_id"] = $unit[2];
            $note["comment_id"] = $unit[3];
            if($unit[5] === "comment_vote"){
                //$sql = "SELECT * FROM comment_votes WHERE comment_unique='$unit[3]' AND content_unique='$unit[2]' LIMIT 1";
                //$date = "date";
                $note["target"] = "comment";
            }else{
                //$sql = "SELECT * FROM ".$vote_db." WHERE content_id='$unit[2]' LIMIT 1";
                //$date = "postdate";
                $note["target"] = "post";
            }
            //$query = mysqli_query($db_conx, $sql);
           // while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
                $note["date"] = $unit[0];
                $note["time_ago"] = convert_date_timeago($unit[0]);
            //}
        }
        //get title
        if($unit[1] !== "image"){
            $sql = "SELECT title FROM ".$dbToUpdate." WHERE uniqueID='$unit[2]' LIMIT 1";// AND poster='$unit[4]'
            $query = mysqli_query($db_conx, $sql);
            $row = mysqli_fetch_row($query);
            $note["title"] = $row[0];
        }
        //get user image
        $sql = "SELECT avatar FROM users WHERE username='$unit[4]' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        if($row[0] !== NULL){
            $note["avatar"] = $s3root."/user/$unit[4]/$row[0]";
        }else{
            $note["avatar"] = $root."/sourceImagery/default_avatar.png";
        }
        $note["note_id"] = $unit[6];
        $note["did_read"] = $unit[7];
        //add note to reponse array
        array_push($response_array, $note);
    }
    //sort the array
    rsort($response_array);
    usort($response_array, 'compare_dates');
    echo json_encode($response_array);
    exit();
}
