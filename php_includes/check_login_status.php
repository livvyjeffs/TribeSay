<?php
session_start();
include_once("db_conx.php");
//files that include this file at the very top would NOT require connection to
//database or session_start(), be careful.
//initialize some vars
$user_ok = false;
$log_id = "";
$log_username = "";
$log_password = "";
//user verify function
function evalLoggedUser($conx,$id,$u,$p){
    $sql = "SELECT ip FROM users WHERE id='$id' AND username='$u' AND password='$p' AND activated='1' LIMIT 1";
    $query = mysqli_query($conx, $sql);
    $numrows = mysqli_num_rows($query);
    if($numrows > 0){
        return true;
    }
}
if(isset($_SESSION["userid"]) && isset($_SESSION["username"]) && isset($_SESSION["password"])){
    $log_id = preg_replace('#[^0-9]#', '', $_SESSION['userid']);
    $log_username = preg_replace('#[^a-z0-9]#i', '', $_SESSION['username']);
    $log_password = preg_replace('#[^a-z0-9]#i', '', $_SESSION['password']);
    //verify the user
    $user_ok = evalLoggedUser($db_conx,$log_id,$log_username,$log_password);
} else if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"])){
    $_SESSION['userid'] = preg_replace('#[^0-9]#', '', $_COOKIE['id']);
    $_SESSION['username'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['user']);
    $_SESSION['password'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['pass']);
    $log_id = $_SESSION['userid'];
    $log_username = $_SESSION['username'];
    $log_password = $_SESSION['password'];
    //verify the user
    $user_ok = evalLoggedUser($db_conx,$log_id,$log_username,$log_password);
    if($user_ok === true){
        //update their lastlogin datetime field
        $sql = "UPDATE users SET lastlogin=now() WHERE id='$log_id' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
    }
}
//get logged user email
function get_user_email($conx, $username) {
    $sql = "SELECT email FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($conx, $sql);
    $row = mysqli_fetch_row($query);
    return $row[0];
}

//define document root

$root = "http://" . $_SERVER["HTTP_HOST"];

if (isset($_SERVER["HTTPS"])) {
    $root = "https://" . $_SERVER["HTTP_HOST"];
}

$s3root = "https://s3.amazonaws.com/TribeSay_images";


if ($_SERVER["HTTP_HOST"] === 'localhost') {
    $version_variable = rand();
} else {
    $version_variable = '018';
    //$version_variable = rand();
}

?>
