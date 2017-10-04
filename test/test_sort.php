<?php
$date = "July 21, 2014, 11:09 pm";
$new_date = date_create($date);
echo date_format($new_date, 'Y-m-d H:i:s');
?>
