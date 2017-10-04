fnj<?php
error_reporting(E_ERROR | E_PARSE);
//prevent autologin u=martin since might be save in cookies or session
include_once("php_includes/abort_martin.php");
//delete above code after a few weeks
include_once("php_includes/check_login_status.php");
include_once("php_includes/convert_date.php");
include_once("php_includes/detect_link.php");
//indicate to highlight tribe content
$highlight = "tribe_content";
//stupid yc autologin crap                                                      UPDATE THIS YC LOGIN CODE FOR ONLINE LOGIN VALUES
if (isset($_GET["yc_login"])) {
    //Create their sessions and cookies
    $_SESSION['userid'] = '46';
    $_SESSION['username'] = 'ycdemo';
    $_SESSION['password'] = '97a54a3da110916c7f7eab3b54f38ea1';
    header("location: index.php?rn=tribe");
} elseif ($user_ok !== true && $login_status === "no" && $new_signup !== "yes") {
    header("location: stealthpage.php");
}

//olivia code

function tag($tag_id, $tag_type, $tag_state) {

    $delete_favorite = "<div class='delete-tag' title='remove this tag' onclick='removeTag($(this).parent()); removeFavorite(this.parentNode.title);'>x</div>";
    $add_favorite = "<div class='add-tag' onclick='add_to_filter(event);'>+</div>";
    $delete_tag = "<div class='delete-ftag' title='remove this tag' onclick='removeTag($(this).parent()); remove_from_filter(this.parentNode.title);'>x</div>";
    $add_tag = "<div class='add-tag' onclick='add_to_filter(event);'>+</div>";

    $tag_action = "";

    switch ($tag_state) {
        case 'delete_favorite':
            $tag_action = $delete_favorite;
            break;
        case 'add_favorite':
            $tag_action = $add_favorite;
            break;
        case 'delete':
            $tag_action = $delete_tag;
            break;
        case 'add':
            $tag_action = $add_tag;
            break;
    }
    return "<div title='" . $tag_id . "' class='tag_module' type='" . $tag_type . "' draggable='true' ondragstart='drag(event)'><div class='tag_text'>" . $tag_id . "</div>" . $tag_action . "</div>";
}

