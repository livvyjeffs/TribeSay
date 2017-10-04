<?php
include_once("../../php_includes/check_login_status.php");
//get status
$sql = "SELECT credit_confirmed, subscription_active, pin_count, post_count FROM classified_clients WHERE username='$log_username' LIMIT 1";
$query = mysqli_query($db_conx, $sql);
$row = mysqli_fetch_row($query);

$credit_confirmed = $row[0];
$subscription_active = $row[1];
//$pin_count = $row[2];
$post_count = $row[3];

$payment_form_style_free = '';

$regular_post_cost = 'collect';

if ($credit_confirmed === "yes") {    
    $regular_post_cost = "not_subscribed";   
    //check regular cost
    /*if ($post_count < 2 && $subscription_active === "yes") {
        $regular_post_cost = "free";
        $payment_form_style_free = "style='display: none'";
    } elseif ($subscription_active === "no") {
        $regular_post_cost = "not_subscribed";
    } else {
        $regular_post_cost = "three_dollar";
    }*/
} else {
    $regular_post_cost = "collect_payment_info";
}