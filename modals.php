<!-- LEVEL 4 -->

<div class="modalBackground closed" type='onboarding'>
    <div class="modal_center_canvas">
        <div class="modal_container closed" id="modal_onboarding" type="onboarding">
            
            <!-- Welcome Message -->
            
            <div slide="1" class='closed'>
                <div class="centered left_column">
                    <div class="media_container profile_tile">
                        <img src="https://s3.amazonaws.com/TribeSay_images/user/LivvyJeffs/179299255972.jpg">
                        <div>Hello, I'm Olivia<br>one of the site creators</div>
                    </div>
                </div>
                <div class="centered right_column">
                    <h1>Welcome to TribeSay!</h1>

                    Get ready to become an active contributor to your news community - aka your Tribe.


                    <div class='button next_button'>
                        Get Started
                    </div>
                </div>
            </div>  
            
            <!-- Deciphered -->          
            
            <div slide="2" class='closed'>
                <div class="centered left_column">
                    <img src='<?php echo $root; ?>/sourceImagery/onboarding/sample_media.png'>
                </div>
                <div class="centered right_column">
                    First let's decipher these tiles.
                    <br><br>
                    Everything on TribeSay gets voted on via <img src='<?php echo $root; ?>/sourceImagery/onboarding/vote.png'>.
                    <br><br>
                    The best content goes to the top, so be sure to vote so other TribeSayers can enjoy the content too!
                    <br><br>
                    Next, we'll teach you how to find the best content on TribeSay.
                    <div class='button next_button'>
                        Next: The Black Bar
                    </div>
                </div>
            </div>
            
            <!-- Look at the Black Bar -->          
            
            <div slide="3" class='closed'>
                <div class="centered left_column">
                    The black bar on top is your control panel.
                    <br><br>
                    Let's go over this quickly, from left to right.
                </div>
                <div class="centered right_column">
                    <div class='button next_button'>
                        Next: Home Base
                    </div>
                </div>
            </div>
            
            <!-- Home Base -->
            
            <div slide="4" class='closed'>
                <div class="centered left_column">
                    <img src='<?php echo $root; ?>/sourceImagery/onboarding/logo.png'>
                </div>
                <div class="centered right_column">
                    If you ever get lost, click the logo and you'll come back to the home page.
                    <div class='button next_button'>
                        Next: Filtering
                    </div>
                </div>
            </div> 
            
            <!-- Filtering -->            
            
            <div slide="5" class='closed'>
                <div class="centered left_column">
                    You can filter by <img src='<?php echo $root; ?>/sourceImagery/onboarding/tags.png'> or <img src='<?php echo $root; ?>/sourceImagery/onboarding/media_type.png'>. 
                    <br><br>Try playing with these buttons and see the news change in the background.
                </div>
                <div class="centered right_column">
                    <div class='button next_button'>
                        Next: Searching
                    </div>
                </div>
            </div> 

            <!-- Searching -->            

            <div slide="6" class='closed'>
                <div class="centered left_column">
                    <img src='<?php echo $root; ?>/sourceImagery/onboarding/find_your_tribe.png'> 
                </div>
                <div class="centered right_column">
                    Click the "Find Your Tribe" button to explore the trending tags - and better yet, to see if your Tribe is already here.
                    <br><br>
                    Go ahead, try it out!
                    <div class='button next_button'>
                        Next: Posting
                    </div>
                </div>
            </div> 
            
            <!-- Posting -->            

            <div slide="7" class='closed'>
                <div class="centered left_column">
                    Click <img src='<?php echo $root; ?>/sourceImagery/onboarding/post_button.png'> to post content to your Tribe.
                    <br><br>
                    Choose the <img src='<?php echo $root; ?>/sourceImagery/onboarding/tags.png'> carefully to make sure your content gets to the right people.
                </div>
                <div class="centered right_column">
                    <div class='button next_button'>
                        Next: Your Notifications
                    </div>
                </div>
            </div> 

            <!-- Notifications -->            

            <div slide="8" class='closed'>
                <div class="centered left_column">
                    When you see a number here <img src='<?php echo $root; ?>/sourceImagery/onboarding/notifications.png'>  it means someone has liked or commented on your post.
                    <br><br>
                    Be sure to write back!
                </div>
                <div class="centered right_column">
                    <div class='button next_button'>
                        Next: Your Account
                    </div>
                </div>
            </div> 
            
            <!-- Your Account -->            

            <div slide="9" class='closed'>
                <div class="centered left_column">
                    Hover over your profile picture <img src='<?php echo $root; ?>/sourceImagery/onboarding/default_avatar.png'>  to access your account settings.
                    <br><br>
                    You have a default picture now, spruce up your profile by adding a picture!
                </div>
                <div class="centered right_column">
                    <div class='button next_button'>
                        Next: Scope
                    </div>
                </div>
            </div> 
            
            <!-- Scope -->            

            <div slide="10" class='closed'>
                <div class="centered left_column">                    
                    <img src='<?php echo $root; ?>/sourceImagery/onboarding/scope.png'>                    
                </div>
                <div class="centered right_column">
                    This tells you how deep you are in TribeSay. You can look at the whole Tribe, just people who you are following, or just content that you've posted.<br><br>It's pretty nifty.
                    <div class='button next_button'>
                        Next: Being a Good TribeSayer
                    </div>
                </div>
            </div> 

            <!-- Being a Good TribeSayer -->            

            <div slide="11" class="closed last">
                <div class="centered left_column">
                    Post, share, vote, and comment to grow your Tribe. 
                    <br><br>
                    If you have questions, comments, or see something that shouldn't be on TribeSay, email me: <a href="mailto:olivia@tribesay.com">olivia@tribesay.com</a>
                    <br><br>
                    If you need a refresher, just hover over your profile picture. Enjoy :)
                </div>
                <div class="centered right_column">
                    <div class='button next_button'>
                        You're Done!
                    </div>
                </div>
            </div> 

        </div>

    </div>
