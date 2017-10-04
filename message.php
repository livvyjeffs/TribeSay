<?php
$message = "";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if($msg === "activation_failure"){
    $message = '<h2>Activation Error</h2>Sorry there was an issue activating 
                your account...';
} else if($msg == "activation_success"){
    $message = '<h2>Activation Success</h2> Your account is now activated.
                <a href="login.php">Click here to login</a>';
} else {
    $message = $msg;
}
?>
<div><?php echo $message; ?></div>
