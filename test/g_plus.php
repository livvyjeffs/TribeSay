<?php
echo $_SERVER["HTTP_HOST"];
echo "<br>";
echo $_SERVER["HTTP_REFERER"];
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$link_to_share = "http://tribesay.com/index.php?rn=tribe&u=WedApr2316331220141013&m=video";
?>

<!-- Place this tag where you want the share button to render. -->
<div class="g-plus" data-action="share" data-href="<?php echo $link_to_share; ?>"></div>

<!-- Place this tag after the last share tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

