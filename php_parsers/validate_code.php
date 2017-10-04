<?php
if(isset($_POST["access_code"])){
    $code = strtolower($_POST["access_code"]);
    $correct = "treasure";
    if($code === $correct){
        echo "success";
    }else{
        echo "failure";
    }
    exit();
}
?>
