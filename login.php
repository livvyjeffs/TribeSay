<?php
error_reporting(E_ERROR | E_PARSE);
include_once("php_includes/check_login_status.php");
//if user is already loggin in, header that weekis away
if (isset($_SESSION["username"])) {
    //header("location: index.php?p=" . $SESSION["username"]);
    //exit();
}
?><?php
//AJAX calls this login code to execute
if (isset($_POST["e"])) {
    //connect to the database
    include_once("php_includes/db_conx.php");
    //gather the posted data into local variables and sanitize
    $e = mysqli_real_escape_string($db_conx, $_POST['e']);
    //get salt form db
    $p = $_POST['p'];
    //get user ip address
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
    //form data error handling
    if ($e === "" || $p === "") {
        echo "Please complete all fields to login.";
        exit();
    } else {
        //end form data error handling
        $sql = "SELECT id, username, password, salt FROM users WHERE (email='$e' AND activated='1') OR (username='$e' AND activated='1') LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $num_row = mysqli_num_rows($query);
        if($num_row < 1){
            echo "We did not find that username or email in our database.";
            exit();
        }
        $row = mysqli_fetch_row($query);
        $db_id = $row[0];
        $db_username = $row[1];
        $db_pass_str = $row[2];
        $db_salt = $row[3];
        if($db_pass_str === "facebook"){
            echo "click face_book login button to login via facebook";
            exit();
        }elseif($db_salt === "reset"){
            echo "reset_pass";
            exit();
        }      
        $p = hash("sha512", $db_salt.$p);
        if ($p !== $db_pass_str) {
            //echo "salt: ".$db_salt." P: ".$p;
            echo "Incorrect Password";
            exit();
        } else {
            //Create their sessions and cookies
            session_start();
            $_SESSION['userid'] = $db_id;
            $_SESSION['username'] = $db_username;
            
            //Olivia added
            $log_username = $_SESSION['username'];
            
            
            
            $_SESSION['password'] = $db_pass_str;
            setcookie("id", $db_id, strtotime('+30 days'), "/", "", "", TRUE);
            setcookie("user", $db_username, strtotime('+30 days'), "/", "", "", TRUE);
            setcookie("pass", $db_pass_str, strtotime('+30 days'), "/", "", "", TRUE);
            //update their "ip" and "lastlogin" fields
            $sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='db_username' LIMIT1";
            $query = mysqli_query($db_conx, $sql);
            echo "success";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="sourceImagery/dot_icon.ico" >
        <title>My Web Page</title>
        <link rel="stylesheet" href="style/relative.css"/> 

        <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script>
            function emptyElement(x) {
                _(x).innerHTML = "";
            }           
        </script>
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php include_once("template_pageTop.php"); ?>
    </div>
    <div id="main">
        <div id="main">
            <h3>Log In to TribeSay</h3>
            <!-- LOGIN FORM -->
            <form id="loginform" onsubmit="return false;">
                <div>Email Address or Username:</div>
                <input type="text" id="email" onfocus="emptyElement('status');"
                       maxlength="88">
                <div>Password:</div>
                <input type="password" id="password" onfocus="emptyElement('status');"
                       maxlength="16">
                <br /><br />
                <button id="loginbtn" onclick="login();">Log In</button>
                <p id="status"></p>
                <a href="forgotPassword.php">Forgot your password?</a>
            </form>
        </div>
    </div>
    <?php include_once("template_pageBottom.php"); ?>
</body>
</html>