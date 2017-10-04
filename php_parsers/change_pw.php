<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
if(isset($_POST["current_pass"])){
    
    $current_pass = $_POST["current_pass"];
    
    $new_pass1 = $_POST["new_pass1"];
    $new_pass2 = $_POST["new_pass2"];
    if($new_pass1 !== $new_pass2){
        echo "New passwords do not match";
        exit();
    }
    
    $sql = "SELECT salt, password FROM users WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $old_salt = $row[0];
    $old_pw = $row[1];
    if($old_pw === "facebook"){
        echo "facebook users must change pw via facebook.";
        exit();
    }
    $current_pass = hash("sha512", $old_salt.$current_pass);
    
    //check that pass matched
    $sql = "SELECT * FROM users WHERE username='$log_username' AND password='$current_pass' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $num_rows = mysqli_num_rows($query);
    if($num_rows > 0){
        
        $salt = mcrypt_create_iv(16, MCRYPT_DEV_RANDOM);
        $new_pass1 = hash("sha512", $salt.$new_pass1);
        
        $sql = "UPDATE users SET password='$new_pass1', salt='$salt' WHERE username='$log_username' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        //check that db change was made
        $sql = "SELECT * FROM users WHERE password='$new_pass1' AND salt='$salt'";
        $query = mysqli_query($db_conx, $sql);
        $num_rows = mysqli_num_rows($query);
        if ($num_rows > 0) {
            //update their login so that they arent auto logged out.
            $_SESSION['password'] = $new_pass1;
            setcookie("pass", $new_pass1, strtotime('+30 days'), "/", "", "", TRUE);
            echo "success";           
        }else{
            echo "failure";           
        }
        exit();
    }else{
        echo "Your old password is incorrect.";
        //give user link to forgot_pass.php
        exit();
    }
}
?>