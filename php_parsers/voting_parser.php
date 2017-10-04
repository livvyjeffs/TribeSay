<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
//process ajax for postVote() function inside template_photoStream
if (isset($_POST['action'])) {
    //collect all posted variables into local variables
    $action = $_POST['action'];
    $content_id = $_POST['content_id'];
    $token = $_POST['token'];
    $vote_state = $_POST['vote_state'];
    $voter = $_POST['voter'];
    $content_date = $_POST['content_date'];
    $metaType = $_POST['metaType'];
    $author = $_POST["author"];
    
    //interpret action value and decide how to tailor db queries to content type
    if ($action === "article") {
        $action = "articlevotes";
        $dbToUpdate = "articles";
        $idParameter = "uniqueID";
        $dateParameter = "postdate";
    } elseif ($action === "video") {
        $action = "videovotes";
        $dbToUpdate = "videos";
        $idParameter = "uniqueID";
        $dateParameter = "postdate";
    } elseif ($action === "image") {
        $action = "imagevotes";
        $dbToUpdate = "photostream";
        $idParameter = "uniqueID";
        $dateParameter = "postdate";
    } elseif ($action === "sound") {
        $action = "audiovotes";
        $dbToUpdate = "audio";
        $idParameter = "uniqueID";
        $dateParameter = "postdate";
    }
    //check if user has already voted onthis and delete their vote
    $checkVotes = "SELECT * FROM ". $action ." WHERE content_id='$content_id' AND voter='$log_username'";
    $query = mysqli_query($db_conx, $checkVotes);
    if (mysqli_num_rows($query)>0){
        $delete_old_votes = "DELETE FROM ". $action ." WHERE content_id='$content_id' AND voter='$log_username'";
        $query = mysqli_query($db_conx, $delete_old_votes);
    }
    
    if ($token === "UP") {
        //update vote tally locally for ajax
        ++$vote_state;
        //record vote in appropriate votes database
        $log_vote = "INSERT INTO " . $action . " (content_id, content_date, voter, token, postdate) VALUES ('$content_id','$content_date', '$voter', '$token', now())";
        $query = mysqli_query($db_conx, $log_vote);
        //update vote tally in appropriate DB
        $update_count = "UPDATE " . $dbToUpdate . " SET vote_state=(vote_state+1) WHERE " . $idParameter . "='$content_id' AND " . $dateParameter . "='$content_date' LIMIT 1";
        $query = mysqli_query($db_conx, $update_count);
        //update authors points 
        $sql_updatePoints = "UPDATE users SET points=(points+1) WHERE username='$author' LIMIT 1";
        $q_updatePoints = mysqli_query($db_conx, $sql_updatePoints);
        mysqli_close($db_conx);
        echo "vote_up|" . $vote_state;
        exit();
    } elseif ($token === "DOWN") {
        //update vote tally locally for ajax
        --$vote_state;
        //update vote tally in appropriate DB
        $update_count = "UPDATE " . $dbToUpdate . " SET vote_state=(vote_state-1) WHERE " . $idParameter . "='$content_id' AND " . $dateParameter . "='$content_date' LIMIT 1";
        $query = mysqli_query($db_conx, $update_count);
        //record vote in appropriate votes database
        $log_vote = "INSERT INTO " . $action . " (content_id, content_date, voter, token, postdate)
                    VALUES ('$content_id','$content_date', '$voter', '$token', now())";
        $query = mysqli_query($db_conx, $log_vote);
        mysqli_close($db_conx);
        echo "vote_down|" . $vote_state;
        exit();
    } else {
        echo "token variable did not valuate to up or down";
        exit();
    }
}
?><?php
//COMMENT VOTING
if (isset($_POST['comment_unique'])) {
    //collect posted variables
    $comment_unique = $_POST['comment_unique'];
    $content_unique = $_POST['content_unique'];
    $token = $_POST['token'];
    $vote_state = $_POST['vote_state'];
    $previous = $_POST["previous"]; //("none", "UP", "DOWN")
    $author = $_POST["author"];
    //update comment
    if ($token === "UP") {
        //update vote tally locally for ajax
        ++$vote_state;
        //record vote in appropriate votes database
        $sql = "INSERT INTO comment_votes (comment_unique, content_unique, voter, token, date)"
                . " VALUES('$comment_unique', '$content_unique', '$log_username', '$token', now())";
        $query = mysqli_query($db_conx, $sql);
        
        
        
        //update vote tally in appropriate DB
        $update_count = "UPDATE comments SET vote_state=(vote_state+1) WHERE content_id='$content_unique' AND comment_id='$comment_unique' LIMIT 1";
        $query = mysqli_query($db_conx, $update_count);
        
        //update authors points 
        $sql_updatePoints = "UPDATE users SET points=(points+1) WHERE username='$author' LIMIT 1";
        $q_updatePoints = mysqli_query($db_conx, $sql_updatePoints);
        
        mysqli_close($db_conx);
        echo "vote_up|" . $vote_state;
        exit();
    } elseif ($token === "DOWN") {
        //update vote tally locally for ajax
        --$vote_state;
        //update vote tally in appropriate DB
        $sql = "INSERT INTO comment_votes (comment_unique, content_unique, voter, token, date)"
                . " VALUES('$comment_unique', '$content_unique', '$log_username', '$token', now())";
        $query = mysqli_query($db_conx, $sql);
        //update vote tally in appropriate DB
        $update_count = "UPDATE comments SET vote_state=(vote_state-1) WHERE content_id='$content_unique' AND comment_id='$comment_unique' LIMIT 1";
        $query = mysqli_query($db_conx, $update_count);
        mysqli_close($db_conx);
        echo "vote_down|" . $vote_state;
        exit();
    } else {
        echo "token variable did not valuate to up or down";
        exit();
    }
}
?>