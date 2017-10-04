<?php
error_reporting(E_ERROR | E_PARSE);
include_once("db_conx.php");

function convert_date($post_date, $vote_state){
    $current = date('Y-m-d H:i:s');
    $date_p = date_create($post_date);
    $date_c = date_create($current);
    $diff = date_diff($date_p, $date_c);
    $months = $diff->format("%M") * 672;
    $days = $diff->format("%D") * 24;
    $hours = $diff->format("%H");
    $total_diff_hours = $months + $days + $hours;
    //echo "total diff: ".$total_diff_hours;
    $denom = pow($total_diff_hours + 2,1.8);//change back to 2 for live
    $score = $vote_state/$denom;
    return $score;
}

function convert_date_timeago($post_date){
    $current = date('Y-m-d H:i:s');
    $date_p = date_create($post_date);
    $date_c = date_create($current);
    $diff = date_diff($date_p, $date_c);
    
    $months = $diff->format("%M");
    $days = $diff->format("%D");
    $hours = $diff->format("%H");
    $minutes = $diff->format("%i");
    if($months !== "00"){
        return ltrim($months,"0")."mos ago";
    }elseif($days !== "00"){
        return ltrim($days,"0")."d ago";
    }elseif($hours !== "00"){
        return ltrim($hours,"0")."h ago";
    }elseif($minutes !== "0"){
        return ltrim($minutes,"0")."m ago";       
    }else{
        return "just now";
    }
}

function compare_dates($a, $b) {
    if($a['date'] < $b['date']){
        return 1;
    }elseif($a['date'] > $b['date']){
        return -1;
    }else{
        return 0;
    }
}

/*
$sql = "SELECT * FROM videos";
$query = mysqli_query($db_conx, $sql);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $oldDate = $row['postdate'];
    echo $oldDate;
    $time_ago = convert_date_timeago($oldDate);
    echo "<br>posted ".$time_ago." ago.";
}
echo $current = date('Y-m-d H:i:s');
$fewmin = "2014-03-19 17:20:31";
echo "<br><br>";
echo "posted ".convert_date_timeago($fewmin);
echo "<br><br>";
echo "posted ".convert_date_timeago($current);
*/


?>
