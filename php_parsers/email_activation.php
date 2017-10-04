<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
if(isset($_POST["get_activation_state"])){
    $sql = "SELECT activated FROM users WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $state = mysqli_fetch_row($query);
    if($state[0] === "1"){
        echo "activated";
    }elseif($state[0] === "0"){
        echo "deactivated";
    }else{
        echo "activation state error";
    }
    exit();
}
if(isset($_POST["change_activation_state"])){
    if($_POST["change_activation_state"] === "activate"){
        $new_state = 1;
    }elseif($_POST["change_activation_state"] === "deactivate"){
        $new_state = 0;
    }else{
        echo "activation state unknown";
        exit();
    }
    $sql = "UPDATE users SET activated='$new_state' WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    echo 'success';
    exit();
}
?>
