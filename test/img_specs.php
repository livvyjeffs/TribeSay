<?php
$img = "http://www.wired.com/wp-content/uploads/2014/04/amazon-settop-inline.jpg";
$img = imagecreatefromjpeg($img);
echo $x = imagesx($img);
echo "<br>";
echo $y = imagesy($img);
echo "<br>";
echo $x / $y;
?>
