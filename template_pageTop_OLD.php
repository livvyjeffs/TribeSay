<?php

if ($pagename === 'classifieds') {
    $news_style = 'style="display: none"';
} else if ($pagename === 'news') {
    $classifieds_style = 'style="display: none"';
}

?>

<div id="header" class="<?php echo $pagename; ?>">
    
    <div id="center_header_container" class="min<?php echo $internal_class; ?>">
        <div id="choose_your_tribe_background" class="centered button"></div>
        <div id="choose_your_tribe_text" class="centered button">Find Your Tribe</div>
        <div id="search_bar" class="centered"><input type="text" id="searchBox" class="search-field" placeholder="find your tribe" autofocus/>            
        </div>

    </div>

    <div id="left_header_container"><a id='you_are_here' class="centered" href="index.php?rn=tribe"><img class="logo centered <?php echo $user_login_status; ?>" src="<?php echo $root; ?>/sourceImagery/share.png" alt="logo" title="TribeSay Home">
            <div class="button word_logo centered" data-link="index.php?rn=tribe"><img src='<?php echo $root; ?>/sourceImagery/tribesay_white_text_logo_small.png'></div></a>

        <div id="tribe_bar" class="centered<?php echo $internal_class; ?>" ondrop="dropInFilter(event)" ondragover="allowDrop(event);"><?php echo $selectedFavs; ?>
            <div id="media_filter_dropdown" class="centered">
                <div id="media_filter" class="button"  <?php echo $news_style; ?>><span>All Types</span><img src="<?php echo $root; ?>/sourceImagery/downtab.png"></div>
                <div id="media_filter" class="button"  <?php echo $classifieds_style; ?>><span>Sometime</span><img src="<?php echo $root; ?>/sourceImagery/downtab.png"></div>
                <ul id="media_options" class="hidden button">
                    <li class="button" media="article" <?php echo $news_style; ?>>Articles</li>
                    <li class="button" media="image" <?php echo $news_style; ?>>Images</li>
                    <li class="button" media="video" <?php echo $news_style; ?>>Video</li>
                    <li class="button" media="sound" <?php echo $news_style; ?>>Sound</li>
                    <li class="button" media="mixed" <?php echo $news_style; ?>>All Types</li>
                    <li class="button" type="event" time="tonight" <?php echo $classifieds_style; ?>>Tonight</li>
                    <li class="button" type="event" time="this-weekend" <?php echo $classifieds_style; ?>>This Weekend</li>
                    <li class="button" type="event" time="later" <?php echo $classifieds_style; ?>>Sometime</li>
                </ul>
            </div>
        </div>
    </div>
    <div id="right_header_container">
        <div id="search_icon" class="<?php echo $internal_class; ?>"><img class="centered button" src="<?php echo $root; ?>/sourceImagery/search_icon.png"><input class="centered" placeholder="find your tribe"></div>
        <div id="post_button" class="button centered<?php echo $internal_class; ?> <?php echo $user_login_status; ?>" title="Click here to post content."><img class="centered" src="<?php echo $root; ?>/sourceImagery/post_button.png"><div class="centered">Post</div>
        </div><div id="profile_bar_text" class="centered <?php echo $user_login_status; ?><?php echo $internal_class; ?>"><a id="to_faq" class="button" href="about.php">About Us&nbsp;|&nbsp;</a><div id="login_here" class="button">Login</div><span>&nbsp;|&nbsp;</span><div id="signup_here" class="button">Signup</div>
        </div><div id="profile_bar_notifications" class="centered button <?php echo $user_login_status; ?>" title="Check Your Notificiations">
            <a href="notifications.php" class="centered"></a><div class="notification_count"></div>                    
        </div><div id="profile_image" class="centered button <?php echo $user_login_status; ?>"><img src="<?php echo $profile_pic_src; ?>" alt="<?php echo $log_username; ?>" title="<?php echo $log_username; ?>">
        </div><div id="menu_icon" class="centered button <?php echo $user_login_status; ?>"><img src="<?php echo $root; ?>/sourceImagery/menu_icon.png"></div></div>

    <div id="profile_menu" class="<?php echo $user_login_status; ?>">
        <a class="option button privilege" href="notifications.php" name="notifications">Notifications</a>
        <a class="option button privilege" href="settings.php">Settings</a>
        <div class='option button privilege<?php echo $internal_class; ?>' id='tutorial'>Tutorial</div>
        <a class="option button" href="about.php">About Us & FAQs</a>
        <a class="option button privilege" id='log_out_option' href="logout.php">Log Out</a>
        <div class='option button<?php echo $internal_class; ?>' id='log_in_option'>Log In</div>
        <div class='option button<?php echo $internal_class; ?>' id='sign_up_option'>Sign Up</div>
    </div>
</div> 