  //expose login variables
  var first_name, last_name, email, id, gender, location;
  window.fbAsyncInit = function() {
  FB.init({
    appId      : 276160959215644,
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });
  FB.Event.subscribe('auth.authResponseChange', function(response) {
    // Here we specify what we do with the response anytime this event occurs. 
    if (response.status === 'connected') {
        //alert(response.status);
      // user is connected to FB and my BT, direct them to logged in experience
      //by automatically collecting and submitting FB login data
      
      //go through our native signup process if not signup up already
      
    } else if (response.status === 'not_authorized') {
        //alert(response.status);
        // In this case, the person is logged into Facebook, but not into the app, so we call
        // show button which calls FB.login() rather than just throwing popup (
        //which will likely be blocked)
     } else {
        //alert(response.status);
        // In this case, the person is not logged into Facebook.
    }
  });
  };
  /*// Load the SDK asynchronously
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));*/
  // Here we run a very simple test of the Graph API after login is successful. 
  // This testAPI() function is only called in those cases. 
  function testAPI() {
    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      //console.log('Good to see you, ' + response.name + '.');
    });
}
function fb_login() {
    
    
    //mobile hack
    //reset_page('livvyjeffs');
    //return
    
    FB.login(function(response) {
        if (response.authResponse) {
            FB.api('/me', function(response) {
                email = response.email;
                id = response.id;
                //check if this email is already registered
                var ajax = ajaxObj("POST", frenetic.root + "/php_parsers/check_fb_user.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {
                        var r = ajax.responseText.split(',');
                        if (r[0] === "sign_up") {
                            //run signup ajax
                            var u = prompt("enter desired username");
                            var ajax_s = ajaxObj("POST", "signup.php");
                            ajax_s.onreadystatechange = function() {
                                if (ajaxReturn(ajax_s) === true) {
                                    if(ajax_s.responseText === "t_signup_success"){
                                        reset_page(u);
                                    }else{
                                        alert(ajax_s.responseText);
                                    }
                                }
                            };
                            ajax_s.send("id="+id+"&u="+u+"&e="+email+"&p="+"facebook"+"&fb_signup=yes");
                        } else if (r[0] === "login") {
                            reset_page(r[1]);
                        }else{
                            alert(r[0]);
                        }
                    }
                };
                ajax.send("id=" + id);
            });
        } else {
            // The person cancelled the login dialog
            alert("authentication failed");
        }
    }, {scope: 'email'});
  }