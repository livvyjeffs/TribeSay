<?php
include_once("./php_includes/check_login_status.php");
$imageArray = array();
$sql = "SELECT * FROM imagelinks";
$query = mysqli_query($db_conx, $sql);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $date = $row['postdate'];
    $source = $row['source'];
    $new = array($date, $source);
    array_push($imageArray, $new);
}
print_r($imageArray);
?>
