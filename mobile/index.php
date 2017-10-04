<?php
error_reporting(E_ERROR | E_PARSE);
//delete above code after a few weeks
include_once("../php_includes/check_login_status.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/php_includes/convert_date.php");
include_once("m_includes/m_load_modal.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/php_parsers/meta_getter.php");
if ($user_ok !== true) {
    if (!isset($_SESSION["f1"])) {
        $filter_status = "not_set";
        $filter_html = '';
        $header_style = '';
        $words_style = '';
        $_SESSION["f1"] = "news";
        $filter_status = "set"; //echo $_SESSION["f1"]
        $filter_html = '<div class="tag_module" title="' . $_SESSION["f1"] . '" type="tag"><div class="tag_text" title="' . $_SESSION["f1"] . '">' . $_SESSION["f1"] . '</div><div class="delete_tag" title="' . $_SESSION["f1"] . '"><span>x</span></div></div>';
        $words_style = 'style="display: none"';
    } else {
        $filter_status = "set"; //echo $_SESSION["f1"]
        $filter_html = '<div class="tag_module" title="' . $_SESSION["f1"] . '" type="tag"><div class="tag_text" title="' . $_SESSION["f1"] . '">' . $_SESSION["f1"] . '</div><div class="delete_tag" title="' . $_SESSION["f1"] . '"><span>x</span></div></div>';
        $words_style = 'style="display: none"';
    }
}

if($log_username === ""){
    $user_status = "not_logged_in";
}else{
    $user_status = "logged_in";
}

?>
<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/Article">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="<?php echo $root; ?>/sourceImagery/dot_icon.ico" />
        <meta name="viewport" content="minimal-ui">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">        
        <meta property="og:title" content="<?php echo $meta_title; ?>"/>
        <meta property="og:description" content="<?php echo $meta_description; ?>"/>
        <meta property="og:image" content="<?php echo $meta_image_src; ?>"/>

        <meta itemprop="name" content="<?php echo $meta_title; ?>">
        <meta itemprop="description" content="<?php echo $meta_description; ?>">
        <meta itemprop="image" content="<?php echo $meta_image_src; ?>">

        <title><?php echo $meta_title; ?></title>
        <meta name="description" content="<?php echo $meta_description; ?>" />

        <?php include_once("mobilehead.php") ?>

        <link rel="stylesheet" href="<?php echo $root; ?>/mobile/m_style/mobile.css?version=<?php echo $version_variable; ?>"/>
        <script src="<?php echo $root; ?>/js/article_formatting.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/dot.js?version=<?php echo $version_variable; ?>"></script>
        <script type="text/javascript" src="<?php echo $root; ?>/mobile/js/mobile.js?version=<?php echo $version_variable; ?>"></script>
        <script type="text/javascript" src="<?php echo $root; ?>/mobile/js/mobile_events.js?version=<?php echo $version_variable; ?>"></script>
    </head>
    <body>        
        <?php include_once($_SERVER["DOCUMENT_ROOT"] . "/analyticstracking.php") ?>
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=276160959215644";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        </script>
        <div id="modalBackground">
            <div class="login_message <?php echo $user_status; ?>">
                Hey there, thanks for visiting TribeSay.
                <br>
                Login to grow your tribe by voting on and posting content. Remember to check us out on desktop for more features.<br>
                <img src="<?php echo $root; ?>/sourceImagery/facebook/facebook_login_buttons/active_200.png" onclick="fb_login()">
            </div>
            <div class="logout_message <?php echo $user_status; ?>">
                Log Out
            </div>
        </div>
        <div id="header">
            <div id="logo_container"><img id="logo_dots" src="<?php echo $root; ?>/sourceImagery/three_dots.png"><img id="logo_icon" src="<?php echo $root; ?>/sourceImagery/mobile/mobile_logo_icon.png"></div>
            <img id="logo_words" <?php echo $words_style ?> src="<?php echo $root; ?>/sourceImagery/mobile/mobile_logo_words.png">
            <div id="tribe_bar"><?php echo $filter_html ?></div>           
            <?php echo $article_header ?>
            <div id="search_icon_container" <?php echo $menu_style; ?>><img id="search_icon" src="<?php echo $root; ?>/sourceImagery/search_icon.png"></div>
            <div id="pull_down"></div>
        </div>

        <div id="main">
            <div id="search_container">
                <div id="search_bar">
                    <div id="searchResults" class="term-list hidden"></div>
                    <div id="searchBox_container">
                        <input type="text" id="searchBox" class="search-field" placeholder="search tags" autofocus="">
                    </div>
                </div>
            </div>
            <div id="stream" <?php echo $stream_display_style ?>>
                <?php echo $loading_html; ?>
                <div id="stream_holder">
                </div>
            </div>
            <div id="content_container" <?php echo $content_display_style ?>>
                <div id="content_holder">
                    <?php echo $content_html ?>
                </div>
                <div id="action_bar">
                    <div class="previous"></div>
                    <div class="close"></div>
                    <div class="next"></div>
                </div>
                <div id="previous_btn">
                    <img src="<?php echo $root; ?>/sourceImagery/mobile/previous_button.png">
                </div>
                <div id="next_btn">
                    <img src="<?php echo $root; ?>/sourceImagery/mobile/next_button.png">
                </div>
            </div>
        </div>
        
    </body>
    <script>
  
    //$('.logo').addClass("rotate");
    var opts = {
        lines: 13, // The number of lines to draw
        length: 20, // The length of each line
        width: 10, // The line thickness
        radius: 30, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        direction: 1, // 1: clockwise, -1: counterclockwise
        color: '#e9e9e9', // #rgb or #rrggbb or array of colors
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: '50%', // Top position relative to parent in px
        left: '50%' // Left position relative to parent in px
    };    
    
    var spinner_fresh = new Spinner(opts).spin($('#stream')[0]);
    
    </script>
</html>