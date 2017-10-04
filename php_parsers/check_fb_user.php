<?php
include_once("../php_includes/check_login_status.php");
error_reporting(E_ERROR | E_PARSE);
if(isset($_POST["id"])){
    $fid = $_POST["id"];
    $old_pw = md5($fid);
    $sql = "SELECT username, id, salt FROM users WHERE salt='$fid' OR password='$old_pw'";
    $query = mysqli_query($db_conx, $sql);
    $count = mysqli_num_rows($query);
    if($count > 0){
        $row = mysqli_fetch_row($query);
        $u = $row[0];
        $id = $row[1];
        $salt = $row[2];
        if($salt === "reset"){
            $sql = "UPDATE users SET salt='$fid', password='facebook' WHERE id='$id' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
        }
        //Create their sessions and cookies
        session_start();
        $_SESSION['userid'] = $id;
        $_SESSION['username'] = $u;
        $_SESSION['password'] = "facebook";//maybe check this against get auth response access token
        setcookie("id", $id, strtotime('+30 days'), "/", "", "", TRUE);
        setcookie("user", $u, strtotime('+30 days'), "/", "", "", TRUE);
        setcookie("pass", "facebook", strtotime('+30 days'), "/", "", "", TRUE);
        
        echo "login,".$u;
    }else{
        echo "sign_up";
    }
    exit();
}
?>
