<?php
$testArray = array (array("poop","poop"),array("poop","poop"),array("RINO","RINO","poop"),"RINO", "MAJOR TOM");
$arrayValues = array_count_values($testArray);

print_r($testArray);
echo "<br>";
print_r($arrayValues);
?>
