<?php
function auth_user($username, $id_token, $connection){
    $sql = "SELECT id_hash FROM app_clients WHERE username='$username' LIMIT 1";
    $query = mysqli_query($connection, $sql);
    $row = mysqli_fetch_row($query);
    $id_hash = $row[0];
    if($id_token === $id_hash){
        return true;
    }else{
        return false;
    }
}
/*
//GOES AT TOP OF STREAM GENERATOR TO ADAPT IT...
error_reporting(E_ERROR | E_PARSE);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
include_once("../app_parsers/auth_functions.php");
if($_SERVER['HTTP_REFERER'] === $_SERVER["HTTP_HOST"]){
    //dont exit
    include_once("check_login_status.php");
}elseif(isset($_POST["username"]) && isset($_POST["id_token"])){
    if(auth_user($_POST["username"], $_POST["id_token"], $db_conx)){
        //dont exit      
        //set session variables
        
    }else{
        //exit
    }
}else{
    //exit
}

