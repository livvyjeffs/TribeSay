<?php

$db_conx = mysqli_connect("localhost", "martianmartin147", "sunny", "test_impo");
/*
echo $salt = openssl_random_pseudo_bytes(64, $crypto_strong);

echo "<br>";

echo $crypto_strong;

echo "<br>";

echo $pass = hash("sha512", $salt."sisdfasdfadq3245u2347yrw937ire");

echo "<br>";echo "<br>";

$sql = "UPDATE users SET salt='$salt', password='$pass' WHERE username='martin' LIMIT 1";
echo $query = mysqli_query($db_conx, $sql);
*/
$sql = "SELECT * FROM users WHERE username='martin' LIMIT 1";
$query = mysqli_query($db_conx, $sql);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    echo $row["username"];
    echo "<br>";
    echo $salt = $row["salt"];
    echo "<br>";
    echo $pass = $row["password"];
    echo "<br>";
}

if($pass === hash("sha512", $salt."sisdfasdfadq3245u2347yrw937ire")){
    echo "pass matched";
}else{
    echo "NO match";
}
?>
