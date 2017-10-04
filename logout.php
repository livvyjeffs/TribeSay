<?php
session_start();
//set session to an empty array
$_SESSION = array();
//expire their cookie files
if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"])){
    setcookie("id", '', strtotime( '-5 days' ), '/');
    setcookie("user", '', strtotime( '-5 days' ), '/');
    setcookie("pass", '', strtotime( '-5 days' ), '/');
}
//destroy the session variables
session_destroy();
//double check to see if their sessions exists
if(isset($_SESSION['username'])){
    header("location: message.php?msg=Error:_Logout_Failed");
} else {
    header("location: index.php");
    exit();
}
?>
