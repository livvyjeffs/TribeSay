<?php

include_once("../../php_includes/check_login_status.php");

//script to calculate points retroactively
//must be done in batches so not to time out

//Declare tables to calc points with format: table => receiver_column
/*
 * Elements that contribute to score
 *      -Comment upvote
 *          +comment_votes[comment_unique]
 *          +comments[comment_unique]->[poster] +score
 *      -Content upvote
 *          +articlevotes[content_id]
 *          +articles[uniqueID]->[poster] +score
 *      -Comment reply?
 */
//Content Vote Points
//Declare array of content voteDb's and correspoints db's
$content_dbs = array("articles"=>"articlevotes", "audio"=>"audiovotes","photostream"=>"imagevotes","videos"=>"videovotes", "comments"=>"comment_votes");
$point_tally = array();
foreach($content_dbs as $content_db => $votes_db){
    set_time_limit(30);
    //get all content id's from votes_db
    if($content_db !== "comments"){$idParameter="content_id";}else{$idParameter="comment_unique";}
    $sql_getIDs = "SELECT ".$idParameter." FROM " . $votes_db . " WHERE token='UP'";
    $r_getIDs = mysqli_query($db_conx, $sql_getIDs);
    while ($row = mysqli_fetch_array($r_getIDs)) {
        $content_id = $row[0];
        //get poster of that comment
        $sql_getPoster = "SELECT poster FROM ".$content_db." WHERE uniqueID='$content_id' LIMIT 1";
        $query_getPoster = mysqli_query($db_conx, $sql_getPoster);
        $r_poster = mysqli_fetch_row($query_getPoster);
        $poster = $r_poster[0];
        if(!(isset($point_tally[$poster]))){$point_tally[$poster] = 0;}
        $point_tally[$poster] += 1;
    }
    echo $content_db . " tally complete!<br>";
}
print_r($point_tally);
set_time_limit(60);
foreach ($point_tally as $poster => $points) {
    //update the posters points
    $sql_updatePoints = "UPDATE users SET points=".$points." WHERE username='$poster' LIMIT 1";
    if(!($query_updatePoints = mysqli_query($db_conx, $sql_updatePoints))){echo "<br>".$poster." update failed";}
}
echo "<br><br>update complete";
?>
