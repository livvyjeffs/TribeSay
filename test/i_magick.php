<?php
//pull emails form old_social db
$db_conx = mysqli_connect("localhost", "martianmartin147", "sunny", "old_social");
$sql = "SELECT * FROM users";
$query = mysqli_query($db_conx, $sql);
$i = 0;
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $i++;
    echo $i . ") " . $row['email'] . "<br>";
}
?>
