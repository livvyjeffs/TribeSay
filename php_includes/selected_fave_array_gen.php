<?php
//generate available selected favs array
$sql = "SELECT * FROM selectedfavorites WHERE user='$log_username'";
$query = mysqli_query($db_conx, $sql);
$selectedFavsArray = array();
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $tagName = $row['tagname'];
    array_push($selectedFavsArray, $tagName);
}
?>
