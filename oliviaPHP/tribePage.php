<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Tribe Page</title>
        <link rel="stylesheet" href="tribeStyle.css"/>   
        <script src="../js/main.js"></script>
    </head>
    <body>

        <?php include_once("header.php"); ?>

        <div id="main">


            <!--  <? //php include_once("profile_info.php");  ?>-->


            <?php include_once("tribePage_streams.php"); ?>


            <!-- <? //php include_once("friend_list.php");  ?>-->


        </div>

        <!--modal window-->

        <!--<div id="log_in" class="modalDialog">
            <div>
                <a href="#close" title="Close" class="close">X</a>
                <h2>Log In</h2>
                <form action="#">
                    <span class='log_in_form_text'>Username: </span><input type="text" name="uname"><br>
                    <span class='log_in_form_text'>Password: </span><input type="text" name="lname"><br>
                    <a href="#" class="button">Submit</a>
                </form>
                
            </div>
        </div>

        <!--modal window-->



    </body>

</html>


