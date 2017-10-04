<?php
error_reporting(E_ERROR | E_PARSE);
include_once("php_includes/check_login_status.php");
include_once("email_tem/welcome_msg.php");
require("libs/sendgrid-php/sendgrid-php.php");
//If user is logged in, header them away
if (isset($_SESSION["username"])) {
    header("location: message.php?msg=You are already logged in...");
    exit();
}
?><?php
//Ajax calls this NAME CHECK code to execute
if (isset($_POST["usernamecheck"])) {
    include_once("php_includes/db_conx.php");
    $username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
    $sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
        echo '<strong style="color: #F00;">3-16 characters please</strong>';
        exit();
    }
    if (is_numeric($username[0])) {
        echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
        exit();
    }
    if ($uname_check < 1) {
        echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
        exit();
    } else {
        echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
        exit();
    }
}
?><?php
//Ajax calls this Registration code to execute
if (isset($_POST["u"])) {
    //CONNECT TO THE DATABASE
    include_once("php_includes/db_conx.php");
    //GATHER THE POSTED DATA INTO LOCAL VARIABLES
    $u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
    $e = mysqli_real_escape_string($db_conx, $_POST['e']);
    $p = $_POST['p'];
    //GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
    //DUPLICATE DATACHECKS FOR USERNAME AND EMAIL
    $sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $u_check = mysqli_num_rows($query);
    //--------------------------------------------------------------------------
    $sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $e_check = mysqli_num_rows($query);
    // FORM DATA ERROR HANDLING
    if ($u == "" || $e == "" || $p == "") {
        echo "The form submission is missing values. Email is required.";
        exit();
    } elseif ($u_check > 0) {
        echo "The username you have entered is already taken";
        exit();
    } elseif ($e_check > 0 && !isset($_POST["fb_signup"])) {
        echo "The email that you have entered is already in our system";
        exit();
    } elseif (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters.";
        exit();
    } elseif (is_numeric($u[0])) {
        echo "Username cannot begin with a number";
        exit();
    } else {
        //END FORM DATA ERROT HANDLING
        //BEGIN INSTERION OF DATA INTO THE DATABASE
        //HASH THE WORD AND APPLY MY OWN UNIQUE SALT...
        
        //for ($i = 0; $i < 3; $i++) {
        if (isset($_POST["fb_signup"])) {
            $salt = $_POST["id"];
            $sql = "INSERT INTO users (username, email, password,
                    ip, signup, lastlogin, notescheck, activated, salt)
                    VALUES('$u', '$e', 'facebook', '$ip', now(), now(), now(), '1', '$salt')";
            $query = mysqli_query($db_conx, $sql);
            $uid = mysqli_insert_id($db_conx);
            $p_hash = "facebook";
        } else {
            //$salt = mcrypt_create_iv(16, MCRYPT_DEV_RANDOM);
            $salt = mcrypt_create_iv(16, MCRYPT_DEV_RANDOM);
            $p_hash = hash("sha512", $salt . $p);
            //ADD user info into the database table for the main site table
            $sql = "INSERT INTO users (username, email, password,
                    ip, signup, lastlogin, notescheck, activated, salt)
                    VALUES('$u', '$e', '$p_hash', '$ip', now(), now(), now(), '1', '$salt')";
            if (!($query = mysqli_query($db_conx, $sql))) {
                echo "Unable to sign you up, please try again.";
                exit();
            }
            $uid = mysqli_insert_id($db_conx);
        }
        //set session variables like ie LOGIN
        //Create their sessions and cookies
        session_start();
        $_SESSION['userid'] = $uid;
        $_SESSION['username'] = $u;
        $_SESSION['password'] = $p_hash; //maybe check this against get auth response access token
        setcookie("id", $uid, strtotime('+30 days'), "/", "", "", TRUE);
        setcookie("user", $u, strtotime('+30 days'), "/", "", "", TRUE);
        setcookie("pass", $p_hash, strtotime('+30 days'), "/", "", "", TRUE);
        //check that insertion succeeded 
        /* $sql = "SELECT * FROM users WHERE username='$u'";
          $query = mysqli_query($db_conx, $sql);
          $num_rows = mysqli_num_rows($query);
          if ($num_rows < 1) {
          if($i === 2){
          echo "We were unable to sign you up. Please try again or contact: martin@tribesay.com";
          exit();
          }
          //echo $i." ";
          }else{
          break;
          } */
        //}      
        //Establish their row in the useroptions table
        $sql = "INSERT INTO useroptions (id, username, background) 
                VALUES('$uid', '$u', 'original')";
        $query = mysqli_query($db_conx, $sql);
        /*//create default favorite tags
        $sql = "INSERT INTO userfavorites (user, tagname, dateadded)
                VALUES ('$u', 'business', now()),
                 ('$u', 'edm', now()),
                 ('$u', 'funny', now())";
        $query = mysqli_query($db_conx, $sql);*/
        //Create directory to hold each user's files(pics, MP3's, etc.)
        /*if (!file_exists("user/$u")) {
            mkdir("user/$u", 0755);
        }
        if (!file_exists("stream/$u")) {
            mkdir("stream/$u", 0755);
        }*/
        // Email the user their activation link
        //might wanna put in absolute links here

        /*

          $to = "$e";
          $from = "braintribe@braintribe.me";                                            //Change this once eveagora expires on godaddy
          $subject = 'Please confirm your email';
          $message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>
          braintribe message</title></head><body style="margin:0px; font-family:Tahoma,
          Geneva, sans-serif;"><div style="padding:10px; background:#333;
          font-size:24px; color:#ccc;"><a href="index.php">
          <img src="sourceImagery/braintribe_brain_logo_black.png"
          width="36" height="30" alt="braintribe.me" style="border:none;
          float:left;"></a>braintribe Account Activation</div><div style="padding:24px;
          font-size:17px;">Hello ' . $u . ',<br /><a href="braintri.be/activation.php?
          id=' . $uid . '&u=' . $u . '&e=' . $e . '&p=' . $p_hash . '">Click here to activate your
          account now</a><br /><br />Login after successful activation using your:
          <br />* E-mail Address: <b>' . $e . '</b></div></body></html>';
          $headers = "From: $from\n";

          $headers .= "Organization: braintribe\r\n";

          $headers .= "MIME-Version: 1.0\r\n";

          $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

          $headers .= "X-Priority: 3\r\n";

          $headers .= "X-Mailer: PHP" . phpversion() . "\r\n";

          mail($to, $subject, $message, $headers);
         * 

         */
        $msg = welcome_html($u);
        $sendgrid = new SendGrid('TribeSay', 'shitsocial8');
        $mail = new SendGrid\Email();
        $mail->addTo($e)->
               setFrom('support@tribesay.com')->
               setReplyTo('olivia@tribesay.com')->
               setFromName('TribeSay Support')->
               setSubject('Welcome To TribeSay')->
               setText('Welcome!')->
               setHtml($msg);
        
        $sendgrid->send($mail);
        //send alert email to admin
        mail("martin@tribesay.com", "USER SIGNUP", "email: ".$e);
        //add to db with status registered
        if(strpos($user_email,'@japes.com') === false){
            $sql = "INSERT INTO mailing_list (email, date, status) VALUES('$e', now(), 'registered')";
            $query = mysqli_query($db_conx, $sql);
        }
        echo "signup_success";
    }
    exit();
}
?>