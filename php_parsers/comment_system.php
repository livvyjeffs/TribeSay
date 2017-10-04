<?php
include_once("../php_includes/check_login_status.php");
error_reporting(E_ERROR | E_PARSE);
?><?php
//process postToComment() ajax request variables posted must include: action, data, poster, content_id, content_author, content_type
if (isset($_POST['action']) && $_POST['action'] === "comment_post") {
    $responseText = "";
    // Make sure post data is not empty
    if (strlen($_POST['data']) < 1) {
        mysqli_close($db_conx);
        echo "data_empty";
        exit();
    }
    //collect context variables
    if($_POST["api_call"] === "calling"){
        $log_username = $_POST["username"];
    }
    
    $poster = $log_username;
    $posted_comment_url = $_POST['url'];    
    $parent_unique = $_POST['parent_unique'];                                   //this will be the comment_id of what you'r replying to
    $content_unique = $_POST['content_unique'];                                 //this will the the content unique of what convo is around
    $return_data = nl2br($_POST["data"]);
    $data = htmlentities($_POST['data']);                                       //the actuall comment text itself
    $data = mysqli_real_escape_string($db_conx, $data);
    $contentType = $_POST['content_type'];                                      //sound, video, article, or image
    $level = $_POST["level"]; //that of the parent                                                                       //content level = 0, +1 thereafter in squence
    //update level by one to reflect posting comments level
    $level++;

    //define database to update
    switch ($contentType) {
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
    
    //collect posted tags
    $sql = "SELECT * FROM " . $dbToUpdate . " WHERE uniqueID='$content_unique' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $tag1 = $row["tag1"];
        $tag2 = $row["tag2"];
        $tag3 = $row["tag3"];
        $tag4 = $row["tag4"];
        $tag5 = $row["tag5"];
        $original_poster = $row["poster"];
    }
    
    // Make sure account name exists (the profile being posted on)
    $sql = "SELECT COUNT(id), avatar FROM users WHERE username='$poster' AND activated='1' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    if ($row[0] < 1) {
        mysqli_close($db_conx);
        echo "account_no_exist";
        exit();
    }
    if($row[1] !== Null){
        $poster_profile = $s3root.'/user/'.$log_username."/".$row[1];
    }else{
        $poster_profile = $root.'/sourceImagery/default_avatar.png';
    }
    
    //generate comment_unique
    $comment_unique = "comment_" . date("DMjGisY") . "" . rand(1000, 9999);
    //get length of the string
    $length = strlen($data);
    //add comment to database
    $sql = "INSERT INTO comments(poster, data, content_id, postdate, content_type, parent_id, comment_id, length, level, uniqueID, tag1, tag2, tag3, tag4, tag5) 
			VALUES('$poster', '$data', '$content_unique', now(), '$contentType', '$parent_unique', '$comment_unique', '$length', '$level', '$comment_unique', '$tag1', '$tag2', '$tag3', '$tag4', '$tag5')";
    $query = mysqli_query($db_conx, $sql);
    
    //upvote the comment
    $sql = "INSERT INTO comment_votes (comment_unique, content_unique, voter, token, date)"
            . " VALUES('$comment_unique', '$content_unique', '$log_username', 'UP', now())";
    $query = mysqli_query($db_conx, $sql);
    //update vote tally in appropriate DB
    $update_count = "UPDATE comments SET vote_state=(vote_state+1) WHERE content_id='$content_unique' AND comment_id='$comment_unique' LIMIT 1";
    $query = mysqli_query($db_conx, $update_count);

    //update the comment counter column for the content being commented on\
    if ($contentType === "image") {
        $dbToUpdate = "photostream";
    } elseif ($contentType === "video") {
        $dbToUpdate = "videos";
    } elseif ($contentType === "article") {
        $dbToUpdate = "articles";
    } elseif ($contentType === "sound") {
        $dbToUpdate = "audio";
    }
    //remember to add coment_state to all three databases
    $update_comment_count = "UPDATE " . $dbToUpdate . " SET comment_state=(comment_state+1) WHERE uniqueID='$content_unique' LIMIT 1";
    $query = mysqli_query($db_conx, $update_comment_count);
    

    //$vote_buttons = "<div class='vote_container'><div class='upvote' onclick='voteComment($(this))' previous='no' token='UP'></div><div class='downvote' onclick='voteComment($(this))' previous='no' token='DOWN'></div></div>";
    //$commentHTML = $vote_buttons . "<div class='comment' votes='1' level='" . $level . "' cid='" . $comment_unique . "' sid='" . $sid . "' pid='" . $parent_unique . "'><span class='comment_info'>" . $poster . ", <span class='vote_state'>1</span> vote</span><br>" . $data . "<div class='reply' status='closed' onclick='reply($(this))'>Reply</div></div>";

    //html attributes for each comment in .comment_wrapper
    $comment_information = "level='" . $level . "' vote_state='1' previous='no' content_type='" . $contentType . "' content_id='" . $content_id . "' parent_id='" . $parent_id . "' poster='" . $poster . "'";
    $comment_glass = "<div class='comment_glass'>" . $previousBtn . $nextBtn . "</div>";
    $comment_container = "<div class='comment_container'><div class='wrapper'>" . $comment_glass . "<img src='" . $poster_profile . "' poster='" . $poster . "' class='button'><span class='comment_info'>" . $poster . ", 1 vote</span><br>" . $return_data . "</div></div>";
    $comment_actions = "<div class='comment_actions'><div class='delete_comment button'>DELETE</div></div>";


    //$responseText = "<div class='comment_container just_added' level='" . $level . "'>" . $commentHTML . "</div>";
    $responseText .= "<div class='comment_wrapper just_added loaded' " . $comment_information . ">" . $comment_container . $comment_actions . "</div>";

    $comment_message = "Posted by: " . $poster . " at (" . $posted_comment_url . "&u=".$content_unique."&m=".$contentType."&c=".$content_id.")";
    $headers = 'From: notifications@tribesay.com';
    //internal email notification

    $user_email = get_user_email($db_conx, $log_username);
    if (strpos($user_email, '@japes.com') === false) {
        mail("martin@tribesay.com, olivia@tribesay.com, jp@tribesay.com", "Reply to Posted Comment", $comment_message, $headers);
    }

    mysqli_close($db_conx);
    $responseText .= "||".$comment_unique;
    $responseText .= "||".$original_poster;
    echo $responseText;
    exit();
}
?><?php

