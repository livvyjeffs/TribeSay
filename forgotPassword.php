<?php
include_once("php_includes/check_login_status.php");
include_once("email_tem/forgot_pw.php");
require("libs/sendgrid-php/sendgrid-php.php");
//if user is already logged in, header that weenis away
if ($user_ok === true) {
    header("location: index.php");
    exit();
}
?><?php
//AJAX calls this login code to execute
if (isset($_POST["e"])) {
    $e = $_POST["e"];
    $sql = "SELECT id, username, password FROM users WHERE email='$e' AND activated='1' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $numrows = mysqli_num_rows($query);
    if ($numrows > 0) {
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $id = $row["id"];
            $u = $row["username"];
            if($row["password"] === "facebook"){
                echo "facebook users must change password via facebook";
                exit();
            }
        }
        $emailcut = substr($e, 0, 4);
        $randNum = rand(10000, 99999);
        $tempPass = "$emailcut$randNum";
        $hashTempPass = $tempPass;    
        
        $sql = "UPDATE useroptions SET temp_pass='$hashTempPass' WHERE username='$u' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        //check that query succeeded
        $sql = "SELECT * FROM useroptions WHERE temp_pass='$hashTempPass' AND username='$u'";
        $query = mysqli_query($db_conx, $sql);
        $num_row = mysqli_num_rows($query);
        if ($num_row > 0) {
            $to = "$e";
            $from = "support@tribesay.com";
            $headers = "From: $from\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $subject = "TribeSay Temporary Password";
            
            $reset_link .= 'http://www.tribesay.com/forgotPassword.php?u=' . $u . '&p=' . $hashTempPass;
            $msg = fogot_pass_html($u, $reset_link, $tempPass);

            $sendgrid = new SendGrid('TribeSay', 'shitsocial8');

            $mail = new SendGrid\Email();
            $mail->addTo($to)->
                    setFrom($from)->
                    setSubject($subject)->
                    addHeader('MIME-Version', '1.0')->
                    addHeader('Content-type', 'text/html; charset=iso-8859-1')->
                    setText('You temporary password is: ' . $tempPass . ". Please go here to activate it: http://www.tribesay.com/forgotPassword.php?u=" . $u . "&p=" . $hashTempPass)->
                    setHtml($msg);

            if ($sendgrid->send($mail)) {
                echo "success";
                exit();
            } else {
                echo "email_send_failed";
                exit();
            }
        }else{
            echo "failed to update db with temp pass";
            exit();
        }
    } else {
        echo $e . " does not exist in our system";
    }
    exit();
}
?><?php
//email link click calls this code to execute
if (isset($_GET['u']) && isset($_GET['p'])) {
    $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
    $temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
    if (strlen($temppasshash) < 5) {
        exit();
    }
    
    $sql = "SELECT id, username FROM useroptions WHERE temp_pass='$temppasshash' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $num_row = mysqli_num_rows($query);
    if($num_row < 1){
        header("location: message.php?msg=There is no match for that username");
        exit();
    }
    
    $db_salt = mcrypt_create_iv(16, MCRYPT_DEV_RANDOM);
    $db_pass_str = hash("sha512", $db_salt.$temppasshash);
    
    $row = mysqli_fetch_row($query);
    $db_id = $row[0];
    $db_username = $row[1];
    
    $sql = "UPDATE users SET password='$db_pass_str', salt='$db_salt' WHERE username='$db_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    
    //check that update was successful
    $sql = "SELECT * FROM users WHERE password='$db_pass_str' AND salt='$db_salt' AND username='$db_username'";
    $query = mysqli_query($db_conx, $sql);
    $num_row = mysqli_num_rows($query);
    if($num_row > 0){
    
    $sql = "UPDATE useroptions SET temp_pass='', salt='' WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    
    $_SESSION['userid'] = $db_id;
    $_SESSION['username'] = $db_username;
    $_SESSION['password'] = $db_pass_str;
    setcookie("id", $db_id, strtotime('+30 days'), "/", "", "", TRUE);
    setcookie("user", $db_username, strtotime('+30 days'), "/", "", "", TRUE);
    setcookie("pass", $db_pass_str, strtotime('+30 days'), "/", "", "", TRUE);
    
    header("location: settings.php?reset");
    exit();
    }else{
        header("location: message.php?msg=Failed to activate temporary password. Please wait 30 second then try again.");
        exit();
    }   
}
?><?php 
$message_txt = "Oops... forgot my password";
if(isset($_GET["reset"])){
    $message_txt = "We've updated our security. Please reset your password.";
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TribeSay | Forgot Password</title>
        
         <?php include_once("standardhead.php") ?>            
        
        <link rel="stylesheet" href="<?php echo $root; ?>/style/modal.css?version=<?php echo $version_variable; ?>"/> 
        <link rel="stylesheet" href="<?php echo $root; ?>/style/normalize.css?version=<?php echo $version_variable; ?>"/>
        <link rel="stylesheet" href="<?php echo $root; ?>/style/textsize.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
        <link rel="stylesheet" href="<?php echo $root; ?>/style/forgot_password.css?version=<?php echo $version_variable; ?>"/> 
        <script src="<?php echo $root; ?>/js/main.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/ajax.js?version=<?php echo $version_variable; ?>"></script>
        <script>
            function sendPassword() {
                var e = $('#email').val();
                if (e === "") {
                    $('.status').text("Type in your email address");
                } else {
                    $('.status').text("Please wait...");
                    var ajax = ajaxObj("POST", frenetic.root + "/forgotPassword.php");
                    ajax.onreadystatechange = function() {
                        if (ajaxReturn(ajax) === true) {
                            var response = ajax.responseText;
                            if (response === "success") {
                                $('.status').html("SWEET! Check your email in a few minutes.<br><br>Look in your spam folders too!<br><br>See you soon!");
                                $('#email').prop('disabled', true);
                                $('.label').remove();
                                $('#sendPasswordBtn').css('display','none');
                            } else if (response === "no_exist") {
                                 $('.status').text("Email is not in our system.");
                            } else if (response === "email_send_failed") {
                                 $('.status').text("Sorry, mailing failed. Please email support@tribesay.com");
                            } else {
                                 $('.status').text(ajax.responseText);
                            }
                        } 
                    };
                    ajax.send("e=" + e);
                }
            }
        </script>
    </head>
    <body>

        <div class="modalBackground" style="display: block">
            <div id="modal_forgot_password" class="modal_container">
                <span class="header_text"><?php echo $message_txt; ?></span>
                <form name="forgotpasswordform" id="forgotPasswordForm" onsubmit="return false;">
                   <input type="text" id="email" maxlength='88' placeholder="you@me.you" trigger="#sendPasswordBtn">
                    <div class="label">Enter your Email Address &#10548;</div>
                </form>
                <div class="bt signup_buttons">
                    <div onclick="sendPassword();" id="sendPasswordBtn" class="button">Send me my password.</div>
                    <div class="status"></div>
                </div>
            </div>

        </div>
        <?php include_once("template_pageBottom.php"); ?>
    </body>
</html>