<?php include_once("php_includes/check_login_status.php"); ?> 

<!DOCTYPE html>
<html>
    <head>
        <title>About TribeSay</title>

        <link rel="stylesheet" href="<?php echo $root; ?>/style/internal.css?version=<?php echo $version_variable; ?>"/> 
        <link rel="stylesheet" href="<?php echo $root; ?>/style/about.css?version=<?php echo $version_variable; ?>"/>

        <?php include_once("standardhead.php") ?>   

        <script src="<?php echo $root; ?>/js/about.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/tags.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/dragndrop.js?version=<?php echo $version_variable; ?>"></script> 
      
    </head>
    <body>

        <?php include_once("analyticstracking.php") ?>
        <?php include_once("template_pageTop.php"); ?>

        <div id="main" class="internal">

            <div id="body_container">

                <div class="row full" style="margin-top: 1rem; text-align: left">
                    <h1>What is TribeSay?</h1>
                    <div class="text-content">TribeSay is a “social news” platform. This means that content is entirely user-submitted and curated. Anyone can post, but it’s up to the community to vote and comment on posts they like. The order of the content is determined by popularity and recentness. The posts that are most recent and have the most votes will be at the top.</div>
                    <div class="text-content">We also list sweet events.</div>
                </div>
              <div class="row full" label="faq">

                    <h1><div class="button" action="expand"><div class="centered">+</div></div>Some Frequently Asked Questions</h1>

                    <div class="faq">
                        <h1>What are all of these tiles?</h1>
                        <div class="text-content">Each tile is a piece of content posted by a user. It can be either an article, video, image or sound. By clicking on the tile’s title or image you can open a modal window and view the content.</div>
                    </div>                   
                    <div class="faq">
                        <h1>How do I filter content by tag?</h1>
                        <div class="text-content">
                            There are three ways that you can filter content:                        
                            <li>Using the "Find Your Tribe" button in the center</li>
                            <li>Clicking on the tags at the bottom of each tile</li>
                            <li>Clicking on the tags at the top of each content window</li>
                        </div>
                    </div>
                    <div class="faq">
                        <h1>How do I filter content by media type?</h1>
                        <div class="text-content">A drop down menu at the top of the main page allows you to filter all content by media type, or choose content of any type if you so choose
                            <br>
                            Your options are:
                            <li>Articles</li>
                            <li>Images</li>
                            <li>Video</li>
                            <li>Sound</li>
                            <li>All Types</li>
                        </div>
                    </div>

                    <div class="faq">
                        <h1>How do I post content?</h1>
                        <div class="text-content">The blue “plus sign (+)” button at the top right of the main page allows you to post links. Simply choose the type of content that you wish to post, copy and paste the URL, add your personal comments and tags (must have at least one tag), and click submit!</div>
                    </div>

                    <div class="faq">
                        <h1>Can I follow other users?</h1>
                        <div class="text-content">Yes. First, navigate to their “personal page” by clicking on their name or image. You can also search for them on the Friend Search page. You know you have arrived at their page when a tile pinned to the top left displays their image/username. On this tile there is also a “+follow” button which you can click.</div>
                    </div>

                    <div class="faq">
                        <h1>What are the three buttons on the top right?</h1>
                        <div class="text-content">
                            These “scope” buttons tell you what you are looking at.
                            <li>The “global” scope pulls content from the whole tribe and ranks it by popularity and recentness</li>
                            <li>The “network” scope pulls content posted by people you are following and ranks it by recentness</li>
                            <li>The “single” scope pulls only from one user. If you click on the single button it will take you to your own personal page. If you click on another user’s name or profile image it will take you to their page</li>
                        </div>
                    </div>

                    <div class="faq">
                        <h1>How can I see content from one particular user?</h1>
                        <div class="text-content">
                            There are two ways to navigate to a particular user’s page:
                            <li>Click on their nametag from a content tile that they posted</li>
                            <li>Click on their image next to a comment that they’ve posted</li>
                            <!--<li>Search for them in TribeSay’s Friend search (TribeSay.com/FriendSearch.php) - hover over your profile picture and click Friend Search</li>-->
                        </div>
                    </div>

                    <div class="faq">
                        <h1>How can I see the content that I have posted?</h1>
                        <div class="text-content">
                            Click on the “single” scope button on the top right of the main page (it’s the bottom of the three buttons) or on your profile picture at the top right of the main page.
                        </div>
                    </div>

                    <div class="faq">
                        <h1>How can I see the content posted by people that I follow?</h1>
                        <div class="text-content">
                            Click on the “network” scope button at the top right of the main page (it’s the middle button of the three vertical scope buttons).
                        </div>
                    </div>

                    <div class="faq">
                        <h1>How do I vote for a piece of content?</h1>
                        <div class="text-content">
                            Simply click the thumbs up image on the bottom right of the tile. It will turn green, denoting that you like/approve of the content, and show up on your personal page.</div>
                    </div>

                </div>
                <div class="row full" style="text-align: left">
                    <h1>Our Mission: Foster Tribes</h1>
                    <div class="text-content">
                        <p>TribeSay’s mission is to form ardent communities by sharing current news and events. It is a place where you can connect with people who share your passions and interests. Share content with them in our news section, find where they’re hanging out in our events section - it’s that simple. TribeSay - <i>Your Passion. Your Tribe.</i></p> 

                    </div>
                </div>
                <div id="footer">
                    TribeSay&trade;
                    1751 Pinnacle Drive, Ste 6,
                    McLean, Virginia 22101 | <a href="mailto:martin@tribesay.com">Contact Us</a>
                </div>
            </div>

        </div>

    </body>







