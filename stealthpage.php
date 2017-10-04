<?php
error_reporting(E_ERROR | E_PARSE);
include_once("php_includes/db_conx.php");
if(isset($_POST["email"])){
    $email = $_POST['email'];
    mail("martin@tribesay.com", "TRIBESAY USER", "user email is: ".$email);
    //ad user email to mailing list
    $sql = "INSERT INTO mailing_list (email, date, status) VALUES('$email', now(), 'signed_up')";
    $query = mysqli_query($db_conx, $sql);
    
    //send the person an email
    require("libs/sendgrid-php/sendgrid-php.php");
    include_once("email_tem/no_mobile.php");

    $sendgrid = new SendGrid('TribeSay', 'shitsocial8');

    $mail = new SendGrid\Email();
    $mail->addTo($email)->
            setFrom('olivia@tribesay.com')->
            setSubject('Welcome to TribeSay')->
            setText('Check us out on your laptop/desktop! We\'ll be on mobile soon.')->
            setHtml(no_mobile_html());

    $sendgrid->send($mail);
    echo "success";
    exit();
}
?>

<!DOCTYPE HTML>

<head>

    <title>Welcome to TribeSay</title>

    <?php include_once("standardhead.php") ?>
    
    
    <link rel="stylesheet" href="style/stealthpage.css" type="text/css" media="screen">
    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/new-modal.js"></script>
    <script>
            function detectmob() {
        if (navigator.userAgent.match(/Android/i)
                || navigator.userAgent.match(/webOS/i)
                || navigator.userAgent.match(/iPhone/i)
                || navigator.userAgent.match(/iPad/i)
                || navigator.userAgent.match(/iPod/i)
                || navigator.userAgent.match(/BlackBerry/i)
                || navigator.userAgent.match(/Windows Phone/i)
                ) {
            return true;
        }
            else {
                return false;
            }
        }

        var browser = BrowserDetect.browser;
        if (browser !== "Chrome") {
        //location.href = "useChrome.php";
        }

        //var check_mobile = detectmob();
        var check_mobile = true;
            function contactAdmin(e) {
                e.preventDefault();
                var email = document.getElementById("email").value;       
        if(email.toLowerCase() === "treasure"){
            window.location = frenetic.root+'/index.php?s';
        }
        
        if (email === "") {
                    alert("Please fill out all of the form data.")
                    return;
                }
                var ajax = new ajaxObj("POST", "stealthpage.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {
                        if (ajax.responseText === "success") {
                            if(check_mobile === true){
                                alert('We\'ve sent you an email, see you soon on the website!\n\n- Martin, JP, and Olivia');
                            }else{
                                openModal('signup_success');
                                $('#email').val("");
                            }
                            //alert("Thanks for your interest! We'll get back to you in about one business day!");
                            //olivia - whatever message you want to show up on 
                            //the stealth page after successful registration, show it here

                                }
                                else {
                                    alert("There was an error sending you message, please try again.")
                        }
                    }
                }
                ajax.send("email=" + email);
            }
            
            function access_code(){
                var status = $('#access_code').attr('status');        
                if(status === 'closed'){
                    $('#access_code').css('display', 'block').attr('status','open').animate({'right': 0,'opacity': 1}, 750);
                }else{
                    $('#access_code').attr('status','closed').animate({'right': -200,'opacity': 0},750,function(){$(this).css('display', 'none')});
                }
                
            }
            
            function check_access_code(){
                var ajax = new ajaxObj("POST", "php_parsers/validate_code.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {
                         if (ajax.responseText === "success") {
                            window.location = frenetic.root+'/index.php?s';
                        }
                        else {
                            alert("This access code is not valid.");
                        }
                    }
                }
                ajax.send("access_code=" + $('#access_code input').val());
            }
    </script>

</head>

<body>
    

    <?php include_once("analyticstracking.php") ?>
    <div id="modalBackground" class="bt" uid="blank"></div>
    <img id="earth" src="sourceImagery/earth.png">

    <div class='stealth logo'></div>
    <div class='stealth copy'>What does the <span class='braintribe'><b>tribe</b>say</span>?<br><span class="coming_soon">Coming soon to mobile. <br>Check us out on your computer!</span></div>
    <div class='stealth signup_container'>
        <div class='stealth signup button'>
            <input id="email" type='email' class='stealth email' placeholder='you@me.you' trigger='.stealth.signup_button' autofocus><div onclick="contactAdmin(event);" class='stealth signup_button'>Find Out</div>
        </div>
        <div class='stealth login'><span class='button' data-link='index.php?login'>Log in</span> or sign up with your <span class="button" onclick='access_code();'>access code</span>.
            <form id="access_code" status='closed'><input type="text" placeholder="access code" style="text-transform:uppercase" trigger='.access_code_btn'><div class="stealth access_code_btn button" onclick='check_access_code();'>Get in here!</div></form></div>

    </div>
    <script>
    if (check_mobile === true) {
        $('*').addClass('mobile');
            }
    </script>
</body>
</html>