</div>

<!-- LEVEL 3 -->

<div class="modalBackground" id="modal_search">
    <div id='searchResults'>
        <div class="term-list hidden"></div>
        <input id='mobile_search_input' placeholder='find your tribe'>
    </div>
</div>

<!-- LEVEL 2 -->

<?php include_once("template_pageTop.php"); ?>    

<!-- LEVEL 1 -->

<div class="modalBackground closed">
    <div class="modal_center_canvas">
        <div class="modal_container closed" id="modal_event_posting">
            <div class="close">
                <div class="centered"></div>
            </div>
            <div slide="1" class="open">
                <h1>Post an Event to TribeSay</h1>
                <input id="event-title" class="full-width" type="text" placeholder="Event Title" required>
                <input id="event-location" class="full-width" type="text" placeholder="address and location" required>
                 <div id="event-date-time">
                    <input id="event-begin-date-time" type="text" placeholder="EVENT beginning date and time" required><span>to
                    </span><input id="event-end-date-time" type="text" placeholder="EVENT end date and time" required>
                </div>
                <textarea id="event-description" class="full-width" type="text" placeholder="Description" required></textarea>
                <div class="tag_ui_container full-width">
                    <div class='tag_input_container'>
                        <div class="selected_tags empty"></div>
                        <input class='tag_input search-field' maxlength='25' type='text' placeholder='add at least one tag here' autocomplete='off'>
                    </div>
                    <ul class='tag_selector term-list hidden'></ul>                        
                </div>
                <input id="event-ticket-link" type="url" placeholder="Link to tickets or webpage" required>
                <div id="event-cost">
                    <input type="radio" class="button" name="event-cost" value="free" checked><label>Free Event</label> or <label>$</label><input type="number" id="event-ticket-price" placeholder="Ticket Price"><label>USD</label>
                </div>
                <input id="event-image" type="file" name="image" title="Choose an image to represent your event." required><label>Choose an image to represent your event.</label>

                <div class="payment_form normal_post">
                    <input type="radio" class="button" name="payment-plan" value="regular_post" checked> $5 Regular Post 
                </div><div class="payment_form pinned_post">
                    <input type="radio" class="button" name="payment-plan" value="pinned_post"> $100 Pinned Post                     
                </div>
            </div> 

            <div class="action-buttons">
                <!--<div class="previous-button button">Previous</div>-->   
                <div class="discount-button button">Use Discount Code</div>
                <div class="post-button button <?php echo $regular_post_cost; ?>">Post</div>
                <div class="pay-button button <?php echo $regular_post_cost; ?>">Set Up Payment</div>
            </div>
        </div>
    </div>        
</div>

