<?php
$db_conx = mysqli_connect("localhost", "martianmartin147", "sunny", "test_impo");
//Evaluate the connection
if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
}
$sql = "SELECT * FROM users WHERE id > 31";
$query = mysqli_query($db_conx, $sql);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    echo $row["email"];
    echo "<br>";
}
?>
