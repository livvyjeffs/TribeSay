<?php
include_once("../php_includes/db_conx.php");
$vs = '4';
//get list of vs number of @japes usernames
$username_array = array();
$sql = "SELECT username FROM users WHERE email LIKE '%@japes.com' ORDER BY RAND() LIMIT ".$vs;//might have to paly with pattern
$query = mysqli_query($db_conx, $sql);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    array_push($username_array, $row["username"]);
}
//print_r($username_array);
//exit();
//generate matching list of dates
$time_ago_array = array();
$current = date('Y-m-d H:i:s');
$date_c = date_create($current);
for ($i = 0; $i < count($username_array); $i++) {
    $rand = rand(0, 15);
    $new = date_sub($date_c, date_interval_create_from_date_string($rand . ' minutes'));
    $date = date_format($new, 'Y-m-d H:i:s');
    array_push($time_ago_array, $date);
}
//combine username and time ago arrays
$note_array = array_combine($username_array, $time_ago_array);
//print_r($note_array);
//exit();
$type = 'article';
$cid = "TueMay622242120149104";
$db = "articles";
//get post data
$sql = "SELECT poster FROM ".$db." WHERE uniqueID='$cid' LIMIT 1";
$query = mysqli_query($db_conx, $sql);
$row = mysqli_fetch_row($query);
$original_poster = $row[0];
$uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
//insert notifications
foreach ($note_array as $username => $date){
    $sql = "INSERT INTO notifications (did_read, poster, receiver, content_id, content_type, post_date, category, uniqueID)
            VALUES('0', '$username', '$original_poster', '$cid', '$type', '$date', 'vote', '$uniqueID')";
    $query = mysqli_query($db_conx, $sql);
}



//echo $current; echo "<br>";