<div class="modalBackground closed">
    <div class="modal_center_canvas">
        <div class="modal_container closed" id="modal_upload">
            <div class="close">
                <div class="centered">
                </div>
            </div>
            <h1>Post an Article</h1>

            <div class="header_icon_container"><div class="header_icon button selected" type="article" title="Post an Article"></div>                
            </div><div class="header_icon_container"><div class="header_icon button not_selected" type="image" title="Post a Picture"></div>                
            </div><div class="header_icon_container"><div class="header_icon button not_selected" type="video" title="Post a Video"></div>                
            </div><div class="header_icon_container"><div class="header_icon button not_selected" type="sound" title="Post a SoundCloud Clip"></div></div>

            <div id="uploadContentContainer">

                <form class="uploadForm" media="article" onkeypress="return event.keyCode != 13;">
                    <input id="link_input" type="url" placeholder="Enter full URL here..." autofocus="">
                    <div class="submit button">Submit</div>
                </form>
                <form id="post_editor">
                    <input id="title_editor" type="text" maxlength="80" placeholder="Default Title">
                    <div id="picture_selector" class="empty" media="article"></div><textarea id="description_editor" placeholder="..your comments here." onkeyup="updatecount();"></textarea>
                    <div class="tag_ui_container full-width">
                    <div class='tag_input_container'>
                        <div class="selected_tags empty"></div>
                        <input class='tag_input search-field' maxlength='25' type='text' placeholder='add at least one tag here' autocomplete='off'>
                    </div>
                    <ul class='tag_selector term-list hidden'></ul>                        
                </div>
                </form>
            </div>
            <div id="post_to_stream_btn">Post</div>
        </div>
           
        <div class="modal_container closed" id="modal_viewer">
            
            <div id="content_headers"><div class="vote_container" type="content"><div class="upvote" token="UP"></div><div class="downvote" token="DOWN"></div></div>
                <div class="vote_tally empty"></div>
                <div id="content_tags" class="empty"></div>
                <div id="modal_share" class="empty">
                </div>
            </div>
            <div class="content_holder container">
                <div id="content_holder" class="empty"><div class="no_empty pseudo_before vmiddle"></div></div>
                <div id="comment_trigger" class="button"><div class="centered">View Comments</div></div>
                <div id="modal_ad" class="button"><div class='advertisement empty'></div></div>              
            </div>
            <div class="event_container">
                <div class="event-image empty"></div><div class="event-details">
                    <div class="event-image-title-holder empty">                       
                       <h1 class="event-title no_empty"></h1> 
                    </div>
                    
                    <div class="event-logistics">
                        <div class="event-date">
                            <label></label><div class="details empty"></div>
                        </div>
                        <div class="event-location">
                            <label></label><div class="details empty"></div>
                        </div>
                        <div></div>
                    </div>
                    <div class="event-actions">
                        <div class="event-add-to-calendar empty"></div><div class="event-go-to-tickets empty"></div>
                    </div><div class="event-description empty"></div><div class="event-map empty"></div>
                </div>               
            </div>
            <div id='modal_action'><div id="previous_btn" class="viewerButton" direction="previous">                    
                </div><div id="next_btn" class="viewerButton" direction="next">                    
                </div><div class="close"></div></div>


            <div class="comment_description_container container closed"><div id="comment_description_container">
                    <div id="description_input">
                        <textarea class="starter_comment" status="closed" rows="1" placeholder="leave a comment.."></textarea>
                        <div class="comment_options">
                            <div type="submit" class="submit_starter">Submit</div>
                            <div class="cancel_comment">Cancel</div>
                        </div>
                    </div>
                    <div id="comment_container">
                        <div class="empty" id="comment_viewer" mode="conversation">
                        </div>
                    </div>
                </div>

            </div>            

        </div>
    
        <div class="modal_container closed" id="modal_debug">
            <div class="close">
                <div class="centered"></div>                
            </div>
            <h1>Send us your ideas and let us know bugs.</h1>
            <form id="debug_form" name="photo" enctype="multipart/form-data" method="post">
                <input name="subject" type="text" placeholder="Bug Subject">
                <textarea name="message" rows="15" placeholder="Report bugs here. Below you can upload a screenshot. Thank you for beta testing!"></textarea>
                <label style="display: inline-block; width: auto">Upload a screenshot (optional):</label><input style="display: inline-block; width: auto" type="file" id="ImageBrowse" name="image" size="30">
                <input type="submit" value="submit">
            </form>
        </div>
    </div>
</div>


<!-- LEVEL 0 -->

<div class="modalBackground invisible">
    <div class="modal_center_canvas">
        <div class="modal_container invisible" id="modal_login" type="login">
            <div class="login_action empty"></div>
            <!--<div id="modal_login" class="modal_container" style="opacity: 1;">-->
            TribeSay<span class="beta"> beta </span>
            <form id="loginform">
                <input id="username" class="login" type="text" placeholder="username or email" autofocus="">
                <input id="password" type="password" placeholder="password" trigger="#login_btn">
            </form>
            <div class="signup_buttons">
                <div class="button" id="login_with_facebook" onclick="fb_login()"><img src="http://tribesay.com/sourceImagery/facebook/facebook_login_buttons/active_600.png" onload="size_login()"></div><div onclick="login();" id="login_btn" class="button" style="width: 226px;">Log In</div>
                <div class="create_an_account button signup">New user? Create an account.</div>
                <a class="forgot_your_password button" href="forgotPassword.php" target='_blank'>Forgot your password?</a>
            </div>       
        </div>
        <div class="modal_container closed" id="modal_signup" type="signup">
            <!--<div id="modal_signup" class="modal_container" style="opacity: 1;">-->
            Welcome to TribeSay<span class="beta"> beta</span>
            <form id="signupform">
                <input id="signup_username" class="signup" type="text" placeholder="username" autofocus="">
                <input id="signup_email" type="email" placeholder="you@me.you" required="">
                <input id="p1" type="password" placeholder="password">
                <input id="p2" type="password" trigger="#signup_btn" placeholder="confirm password">
            </form>
            <div class="signup_buttons">
                <div class="button" id="signup_btn" style="width: 226px;">Sign Up</div>
                <div class="button" id="signup_with_facebook" onclick="fb_login()"><img src="http://tribesay.com/sourceImagery/facebook/facebook_login_buttons/active_600.png"></div>
                <div class="create_an_account login button">Already have an account? Login.</div>
            </div>       
        </div>
    </div>
</div>

<script>modal_events();</script>