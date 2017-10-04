<?php
//include_once("db_conx.php");
if(isset($_GET['u'])){
    //initialize and sanitize username variable
    $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
    //$u = "martinaman123456";
    $friends = array();
    //Query friends table for all of posted user's array of friends
    $sql = "SELECT * FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        if($row['user1'] === $u){
            array_push($friends, $row['user2']);
        } elseif($row['user2'] === $u) {
            array_push($friends, $row['user1']);
          }
    }
}
?>
