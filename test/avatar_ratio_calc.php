<?php
/*
include_once("../php_includes/db_conx.php");
$sql = "SELECT avatar,username FROM users";
$query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($query)) {
    $a = $row[0];
    $u = $row[1];
    $s3root = "https://s3.amazonaws.com/TribeSay_images";

    if ($a === null) {
        $ratio = 1;
    } else {
        $path = $s3root . "/user/" . $u . "/" . $a;
        $img = imagecreatefromjpeg($path); //also detect type and and png and gif
        $h = imagesy($img);
        $w = imagesx($img);
        $ratio = $h / $w;
    }
    $sql2 = "UPDATE users SET ratio='$ratio' WHERE username='$u' LIMIT 1";
    $query2 = mysqli_query($db_conx, $sql2);
}
*/