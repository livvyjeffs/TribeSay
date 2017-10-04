<?php
error_reporting(E_ERROR | E_PARSE);
include_once('../php_includes/db_conx.php');
if(isset($_POST["email"])){
    $email = $_POST["email"];
    $status = $_POST["status"];
    $sql = "INSERT INTO mailing_list (email, date, status) VALUES('$email', now(), '$status')";
    $query = mysqli_query($db_conx, $sql);
    echo "success";
    exit();
}
?>
