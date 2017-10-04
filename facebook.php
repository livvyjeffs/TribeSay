        <div id="fb-root"></div>
        <script>
            
            function get_facebook_sdk() {
                return facebook_sdk_loaded;
            }
            
            var facebook_sdk_loaded = false;
    
            (function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=276160959215644";
          js.onload = function(){
              facebook_sdk_loaded = true;
                FB.XFBML.parse(document, function() {
            $('#modal_share').animate({'opacity': '1'}, 500);
        });
            }; 
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        
        //run this command after writing button on page to re-parse html: FB.XFBML.parse()
        </script>
        <script src='<?php echo $root; ?>/js/fb_login.js?version=<?php echo $version_variable; ?>'></script>