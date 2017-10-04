<?php
include_once("../php_includes/check_login_status.php");
if(isset($_POST["username"])){
    $u = $_POST["username"];
    $sql = "SELECT avatar,ratio,username FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $avatar = $row[0];
    $ratio = $row[1];
    $u = $row[2];
    if($avatar === null){
        $path = $root."/sourceImagery/default_avatar.png";
    }else{
        $path = $s3root."/user/".$u."/".$avatar;
    }
    echo json_encode(array("path"=>$path,"ratio"=>$ratio));
    exit();
}
?>
