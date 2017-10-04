<?php 
$pagename = 'news';
include_once("tile_head.php"); 
?>

<body>        
        <?php include_once("facebook.php"); ?>
       
        <div id="main">

            <?php include_once("oliviaPHP/tribePage_streams.php"); ?>
            
        </div>         
        
        <?php include_once("modals.php"); ?>
   
        <script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js?version=<?php echo $version_variable; ?>"></script>
    </body>
</html>