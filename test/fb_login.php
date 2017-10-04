<?php
?>
<!DOCTYPE html>
<html>
<head></head>
<body>
<div id="fb-root"></div>
<script>
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
        alert(response.status);
      // user is connected to FB and my BT, direct them to logged in experience
      //by automatically collecting and submitting FB login data
      get_basic_data();
    } else if (response.status === 'not_authorized') {
        alert(response.status);
        // In this case, the person is logged into Facebook, but not into the app, so we call
        // show button which calls FB.login() rather than just throwing popup (
        //which will likely be blocked)
     } else {
        alert(response.status);
        // In this case, the person is not logged into Facebook.
    }
  });
  };
  // Load the SDK asynchronously
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));
  // Here we run a very simple test of the Graph API after login is successful. 
  // This testAPI() function is only called in those cases. 
  function testAPI() {
    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      //console.log('Good to see you, ' + response.name + '.');
    });
  }
  function get_basic_data(){
      var uid = FB.getUserID();
      FB.api('/'+uid, {fields: 'first_name, last_name, email, user_likes, age_range, location'}, function(response) {
          first_name = response.first_name;
          last_name = response.last_name;
          email = response.email;
          id = response.id;
          location = response.location.name;
          gender = response.gender;
        //console.log(response.first_name);
        return response;
      });
  }
  function fb_login(){
      FB.login(function(response) {
            if (response.authResponse) {
                // The person logged into your app
                alert("login authorized");
            } else {
                // The person cancelled the login dialog
                alert("login failed");
            }
                }, {scope: 'email,user_likes'});
  }
</script>
<div onclick='fb_login();'>Login</div>
</body>
</html>