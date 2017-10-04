<?php
echo "reformat: ".$today = str_replace("-","/",date_format(date_create(date('2014-07-22 18:19:43')), 'Y-m-d H:i:s'));
echo "<br><br>";
//Note - display dates as Wed. 5th, rather than April 5th? - Find examples
//Events From approach
//calendar approach
//simple list (eventBrite)

$midnight = "23:59:59";
//current datetime
$today = date_format(date_create(date('Y-m-d H:i:s')), 'Y-m-d H:i:s');

//midnight tonight
$mid_tonight = date_create(date('Y-m-d 23:59:59'));

//mid tomorrow
echo $mid_tomorrow = date_format(date_add(date_create(date('Y-m-d 23:57:59')), date_interval_create_from_date_string('1 day')),'Y-m-d H:i:s');echo "<br>";

//--this weekend--
//this Friday, noon
$noon_friday = date_create(date("Y-m-d 12:00:00", strtotime('next Friday')));
echo $date = date_format($noon_friday, 'Y-m-d H:i:s');echo "<br>";

//midnight sunday
echo "sunday: ".$sunday = date('Y-m-d H:i:s', strtotime('next Sunday', strtotime($date)));echo "<br>";
echo $dw = date( "w", strtotime($today));

//targeting criteria
/*$start = date_format(date_create(date('Y-m-d 6:00:00')),'Y-m-d H:i:s');//am This Morning
        $end = date_format(date_create(date("Y-m-d 23:59:59", strtotime('next Friday'))),'Y-m-d H:i:s');//Sunday Midnight
        $criteria = "(event_begin > '$start' && event_begin < '$end')";*/
/* if Friday, Weekend === tonight thru next sunday
 * if Saturday, weekend === tonight thru next sunday
 * if sunday, weekend === tonight
 */
if ($event_filter === "weekend") {//this weekend
    $today = date_format(date_create(date('Y-m-d H:i:s')), 'Y-m-d H:i:s');
    echo $dw = date( "w", strtotime($today));
    switch ($dw) {
        case 5://Friday
        case 6://Saturday
        case 7://Sunday
            echo $criteria = " (event_begin > now())";
            break;
        default:
            $start = date_format(date_create(date("Y-m-d 12:00:00", strtotime('next Friday'))), 'Y-m-d H:i:s');// Noon This Friday
            $end = date_format(date_create(date('Y-m-d 23:59:59', strtotime('next Sunday', strtotime($date)))),'Y-m-d H:i:s');//Sunday Midnight
            echo $criteria = "(event_begin > '$start' AND event_begin < '$end')";
            break;
    }
}

?>
