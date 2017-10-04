<?php
include_once("php_includes/check_login_status.php");
include_once("php_includes/convert_date.php");
if ($user_ok !== true) {
    header("location: signup.php");
}
// Initialize any variables that the page might echo
$u = "";
$isOwner = false;
//this checks the url encoded variable and sets it for local use, otherwise if DNE then header away
if (isset($_GET["u"])) {
    $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();
}
if ($u === $log_username) {
    $isOwner = true;
    $highlight = "personal_content";
} else {
    $highlight = "none";
}
// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
// Now make sure that user exists in the db, if not give a message saying they don't
$numrows = mysqli_num_rows($user_query);
if ($numrows < 1) {
    echo "That user does not exist or is not yet activated, press back";
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="sourceImagery/dot_icon.ico" >
        <title>TribeSay</title>
        <link rel="stylesheet" href="style/relative.css"/>   
        <link rel="stylesheet" href="style/modal.css"/> 
        <script src="js/main.js"></script>
        <script src="js/toggle_functions.js"></script>
        <script src="js/ajax.js"></script>
        <script src="js/load_more_content.js"></script>
        <script src="js/vote_system.js"></script>
        <script src="js/modal.js"></script>
        <script src="js/masonry.js"></script>
        <script src="js/tags.js"></script>
        <script src="js/tribeTags.js"></script>
        <script src="js/dragndrop.js"></script>  
     
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <!--Modal Windows-->
        <div class="modalBackground" id="blankModal"></div>

        <?php include_once("template_pageTop.php"); ?>

        <div id="main" name="normal">


                <!--<?//php include_once("oliviaPHP/profile_info.php"); ?> -->

      
                <?php include_once("streams.php"); ?>

     
                <!--<?php include_once("oliviaPHP/friend_list.php"); ?> -->
  


        </div>

        <?php include_once("template_pageBottom.php"); ?>

    </body>
</html>
