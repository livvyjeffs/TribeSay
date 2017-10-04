<?php
error_reporting(E_ERROR | E_PARSE);
if(isset($_POST["email"])){
    $email = $_POST['email'];
    $browser = $_POST["browser"];
    $mobile = $_POST["mobile"];
    mail("martinmolina147@gmail.com", "TRIBESAY USER", "user email is: ".$email.$browser.$mobile);
    echo "thanks for signing up!";
    exit();
}
?>