if (isset($_POST['action']) && $_POST['action'] === "retrieve_comments") {
    //the ajax that feeds into this block will need the three posted variables
    //listed below. additionally it will need to set parent to content if level 
    //equals 0. level could be determined by the context of calling the js function
    //which calls this ajax. eg "open comments" could set level to 0 while 
    //calling from "convo shift" could have an appropriately pre-defined level.
    //local jquery comment formatting will need to handle numerical level info
    //and context in order to format comments. also the i value could be used to 
    //determing if a shift button is even appropriate. 
    $response_array = array();
    $conversations = array();
    //collect context variables
    $timing = $_POST['timing'];
    $content_unique = $_POST['content_id'];                                     //content id
    $parent_unique = $_POST['parent_id'];                                       //parent of the comment you click next to, or content if initial load
    $level = intval($_POST['level']);                                           //default at 0, otherwise level of comment clicking next to                                    
    $collect_sibling = intval($_POST["sibling_id"]);                            //collect this integer from page, default at 1
    //optional array for traversing tree back
    $parents = array($parent_unique);
    //declare max supplement just in case there are only primaries
    $max_out = 0;
    $comment_count = 0;
    //declare i just in case there are no comments on queries level
    $i = 1;
    //get the top 1st degree comment with this content_unique
    //query for child, this should be a recursive loop until no children
    $continue = "yes";
    $convo = array();
    while ($continue === "yes") {
        //query for comment in current level - basically pulls the top child
        $sql = "SELECT * FROM comments WHERE content_id='$content_unique' AND parent_id='$parent_unique' ORDER BY vote_state DESC";
        $query = mysqli_query($db_conx, $sql);
        if (mysqli_num_rows($query) !== 0) {
            $i = 1;
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $unit = array();
                if ($i === $collect_sibling) {
                    $unit["total"] = mysqli_num_rows($query);
                    //collect data from this comment and put it into html for response.
                    $unit["comment_id"] = $row['comment_id'];
                    $comment_id = $row['comment_id'];
                    $parent_unique = $row['comment_id']; 
                    $unit["content_id"] = $row["content_id"];
                    $content_id = $row["content_id"];
                    $poster = $row["poster"];
                    $unit["poster"] = $row["poster"];
                    //get user avatar
                    $sql_a = "select * FROM users WHERE username='$poster' LIMIT 1";
                    $query_a = mysqli_query($db_conx, $sql_a);
                    while($row_a = mysqli_fetch_array($query_a, MYSQLI_ASSOC)){
                        $avatar = $row_a["avatar"];
                        if($avatar === null){
                            $unit["poster_profile"] = $root."/sourceImagery/default_avatar.png";
                        }else{
                            $unit["poster_profile"] = $s3root."/user/".$poster."/".$avatar;
                        }
                        $unit["avatar_ratio"] = $row_a["ratio"];
                    }
                    $data = html_entity_decode($row["data"]);
                    $unit["data"] = nl2br($data);
                    $unit["post_date"] = $row["postdate"];
                    $unit["content_type"] = $row["content_type"];
                    $unit["parent_id"] = $row["parent_id"];
                    $unit["length"] = $row["length"];
                    $level = intval($row["level"]);
                    $unit["level"] = $level;
                    $unit["vote_state"] = $row["vote_state"];
                    $unit["previous"] = "no";
                    $tag_array = array("tag1","tag2","tag3","tag4","tag5");
                    foreach($tag_array as $tag){
                        $unit[$tag] = $row[$tag];
                    }
                    $collect_sibling = 1; // reset so that top child comment there after is selected.
                    $sid = $i;
                    $unit["sid"] = $sid;
                    //HTML HERE
                   // if ($timing === 'initial' && $level === 1) {
                        //$responseText .= "<div class='conversation_container' starterCID='" . $cid . "'>";
                    //}
                    //check if user has voted on this
                    $sql2 = "SELECT * FROM comment_votes WHERE voter='$log_username' AND comment_unique='$comment_id' AND content_unique='$content_id'";
                    $query2 = mysqli_query($db_conx, $sql2);
                    if (mysqli_num_rows($query2) !== 0) {
                        while ($row2 = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
                            $token = $row2["token"];
                            if ($token === "UP") {
                                //user has already upvotes, allow them to undo upvote or do downvote
                                $unit["previous"] = "UP";
                            } elseif ($token === "DOWN") {
                                //same as above but oposite
                                $unit["previous"] = "DOWN";
                            }
                        }
                    }else{
                        $unit["none"];
                    }
                    //$vote_buttons = "<div class='vote_container'><div class='upvote' onclick='voteComment($(this))' " . $previous . " token='UP' votestate='".$vote_state."'></div></div>";
                    //$commentHTML = $vote_buttons . "<div class='comment' votes='" . $vote_state . "' level='" . $level . "' cid='" . $comment_id . "' sid='" . $sid . "' pid='" . $parent_id . "'><span class='comment_info'>" . $poster . ", <span class='vote_state'>" . $vote_state . "</span> votes</span><br>" . $data . "<div class='reply' status='closed' onclick='reply($(this))'>Reply</div></div>";
                    array_push($convo, $unit);
                    $comment_count++;
                }
                $i++;
                //selectively echo back info on the i state since shifting is allowed.
            }
            
            //declare buttons
            //$previousBtn = "";
            //$nextBtn = "";
            //
            
            //$single_status = " single";

            //$fraction = "<div class='fraction'><sup>" . $sid . "</sup>&frasl;<sub>" . $total . "</sub></div>";

            //check for previous button
            if ($sid > 1) {
               // $single_status = "";
               // $previousBtn = "<div class='sub_comment_navigation previous_button button' direction='previous' sid='" . $sid . "' total='" . $total . "'></div>";
            }
            
            //check for next button
            if ($total > $sid) {
               // $single_status = "";
                //$nextBtn = "<div class='sub_comment_navigation next_button button' direction='next' sid='" . $sid . "' total='" . $total . "'>" . $fraction . "<div class='button'></div></div>";
            }
            
            //check if only previous
            if ($total === $sid && $total > 1) {
               // $single_status = "";
                //$previousBtn = "<div class='sub_comment_navigation previous_button button solo' direction='previous' sid='" . $sid . "' total='" . $total . "'><div class='button'></div>" . $fraction . "</div>";
            }
            //$comment_delete = "";
            if ($log_username === $poster){
                //$comment_delete = "<div class='delete_comment button'>DELETE</div>";
            }
            
            //html attributes for each comment in .comment_wrapper
               // $comment_information = "level='" . $level . "' sid='".$sid."' vote_state='" . $vote_state . "' previous='" . $previous . "' content_type='" . $content_type . "' comment_id='" . $comment_id . "' content_id='" . $content_id . "' parent_id='" . $parent_id . "' poster='" . $poster . "' post_date='" . $post_date . "'";
                //$comment_container = "<div class='comment_container'><div class='wrapper'>" . $previousBtn . $nextBtn . "<img src='" . $poster_profile . "' poster='".$poster."' class='button'><span class='comment_info'>" . $poster . ", <span class='vote_state'>" . $vote_state . " vote" . $plural . "</span></span><br>" . $data . "</div></div>";
               // $comment_actions = "<div class='comment_actions'><div class='vote_container'><div class='upvote button' votestate='".$vote_state."' previous='".$previous."' token='UP'></div></div><div class='share button' media='comment'></div>".$comment_delete."<div class='reply button'>Reply</div></div>";
                //$comment_reply = "<div class='comment_reply'><textarea rows='5'></textarea><div class='reply_action button'><div class='post_reply'>Submit</div></div></div>";


                //$responseText .= "<div class='comment_container' level='" . $level . "'>" . $commentHTML . $previousBtn . "<div class='sub_comment_navigation tally'>" . $sid . "/" . $total . "</div>" . $nextBtn . "</div>";
            //$responseText .= "<div class='comment_wrapper".$single_status."' " . $comment_information . ">" . $comment_container . $comment_actions . $comment_reply . "</div>";
        } else {
            array_push($conversations, $convo);
            if ($timing === 'initial') {
                //close convo
                //$responseText .= "</div>";
            }
            if ($level !== 1 || $max_out > 5 || $i === ($max_out + 2) || $timing === "default") { //the greater than number decides whether to load more primaries
                $continue = "no";
                //exit and have no more comments
            } else {
                $convo = array();
                //keep pulling comments but signal a new convo to start with level
                $level--;
                //set parent unique to previous from array
                //$parent_unique = array_pop($parents);
                $parent_unique = $content_unique;
                //this can only happen limited time so max out at 10?
                $max_out++;
                //make sure collect sibling test can pass
                $collect_sibling = $max_out + 1;
            }
        }
    }
    /*$sql = "SELECT COUNT(id) FROM comments WHERE content_id='$content_unique'";
    $query = mysqli_query($db_conx, $sql);
    $result = mysqli_fetch_row($query);*/
    $response_array["num_comments"] = $comment_count;
    array_push($response_array, $conversations);
    //array_push($response_array, array("total" => $total));
    echo json_encode($response_array);
    exit();
}
?><?php

