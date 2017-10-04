<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js?version=<?php echo $version_variable; ?>"></script>
<script src="https://connect.soundcloud.com/sdk.js?version=<?php echo $version_variable; ?>"></script>


<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Play:400,700' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

<link rel="stylesheet" href="<?php echo $root; ?>/style/normalize.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/textsize.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">


<script type="text/javascript" src="<?php echo $root; ?>/js/jquery.easing.1.3.js?version=<?php echo $version_variable; ?>"></script>
<script src="//code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/ajax.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/main.js?version=<?php echo $version_variable; ?>"></script>

<script type="text/javascript" src="<?php echo $root; ?>/js/data_wrappers.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/object_creators.js?version=<?php echo $version_variable; ?>"></script>
<!--<script type="text/javascript" src='<?php //echo $root; ?>/js/searchbar.js?version=<?php //echo $version_variable; ?>'></script>-->
<script type="text/javascript" src='<?php echo $root; ?>/js/searchbar_TEST.js?version=<?php echo $version_variable; ?>'></script>
<script type="text/javascript" src='<?php echo $root; ?>/js/fb_login.js?version=<?php echo $version_variable; ?>'></script>
<script type="text/javascript" src='<?php echo $root; ?>/js/vote_system.js?version=<?php echo $version_variable; ?>'></script>

<script type="text/javascript" src="<?php echo $root; ?>/js/spinner.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/dot.js?version=<?php echo $version_variable; ?>"></script>


<script>
    
 

   var frenetic = new Object();
    
    frenetic.root = '<?php echo $root; ?>';    
       
    frenetic.s3root = '<?php echo $s3root; ?>';
    
    frenetic.user = new Object();    
    
    frenetic['user'].username = '<?php echo $log_username; ?>'; 
    frenetic['user'].avatar = '<?php echo $profile_pic_src; ?>';
    
    if(frenetic['user'].username === ''){
         frenetic['user'].user_id = 'stranger_' + Math.round(Math.random() * 10000000000); 
         frenetic['user'].login_status = 'not_logged_in';
    }else{
        frenetic['user'].user_id = frenetic['user'].username;
        frenetic['user'].login_status = 'logged_in';
    }    

    frenetic.page_owner = new Object();

    frenetic['page_owner'].username = frenetic['user'].username;  
    frenetic['page_owner'].avatar = frenetic['user'].avatar;
    frenetic['page_owner'].avatar_ratio = frenetic['user'].avatar_ratio;
     
   frenetic.gate_id = 'fresh';
   
   frenetic.media = 'mixed';
   
   frenetic.scope = 'tribe';
    
 
    
    frenetic.link = new Object();
    
    frenetic['link'].username = '<?php echo $l_pid; ?>';
    frenetic['link'].rn = '<?php echo $rn; ?>';
    frenetic['link'].uid = '<?php echo $l_uid; ?>';
    frenetic['link'].media = '<?php echo $l_media; ?>';
    frenetic['link'].cid = '<?php echo $l_cid; ?>';
    
    frenetic['link'].login_status = '<?php echo $login_status; ?>';
    frenetic['link'].specific_user_status = '<?php echo $specific_user; ?>';
    frenetic['link'].signup_status = '<?php echo $new_signup; ?>';
    frenetic['link'].load_status = '<?php echo $load_link; ?>';
   
   
</script>

<script>
   
(function( win ){
    
//    http://24ways.org/2011/raising-the-bar-on-mobile/

	var doc = win.document;
	
	// If there's a hash, or addEventListener is undefined, stop here
	if( !location.hash && win.addEventListener ){
		
		//scroll to 1
		window.scrollTo( 0, 1 );
		var scrollTop = 1,
			getScrollTop = function(){
				return win.pageYOffset || doc.compatMode === "CSS1Compat" && doc.documentElement.scrollTop || doc.body.scrollTop || 0;
			},
		
			//reset to 0 on bodyready, if needed
			bodycheck = setInterval(function(){
				if( doc.body ){
					clearInterval( bodycheck );
					scrollTop = getScrollTop();
					win.scrollTo( 0, scrollTop === 1 ? 0 : 1 );
				}	
			}, 15 );
		
		win.addEventListener( "load", function(){
			setTimeout(function(){
				//at load, if user hasn't scrolled more than 20 or so...
				if( getScrollTop() < 20 ){
					//reset to hide addr bar at onload
					win.scrollTo( 0, scrollTop === 1 ? 0 : 1 );
				}
			}, 0);
		} );
	}
})( this );
    </script>

