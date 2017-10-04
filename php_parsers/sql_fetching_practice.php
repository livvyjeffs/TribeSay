<?php
include_once("./php_includes/check_login_status.php");
//print confirmation
echo "Ping Check: ".mysqli_ping($db_conx);
//query database
$sql = "SELECT * FROM users WHERE username='$log_username' LIMIT 1";
$query = mysqli_query($db_conx, $sql);
//trial variation of query and print
$row1 = mysqli_fetch_assoc($query);
$row2 = mysqli_fetch_array($query);
$row3 = mysqli_fetch_field($query);
$row4 = mysqli_fetch_field_direct($query);
$row5 = mysqli_fetch_fields($query);
$row6 = mysqli_fetch_object($query);
$row7 = mysqli_fetch_row($query);
echo "1";
print_r($row1);
echo "2";
print_r($row2);
echo "3";
print_r($row3);
echo "4";
print_r($row4);
echo "5";
print_r($row5);
echo "6";
print_r($row6);
echo "7";
print_r($row7);
?>