//olivia code
//generate available selected favs array
//populate selecetd favs html
$sql = "SELECT * FROM selectedfavorites WHERE user='$log_username'";
$query = mysqli_query($db_conx, $sql);
$selectedFavsArray = array();
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $tagName = $row['tagname'];
    array_push($selectedFavsArray, $tagName);
}
//puts in tribe filtering
$sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
$query = mysqli_query($db_conx, $sql);
$listed_favs = "";
if (mysqli_num_rows($query) === 0) {
    $listed_favs = "Drag and drop tags here";
} else {
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $tagName = $row['tagname'];
        //if (in_array($tagName, $selectedFavsArray) === false) {
        $listed_favs .= tag($tagName, 'favorites', 'add');
        //"<div class='tag_module' id='tag_" . $tagName . "' title='" . $tagName . "'>" . $tagName . "<div class='add-tag' title='" . $tagName . "' onclick='add_to_filter(event);'></div></div>";
        //}
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TribeSay Signals</title>

        <?php include_once("standardhead.php") ?>


        <link rel="stylesheet" href="style/smokesignals.css"/>   
        <link rel="stylesheet" href="style/comment_testing.css"/>   
        <link rel="stylesheet" href="style/modal.css"/> 
        <link rel="stylesheet" href="style/searchbar.css"/>
        <link rel="stylesheet" href="style/debug.css"/>
        
        <!--<link rel="stylesheet" href="style/datepicker/datepicker.css"/>
        <link rel="stylesheet" href="style/datepicker/layout.css"/>-->

        <script src="scraping/api_modules/js_scraping_services.js"></script>

        <script src="js/main.js"></script>
        <script src="js/toggle_functions.js"></script>
        <script src="js/ajax.js"></script>
        <script src="js/load_more_content.js"></script>
        <script src="js/vote_system.js"></script>
        <script src="js/new-modal.js"></script>
        <script src="js/sound.js"></script>
        <script src="js/masonry.js"></script>
        <script src="js/tags.js"></script>
        <!--<script src="js/tribeTags.js"></script>-->
        <script src="js/dragndrop.js"></script> 
        <script src="js/checkingloadmore.js"></script> 
        <script src="js/mainpage.js"></script>
        <script src="js/comments.js"></script>
        <script src="js/ellipsis.js"></script>
        <script src='js/searchbar.js'></script>

        <script src='js/extractor_api.js'></script>

        <script src='js/averagecolor.js'></script>

        <?php
        error_reporting(E_ERROR | E_PARSE);
//degine right nav bar selector class variables
        $nav_bar_array = array("tribe" => " selected", "friends" => "", "single" => "", "tags" => "", "fs" => "", "nots" => "", "set" => "");
        $rn = $_GET["rn"];

        if (isset($_GET["rn"])) {
            foreach ($nav_bar_array as $key => $el) {
                if ($rn === $key) {
                    $nav_bar_array[$key] = " selected";
                } else {
                    $nav_bar_array[$key] = "";
                }
            }
        }
//It is important for any file that includes this file, to have
//check_login_status.php included at its very top.

        $menu_bar = "";
        $tribe_bar = "";
        $friend_icon = "";
        $tribe_icon = "";

        $envelope = "";
        $loginLink = '&nbsp; <a class="nav_button" href="login.php">Log In</a> &nbsp; | &nbsp; <a href="signup.php">Sign Up</a>';
        $profile_pic = "";
        $profile_pic_btn = "";
        $avatar_form = "";

        if ($user_ok === true) {
            //puts in navbar
            $menu_bar .= '<div id="navigation">';
            $menu_bar .= '<nav style="height: 45%;">
                    <div>
                        <ul>
                            <li><a href="index.php">Tribe Content</a></li>
                            <li><a href="index.php?p=' . $log_username . '">Personal Content</a></li>
                            
                        </ul>
                    </div>
                </nav>';
            //populate selecetd favs html
            $selectedFavs = "";
            $sql = "SELECT * FROM selectedfavorites WHERE user='$log_username'";
            $query = mysqli_query($db_conx, $sql);
            $selectedFavsArray = array();
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $tagName = $row['tagname'];
                $selectedFavs .= '<div class="tag_module" title="' . $tagName . '" type="tag"><div class="tag_text">' . $tagName . '</div><div class="delete-tag" title="remove this tag" onclick="removeTag($(this).parent()); remove_from_filter(this.parentNode.title);">x</div></div>';
                array_push($selectedFavsArray, $tagName);
            }




            /* $tribe_bar .= '<select id="tribe_list" onchange="tribeTags(this.id); updateSelFavorites(event);">
              <option>Filter</option>';
              //puts in tribe filtering
              $sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
              $query = mysqli_query($db_conx, $sql);
              while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
              $tagName = $row['tagname'];
              if (in_array($tagName, $selectedFavsArray) === false) {
              $tribe_bar .= '<option>' . $tagName . '</option>';
              }
              }
              $tribe_bar .= '</select>'; */


            //generate for for changing avatar
            $avatar_form = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
            $avatar_form .= '<input type="file" name="avatar" required>';
            $avatar_form .= '<p><input type="submit" value="Upload"></p>';
            $avatar_form .= '</form>';


            //generate button to display form generated above

            $profile_pic_btn = '<button id="change_profile_btn" onclick="toggleElement(\'avatar_form\'); this.style.opacity=\'0.5\';">Change</button>';

            //check database for last time notes were checked 
            $sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
            $row = mysqli_fetch_row($query);
            $notescheck = $row[0];
            $sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
            $queryNotes = mysqli_query($db_conx, $sql);
            $numrows = mysqli_num_rows($queryNotes);
            if ($numrows === 0) {
                $envelope = '<a class="nav_button" href="notifications.php" title="Your notifications and friend requests"><img id="notification_image" src="sourceImagery/note_still.png" alt="Notes"></a>';
            } else {
                $envelope = '<a class="nav_button" href="notifications.php" title="Youe have new notifications"><img id="notification_image" src="sourceImagery/note_flash.png" alt="Notes"></a>';
            }

            //rest of icons
            $friend_icon .= '<a class="nav_button" href="friendSearch.php" title="Search for friends."><img id="search_friends_icon" src="sourceImagery/friendsIcon.png" alt="Friends"></a>';
            $tribe_icon .= '<a class="nav_button" href="tags.php" title="Search for new tribes."><img id="search_tribes_icon" src="sourceImagery/tagIcon.png" alt="Tribes"></a>';

            //pull the avatar image link from the db
            $sql = "SELECT * FROM users WHERE username='$log_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
            //trial variation of query and print
            $row1 = mysqli_fetch_assoc($query);
            $avatar = $row1['avatar'];
            //////////////////////////////////////////////////
            //generate profile pic html
            $profile_pic = '<a href="settings.php"><img src="user/' . $log_username . '/' . $avatar . '" alt="' . $log_username . '" title="' . $log_username . '"></a>';
            //set to default if null
            if ($avatar === NULL) {
                $profile_pic = '<a href="settings.php"><img src="sourceImagery/default_avatar.png" alt="' . $log_username . '" title="' . $log_username . '"></a>';
            }
            $loginLink = '&nbsp; <a class="nav_button" hreffunctioin="logout.php">Log Out</a>';
        }
        ?>

        <script>
            //direct all mobile or non chrome users to usechrome.php
            function detectmob() {
                if (navigator.userAgent.match(/Android/i)
                        || navigator.userAgent.match(/webOS/i)
                        || navigator.userAgent.match(/iPhone/i)
                        || navigator.userAgent.match(/iPad/i)
                        || navigator.userAgent.match(/iPod/i)
                        || navigator.userAgent.match(/BlackBerry/i)
                        || navigator.userAgent.match(/Windows Phone/i)
                        ) {
                    return true;
                }
                else {
                    return false;
                }
            }

            var browser = BrowserDetect.browser;
            if (browser !== "Chrome") {
                location.href = "useChrome.php";
            }

            var check = detectmob();
            if (check === true) {
                location.href = "useChrome.php";
            }


            function report_bug() {
                //something with ajax
                var subject = $('#debug_form input').val();
                var message = $('#debug_form textarea').val();
                var browser = BrowserDetect.browser;
                var mobile = detectmob(); //'true' or 'false'
                var width = $(window).width();
                var height = $(window).height();

                var ajax = ajaxObj("POST", "php_parsers/save_debug_img.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {
                        $('#debug_form input').val('');
                        $('#debug_form textarea').val('');
                        $('.nav_button[number="0"]').trigger('click');
                    }
                };

                ajax.send("report_bug=yes" + "&subject=" + subject + "&message=" + message + "&browser=" + browser + "&mobile=" + mobile + "&window_width=" + width + "&window_height=" + height);
            }



        </script>   

    <div id="header">
        <div id="banner">
            <a href="index.php?rn=tribe"><img class="logo" src="sourceImagery/share.png" alt="logo" title="trialLogo"></a>


            <div id='you_are_here' class="button" data-link="index.php?rn=tribe"></div>



            <div id="tribe_bar" ondrop="dropInFilter(event)" ondragover="allowDrop(event);"> <?php echo $selectedFavs; ?></div>
            <div id="post_button" class="button" onclick="openModal('ad');" title="Post to TribeSay"></div>
            <div id="search_bar"><input type="text" id="searchBox" class="search-field" placeholder="search tags"/>
                <ul id="searchResults" class="term-list hidden"></ul></div>
            <div id="profile_image">
                <?php echo $profile_pic; ?>

            </div>    

        </div>



    </div> 
    <div id='side_navbar'>
        <a class="nav_button button" number="0" title='SmokeSignals'></a>
        <div>
            <a class="nav_button button scope" onclick='toggleNavigation($(this));
                    load_content("scope");
                    removeProfilePicture();' number="1" scope='tribe' href="index.php?rn=tribe" title='Tribe'></a>
            <a class="nav_button button scope" onclick='toggleNavigation($(this));
                    addProfilePicture($(this).attr("scope"));
                    load_content("scope");' number="2" scope='friends' href="index.php?rn=friends" title='Friends'></a>
            <a class="nav_button button scope" onclick='toggleNavigation($(this));
                    addProfilePicture($(this).attr("scope"));
                    load_content("scope", undefined, $("#profile_image img").attr("alt"));' number="3" scope='single' href="index.php?rn=single" title='Personal'></a>
        </div>
        <a class="nav_button button" number="7" href="tags.php" title='TagSearch'></a>
        <a class="nav_button button" number="8" href="friendSearch.php" title='FriendSearch'></a>
        <a class="nav_button button" number="4" href="notifications.php" title='Notifications'></a>
        <a class="nav_button button" number="5" href="settings.php" title='Account Settings'></a>
        <a class="nav_button button" number="6" href="logout.php" title='Log Out'></a>

    </div>







    <script>
        $(document).ready(function() {
            var load_link = '<?php echo $load_link; ?>';
            if (load_link === 'yes') {
                var uid = '<?php echo $l_uid; ?>';
                var media = '<?php echo $l_media; ?>';
                var cid = '<?php echo $l_cid; ?>';

                //console.log(uid + media + cid)

                var ajax = new ajaxObj("POST", "php_parsers/get_link_data.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {
                        var json = JSON.parse(ajax.responseText);
                        load_content('fresh_load');
                        openModal('viewer', json);
                        //console.log(json);
                    }
                };
                ajax.send("uid=" + uid + "&media=" + media + "&cid=" + cid);
            }
            var spec_user = '<?php echo $specific_user; ?>';
            if (spec_user === 'yes') {
                var pid = '<?php echo $l_pid; ?>';
                //alert(pid);
                go_to_person(pid);
            }
            var new_signup = '<?php echo $new_signup; ?>';
            if (new_signup === 'yes') {
                var signup_code = '<?php echo $l_signup_code ?>';
                //check link, if code is valid open modal, otherwise send them to the loginpage
                openModal('new_user', signup_code);
            }

            var login_status = '<?php echo $login_status; ?>';
            if (login_status === 'yes') {
               openLogin();
            }
        });



    </script>
    <!--relative-->