if (isset($_POST['action']) && $_POST['action'] === "retrieve_specific_convo") {
    $uniqueID = $_POST["unique_id"];
    $original_id = $uniqueID;
    $response_array = array();
    //generate an array of comment_is's, with older generations added to begining
    //(unshift)and younger generations added to the end(push).
    $id_array = array();
    //get older generations, stop once level=1
    $level = null;
    do {
        $sql = "SELECT * FROM comments WHERE uniqueID='$uniqueID' ORDER BY vote_state DESC";
        $query = mysqli_query($db_conx, $sql);
        $i = 0;
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            if ($i === 0) {
                $uniqueID = $row["parent_id"];
                $this_id = $row["uniqueID"];
                $level = intval($row["level"]);
            }
            $i++;
        }//this i does not represent the true number of siblings...please fix by using this parent to find true siblings
        $sib_check = "SELECT * FROM comments WHERE parent_id='$uniqueID' ORDER BY vote_state DESC";
        $query_sib = mysqli_query($db_conx, $sib_check);
        $i = mysqli_num_rows($query_sib);
        $a = 1;
        while ($row = mysqli_fetch_array($query_sib, MYSQLI_ASSOC)) {
            $chose_child = $row["uniqueID"];
            if ($chose_child === $original_id) {
                $sid = $a;
            }
            $a++;
        }
        $this_unit = array($i . "|" . $sid => $this_id);
        array_unshift($id_array, $this_unit);
    } while ($level !== 1);
    //get younger generations, stop once num_rows <0
    //reset uniqueID
    $uniqueID = $original_id;
    $sql = "SELECT * FROM comments WHERE parent_id='$uniqueID' ORDER BY vote_state DESC";
    $query = mysqli_query($db_conx, $sql);
    $num_rows = mysqli_num_rows($query);
    while ($num_rows >= 1) {
        $push = "no";
        $i = 0;
        $sql = "SELECT * FROM comments WHERE parent_id='$uniqueID' ORDER BY vote_state DESC";
        $query = mysqli_query($db_conx, $sql);
        $num_rows = mysqli_num_rows($query);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            if ($i === 0) {
                $push = "yes";
                $uniqueID = $row["uniqueID"];
                $this_id = $uniqueID;
                $sid = $i + 1;
            }
            $i++;
        }
        if ($push === "yes") {
            $this_unit = array($i . "|" . $sid => $this_id);
            array_push($id_array, $this_unit);
        }
    }
    //iterate over id_array and generate html
    foreach ($id_array as $unit) {
        foreach ($unit as $total_sib => $id) {
            //parse total_sid
            $context_data = explode("|", $total_sib);
            $total = $context_data[0];
            $sid = $context_data[1];
            //query for content data
            $sql = "SELECT * FROM comments WHERE uniqueID='$id' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $unit = array();
                //collect data from this comment and put it into html for response.
                $comment_id = $row['comment_id']; //
                $unit["comment_id"] = $comment_id;
                $content_id = $row["content_id"]; //
                $unit["content_id"] = $content_id;
                $unit["poster"] = $row["poster"]; //
                $data = html_entity_decode($row["data"]);
                $unit["data"] = nl2br($data);
                $unit["postdate"] = $row["postdate"]; //
                $unit["content_type"] = $row["content_type"]; //
                $unit["parent_id"] = $row["parent_id"]; //
                $unit["length"] = $row["length"]; 
                $level = intval($row["level"]); //
                $unit["level"] = $level;
                $unit["vote_state"] = $row["vote_state"]; //
                $tag_array = array("tag1", "tag2", "tag3", "tag4", "tag5");
                foreach ($tag_array as $tag) {
                    $unit[$tag] = $row[$tag];
                }
                $unit["previous"] = "no"; //
                //get user avatar
                $sql_a = "select * FROM users WHERE username='$poster' LIMIT 1";
                $query_a = mysqli_query($db_conx, $sql_a);
                while($row_a = mysqli_fetch_array($query_a, MYSQLI_ASSOC)){
                    $avatar = $row_a["avatar"];
                    if($avatar === null){
                        $unit["profile_pic"] = $root."/sourceImagery/default_avatar.png";
                    }else{
                        $unit["profile_pic"] = $s3root."/user/".$poster."/".$avatar;
                    }
                }
                if($vote_state === 1){
                   // $plural = "";
                }else{
                    //$plural = "s";
                }
                
                if ($level === 1) {                    
                    //$responseText .= "<div class='conversation_container' starterCID='" . $cid . "'>";
                }
                //check if user has voted on this
                $sql2 = "SELECT * FROM comment_votes WHERE voter='$log_username' AND comment_unique='$comment_id' AND content_unique='$content_id'";
                $query2 = mysqli_query($db_conx, $sql2);
                if (mysqli_num_rows($query2) !== 0) {
                    while ($row2 = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
                        $token = $row2["token"];
                        if ($token === "UP") {
                            //user has already upvotes, allow them to undo upvote or do downvote
                            $unit["previous"] = "UP";
                        } elseif ($token === "DOWN") {
                            //same as above but oposite
                            $unit["previous"] = "DOWN";
                        }
                    }
                }
             
                
                //$vote_buttons = "<div class='vote_container'><div class='upvote' onclick='voteComment($(this))' " . $previous . " token='UP'></div><div class='downvote' onclick='voteComment($(this))' " . $previous . " token='DOWN'></div></div>";
                //$commentHTML = $vote_buttons . "<div class='comment' votes='" . $vote_state . "' level='" . $level . "' cid='" . $comment_id . "' sid='" . $sid . "' pid='" . $parent_id . "'><span class='comment_info'>" . $poster . ", <span class='vote_state'>" . $vote_state . "</span> votes</span><br>" . $data . "<div class='reply' status='closed' onclick='reply($(this))'>Reply</div></div>";
                //declare buttons
               // $previousBtn = "";
                //$nextBtn = "";
                //check for previous button
                //$fraction = "<div class='fraction'><sup>" . $sid . "</sup>&frasl;<sub>" . $total . "</sub></div>";
                //$single_status = " single";
                //check for previous button
                if ($sid > 1) {
                    //$single_status = "";
                    //$previousBtn = "<div class='sub_comment_navigation previous_button button' direction='previous' sid='" . $sid . "' total='" . $total . "'></div>";
                }

                //check for next button
                if ($total > $sid) {
                   // $single_status = "";
                    //$nextBtn = "<div class='sub_comment_navigation next_button button' direction='next' sid='" . $sid . "' total='" . $total . "'>" . $fraction . "<div class='button'></div></div>";
                }

                //check if only previous
                if ($total === $sid && $total > 1) {
                    //$single_status = "";
                    //$previousBtn = "<div class='sub_comment_navigation previous_button button solo' direction='previous' sid='" . $sid . "' total='" . $total . "'><div class='button'></div>" . $fraction . "</div>";
                }
                
                $comment_delete = "";
                if ($log_username === $poster) {
                    //$comment_delete = "<div class='delete_comment button'>DELETE</div>";
                }
/*
                //html attributes for each comment in .comment_wrapper
                $comment_information = "level='" . $level . "' sid='" . $sid . "' vote_state='" . $vote_state . "' previous='" . $previous . "' content_type='" . $content_type . "' comment_id='" . $comment_id . "' content_id='" . $content_id . "' parent_id='" . $parent_id . "' poster='" . $poster . "' post_date='" . $post_date . "'";
                $comment_container = "<div class='comment_container'><div class='wrapper'>" . $previousBtn . $nextBtn . "<img src='" . $poster_profile . "' poster='".$poster."' class='button'><span class='comment_info'>" . $poster . ", <span class='vote_state'>" . $vote_state . " vote" . $plural . "</span></span><br>" . $data . "</div></div>";
                $comment_actions = "<div class='comment_actions'><div class='vote_container'><div class='upvote button' votestate='".$vote_state."' previous='".$previous."' token='UP'></div></div><div class='share button' media='comment'></div>".$comment_delete."<div class='reply button'>Reply</div></div>";
                $comment_reply = "<div class='comment_reply'><textarea rows='5'></textarea><div class='reply_action button'><div class='post_reply'>Submit</div></div></div>";
*/

                //$responseText .= "<div class='comment_container' level='" . $level . "'>" . $commentHTML . $previousBtn . "<div class='sub_comment_navigation tally'>" . $sid . "/" . $total . "</div>" . $nextBtn . "</div>";
                //$responseText .= "<div class='comment_wrapper".$single_status."' " . $comment_information . ">" .$comment_container . $comment_actions . $comment_reply . "</div>";
                array_push($response_array, $unit);
            }
        }
    }
    //$responseText .= "</div>"; //end .conversation_container
    echo $responseText;
    exit();
}
?><?php
//delete comment
if(isset($_POST["delete_id"])){
    $id = $_POST["delete_id"];
    $sql = "DELETE FROM comments WHERE comment_id='$id' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    echo "success";
    exit();
}

?>
