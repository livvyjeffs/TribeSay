<?php
$pagename = 'events';
include_once("tile_head.php");
//include_once("php_parsers/events/classified_payments.php");
?>

<body>    

    <?php include_once("facebook.php"); ?>

    <div id="main">

        <?php include_once("oliviaPHP/tribePage_streams.php"); ?>

    </div>         

    <?php include_once("modals.php"); ?>

    <div id="scope_navigation" class="<?php echo $user_login_status; ?> <?php echo $pagename; ?>">
        <div class="scope tribe button" scope="tribe" title="See all events on TribeSay."></div>       
        <div class="scope single button" scope="single" title="See all events you have posted."></div>
    </div>

    <script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js?version=<?php echo $version_variable; ?>"></script>
    
</body>
</html>