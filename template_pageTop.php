
<div id="header" class="<?php echo $p_type; ?>">
    <div id="header_positional">
        <div id="left_header_container">
            <div class="pseudo_before vmiddle"></div>
            <a class="button" id="you_are_here" href="index.php?rn=tribe"><div class="pseudo_before vmiddle"></div><img class="centered" src="<?php echo $root ?>/sourceImagery/logo-tribesay.png"></a>         

            <div id="tribe_bar" class="centered<?php echo $internal_class; ?>" ondrop="dropInFilter(event)" ondragover="allowDrop(event);">
                <div id="tribe_bar_dropdown">
                    <div class="top"></div>
                    <div class="down"></div>
                </div>
                <div id="tribe_bar_positional">
                    <div class="pseudo_before vmiddle"></div>
                    <div class="logo centered button"></div><div id="search_icon" class="<?php echo $internal_class; ?>"><img class="centered button" src="<?php echo $root; ?>/sourceImagery/search_icon.png"><div class="input_container"><input class="centered" placeholder="find your tribe"></div>
                    </div>  <div id="center_header_container">
                    <div class="no_empty pseudo_before vmiddle"></div><div id="switch_container" class="centered">                       
                        <div class="news"><div class="no_empty pseudo_before vmiddle"></div>News</div><div class="events"><div class="no_empty pseudo_before vmiddle"></div>Events</div>
                     <div class="selected <?php echo $p_type; ?>"></div>
                    </div>
                </div><?php echo $selectedFavs; ?><div id="media_filter_dropdown" class="centered">
                    <div id="media_filter" class="button this-is-news"><span>News</span><img src="<?php echo $root; ?>/sourceImagery/downtab.png"></div>
                    <div id="media_filter" class="button this-is-event"><span>Events</span><img src="<?php echo $root; ?>/sourceImagery/downtab.png"></div>

                    <ul id="media_options" class="button hidden">
                        <li class="button primary this-is-news" type="news" media="mixed" text="News">All Types</li>
                        <li class="button secondary this-is-news" type="news" media="article" text="Articles">Articles</li>
                        <li class="button secondary this-is-news" type="news" media="image" text="Images">Images</li>
                        <li class="button secondary this-is-news" type="news" media="video" text="Video">Video</li>
                        <li class="button secondary this-is-news" type="news" media="sound" text="Audio">Audio</li>

                        <li class="button primary this-is-event" type="event" media="event" time="anytime">Events</li>
                        <li class="button this-is-event secondary" type="event" media="event" time="today" text="Today">Today</li>
                        <li class="button this-is-event secondary" type="event" media="event" time="tomorrow" text="Tomorrow">Tomorrow</li>
                        <li class="button this-is-event secondary" type="event" media="event" time="weekend" text="This Weekend">This Weekend</li>
                        <li class="button this-is-event secondary" type="event" media="event" time="anytime" text="In the Future">In the Future</li>

                    </ul>

                </div>     
                <div id="scope_navigation" class="this-is-news <?php echo $user_login_status; ?>">
                    <div class="pseudo_before vmiddle"></div>
                    <div class="scope tribe button" scope="tribe" title="See content from everyone."></div>    
                    <div class="scope network button" scope="friends" title="See content from people you follow."></div>    
                    <div class="scope single button" scope="single" title="See content you've posted and liked."></div>
                </div>
            </div>
            </div>
        </div>
        <div id="right_header_container">
            <div class="pseudo_before vmiddle"></div>
            <div id="post_button" class="button centered<?php echo $internal_class; ?> <?php echo $user_login_status; ?>" title="Click here to post content."><div class="pseudo_before vmiddle"></div><img class="centered" src="<?php echo $root; ?>/sourceImagery/post_button.png"><div class="centered">Post</div>
            </div><div id="profile_bar_text" class="centered <?php echo $user_login_status; ?><?php echo $internal_class; ?>"><div class="pseudo_before vmiddle"></div><a id="to_faq" class="button" href="about.php">About Us&nbsp;|&nbsp;</a><div id="login_here" class="button">Login</div>
                <!--<span>&nbsp;|&nbsp;</span><div id="signup_here" class="button">Signup</div>-->
            </div><div id="profile_bar_notifications" class="centered button <?php echo $user_login_status; ?>" title="Check Your Notificiations">
                <a href="notifications.php" class="centered"></a><div class="notification_count"></div>                    
            </div><div id="profile_image" class="centered button <?php echo $user_login_status; ?>"><div class="pseudo_before vmiddle"></div><img src="<?php echo $profile_pic_src; ?>" alt="<?php echo $log_username; ?>" title="<?php echo $log_username; ?>">
            </div><div id="menu_icon" class="centered button <?php echo $user_login_status; ?>"><div class="pseudo_before vmiddle"></div><img src="<?php echo $root; ?>/sourceImagery/menu_icon.png"></div></div>

        <div id="profile_menu" class="<?php echo $user_login_status; ?>">
            <a class="option button privilege" href="notifications.php" name="notifications">Notifications</a>
            <a class="option button privilege" href="settings.php">Settings</a>
            <div class='option button privilege<?php echo $internal_class; ?>' id='tutorial'>Tutorial</div>      
            <a class="option button" href="about.php">About Us & FAQs</a>
            <a class="option button" href="blog.php">Blog</a>
            <a class="option button privilege" id='log_out_option' href="logout.php">Log Out</a>
            <div class='option button<?php echo $internal_class; ?>' id='log_in_option'>Log In</div>
            <div class='option button<?php echo $internal_class; ?>' id='sign_up_option'>Sign Up</div>
        </div>
    </div>
</div> 