</head>
<body> 
    <?php include_once("analyticstracking.php") ?>
    <!--Modal Windows-->
    <div id="modalBackground" class="bt" uid="blank"></div>
    <div id="shareBackground" class="bt"></div>


    <div id="main" class="streampage">
        <div class="open" id="tagStream">

            <div id="minimize_tags" title="Minimize the TagStream" onclick="toggleSize($(this), 'tagStream');"></div>

            <ul id="filter_controls">
                <li id="view_all_favorites" onclick="add_to_filter(null, 'favorites');">View All Favorites</li>
                <li id="clear_filter" onclick="clear_filter();">Clear Filter</li>
            </ul>
            <div id="my_tribetags" ondrop="add_to_favorites(event)" ondragover="allowDrop(event)">
                <h1>Favorites<span id="edit_favorites" status="edit" onclick="toggle_edit($(this));"></span></h1>   
                <?php echo $listed_favs; ?>
            </div>
            <div id="trending_tribetags">
                <h1>Trending</h1>   
                <?php echo tag('tech', 'trending', 'add'); ?>
                <?php echo tag('startup', 'trending', 'add'); ?>
                <?php echo tag('health', 'trending', 'add'); ?>
            </div>
            <div id="debug_contact" class='button' onclick='openModal("debug")'>
                <h1>Give us your 2cents!</h1>
            </div>
        </div>


        <div id="stream_header">
            <div class='corner top_layer'></div>
            <div class='corner bottom_layer'></div>
            <div id="stream_top_container">
                <div class="corner left"></div>
                <div class="stream_top first last" type="smokesignals">SmokeSignals</div>
            </div>
        </div>
        <div id="stream_container" onscroll="invisibleHeights(this);">
            <div class="stream media" id="articleStream" type="smokesignals">

                <div class="content_container" id="articleStreamContainer">
                    <div class="content" id="articleContent" media="article">

                        <div class="media_container tristream loadmore_image" type="article"><img src='sourceImagery/spaceholder.jpg' onload='attachFunctions()'></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php include_once("template_pageBottom.php"); ?>

</body>
</html>
