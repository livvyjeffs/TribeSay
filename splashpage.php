<?php
if(isset($_POST["email"])){
    $email = $_POST['email'];
    mail("martinmolina147@gmail.com", "BRAINTRIBE USER", "user email is: ".$email);
    echo "success";
    exit();
}
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--<html class="no-js"><!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
        <title>Welcome to braintribe</title>
        <link rel="shortcut icon" href="sourceImagery/dot_icon.ico" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="style/normalize.css" type="text/css" media="screen">
        <link rel="stylesheet" href="style/grid.css" type="text/css" media="screen">
        <link rel="stylesheet" href="style/splashpage.css" type="text/css" media="screen">
        <script type="text/javascript" src="js/ajax.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
        <!-- <link rel="stylesheet" href="css/style.min.css" type="text/css" media="screen"> -->
        <!--[if IE]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <script>
            function contactAdmin(e) {
                e.preventDefault();
                var name = document.getElementById("name").value;
                var email = document.getElementById("email").value;
                var message = document.getElementById("message").value;
                if (name === "" || email === "" || message === "") {
                    alert("Please fill out all of the form data.")
                    return;
                }
                var ajax = new ajaxObj("POST", "php_includes/mailAdmin.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {
                        if (ajax.responseText === "success") {
                            alert("Thanks for your interest! We'll get back to you in about one business day!");
                            document.getElementById("name").value = "";
                            document.getElementById("email").value = "";
                            document.getElementById("message").value = "";
                        }
                        else {
                            alert("There was an error sending you message, please try again.")
                        }
                    }
                }
                ajax.send("name=" + name + "&email=" + email + "&message=" + message);
            }
        </script>
        
        

    </head>

    <body>

        <?php include_once("analyticstracking.php") ?>

        <div class="slide" id="slide0" data-slide="0" data-stellar-background-ratio="0.5">
            <div id="header">
                 <a href="index.php"><img class="logo" src="sourceImagery/newlogo_1.png" alt="logo" title="trialLogo"></a>
                <div id="you_are_here"></div>
                <form id="demo_login">
                    <span style="color: white; font-size: 1.2em">Demo Login</span>
                    <input id="username" placeholder="username">
                    <input id="password" placeholder="password" type="password">
                    <div onclick="login();" id="demo-login-button" style="background: url(sourceImagery/navigation/login_button.png); width: 30px; height: 25px; display: inline-block; vertical-align: middle"></div>
                </form>    
            </div>
            <div class="container clearfix"></div>
        </div>
        
       

        <div class="slide" id="slide1" data-slide="1" data-stellar-background-ratio="0.5">
            <div class="container clearfix">
                <div class="grid_12 omega"><p class="large_copy">Get nerdy on <span style="font-size: 1.25em; vertical-align: -0.05em; font-family: 'Bauhaus', 'aaarghnormal'; color: orange">braintribe</span>.</p></div>
                <div class="grid_12 omega" type="container">
                    <div class="grid_8 splash_slideshow">
                        <section class="slider">

                            <nav class="slide-nav">
                                <ul>
                                    <li><a href="#">01</a></li>
                                    <li><a href="#">02</a></li>
                                    <li><a href="#">03</a></li>
                                    <li><a href="#">04</a></li>
                                    <li><a href="#">05</a></li>
                                    <li><a href="#">06</a></li>
                                </ul>
                            </nav>

                            <ul class="slides">
                                <li><img src="http://kylefoster.me/cp/Up01.jpg" alt="Slide One"></li>    
                                <li><img src="http://kylefoster.me/cp/Up02.jpg" alt="Slide Two"></li>
                                <li><img src="http://kylefoster.me/cp/Up03.jpg" alt="Slide Three"></li>
                                <li><img src="http://kylefoster.me/cp/Up04.jpg" alt="Slide Four"></li>
                                <li><img src="http://kylefoster.me/cp/Up05.jpg" alt="Slide Five"></li>
                                <li><img src="http://kylefoster.me/cp/Up06.jpg" alt="Slide Five"></li>
                            </ul> 

                        </section>
                    </div>
                    <div class="grid_4 omega action_page">
                        <p>
                            Thanks go out to our 285 beta testers for their wonderful feedback and participation. <br></br> We are in<b>code</b>porating your feedback for a better braintribe!
                        </p>
                        <br></br>
                        <div class='grid_12 omega' id='mailing_list'>
                            <p style="text-align: center">Get first access to <span style="font-family: 'Bauhaus','aaarghnormal'; color: white; font-size: 1.2em;">braintribe</span></p>
                            <form>
                                <div class="grid_9"><input placeholder='email address' onkeypress="check_if_enter(event);"></div>
                                <div class="grid_2 omega" id='mailing_list_submit' onclick="changeWelcomeText();"><p>>></p></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="slide" id="slide2" data-slide="2" data-stellar-background-ratio="0.5">
            <div class="container clearfix">

                <div id="our_process" class="grid_12">
                    <h1>What do you get by joining?</h1>
                </div>
                <div id="content" class="grid_12 omega">
                  
                        <p>Get the most recent news about things you love.</p>
                        <p>Find people who love what you love.</p>
                        <p>Go to events and share your interest with the people!</p>
                   
                </div>


            </div>
        </div>



        <div class="slide" id="slide3" data-slide="3" data-stellar-background-ratio="0.5">
            <div class="container clearfix">

                <div id="content" class="grid_12 omega">
                    <h1>Does it cost anything?</h1>
                </div>
                <div id="content" class="grid_12 omega">
                    <p>No, the site is completely free to use!</p>
                </div>

            </div>

        </div>

        <div class="slide" id="slide4" data-slide="4" data-stellar-background-ratio="0.5">
            <div class="container clearfix">

                <div id="content" class="grid_12"><h1>About the Team</h1></div>

                <div class="grid_4">
                    <img src="sourceImagery/martin.png">
                    <h2>Martin Molina</h2>
                    <p>CEO and Back-end</p>
                </div>
                <div class="grid_4">
                    <img src="sourceImagery/olivia.png">
                    <h2>Olivia Jeffers</h2>
                    <p>COO and Front-end</p>
                </div>
                <div class="grid_4 omega"><img src="sourceImagery/jp.png">
                    <h2>Jean-Philippe Lefebvre</h2>
                    <p>CMO and BizDev</p>
                </div>
            </div>
        </div>
   




    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.stellar.min.js"></script>
    <script type="text/javascript" src="js/waypoints.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>


    <!--timing function, all the same-->

    <script src="js/slider.js"></script>

</body>
</html>
