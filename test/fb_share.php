<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=276160959215644";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
//run this command after writing button on page to re-parse html: FB.XFBML.parse()
</script>
<script>
function write_it(){
    document.getElementById('write_me').innerHTML = '<div class="fb-share-button" data-href="http://tribesay.com/index.php?rn=tribe&u=FriApr2512270920149611&m=image" data-type="button"></div>';

}
</script>

<div class="fb-share-button" data-href="http://www.tribesay.com/index.php?u=FriApr2512364620147621&amp;m=image" data-type="button"></div>


<div id="write_me"></div>

<button onclick=write_it();"">click here to write button</button>