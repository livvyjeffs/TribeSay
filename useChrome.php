<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TribeSay Beta</title>
        <link rel="stylesheet" href="style/usechrome.css"/> 
        <link href='http://fonts.googleapis.com/css?family=Play:400,700' rel='stylesheet' type='text/css'>
        <script src="js/ajax.js"></script>
        <?php include_once("standardhead.php") ?> 

    </head>
    <body>
        <div class="copy_head row">Welcome to <span class="braintribe"><b>tribe</b>say</span></div>
        <div class="row">We are currently in beta and only operate on Desktop Chrome.</div>
        <div class="half row"><div id='email'><input type="email" trigger='#email_button' placeholder='youremail@you.com' autofocus><div class='button' id='email_button' onclick='submit_email();'>Get notified when we're on your platform.</div></div><div class='ordiv'>or</div></div>
        <div class="half row"><a target='_blank' href='http://www.google.com/chrome'><img src='sourceImagery/download_chrome.png'></a></div>
        
        
    </body>
    
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
                        return 'mobile';
                    }
                    else {
                        return 'not_mobile';
                    }
                }
                
        
            
            $(document).ready(function(){
               $('[trigger]').keyup(function(event){
                  if(event.keyCode === 13){
                      $($(this).attr('trigger')).trigger('click');
                  }
               }); 
            });
            
            function submit_email(){
                var email = $('#email input').val();
                if(email === ""){
                    alert("please enter you email");
                    return;
                }
                var browser = BrowserDetect.browser;
                var mobile = detectmob(); //'mobile' or 'not_mobile'
                
                var ajax = ajaxObj("POST", "php_parsers/contact_admin.php");
                ajax.onreadystatechange = function(){
                    if(ajaxReturn(ajax) === true){
                        alert(ajax.responseText);
                        $('#email input').val('');
                    }
                };
                ajax.send("email="+email+"&browser="+browser+"&mobile="+mobile);
            }
        </script>
        
</html>