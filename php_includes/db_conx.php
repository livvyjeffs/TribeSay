<?php
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *');
error_reporting(E_ERROR | E_PARSE);
$db_conx = mysqli_connect("localhost", "martianmartin147", "sunny", "social");

//setup object oriented connection for prepared statements
//remove echo statement after debugging
$mysqli = new mysqli("localhost", "martianmartin147", "sunny", "social");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
//$db_conx = mysqli_connect('ts-db.cpwd3jqvfg1o.us-east-1.rds.amazonaws.com', 'root', 'sunny123', 'social', 3306);

//Evaluate the connection
if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
}
?>