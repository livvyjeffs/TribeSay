<?php

include_once("php_includes/check_login_status.php");

if ($user_ok === true) {
    $sql = "SELECT avatar,ratio FROM users WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $avatar = $row[0];
    if ($avatar === null) {
        $profile_pic_src = $root . "/sourceImagery/default_avatar.png";
    } else {
        $profile_pic_src = $s3root . "/user/" . $log_username . "/" . $avatar;
    }
    $profile_ratio = $row[1];
    $user_login_status = 'logged_in';
    //experiment later
    $tagStream_status = 'closed';
}else{
    $user_login_status = 'not_logged_in';
    $tagStream_status = 'closed';
}
if (isset($_SERVER["HTTPS"])) {
    $root = "https://" . $_SERVER["HTTP_HOST"];
} else {
    $root = "http://" . $_SERVER["HTTP_HOST"];
}


//hides things in the header bar
$needles = array('notifications.php','settings.php','friendSearch.php','tags.php','about.php','blog.php');

foreach ($needles as $n){
    if(strpos($_SERVER["SCRIPT_FILENAME"], $n) !== false){
        $internal_class = ' hidden';
        break;
    }
}

$l_pid = '';
$l_uid = '';
$l_media = '';
$rn = '';
$f_count = '';
$login_status = '';
$new_signup = '';
$specific_user = '';
$load_link = '';
$l_cid = '';

if ($login_status === "no") {
    $soundcloud_sdk = '';
} else {
    //$soundcloud_sdk = '';
    $soundcloud_sdk = '<script src="https://connect.soundcloud.com/sdk.js"></script>';
}

include_once("php_includes/detect_link.php");

//degine right nav bar selector class variables
$nav_bar_array = array("tribe" => " selected", "friends" => "", "single" => "", "tags" => "", "fs" => "", "nots" => "", "set" => "");
//$rn = $_GET["rn"];

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

$profile_pic = "";
$selectedFavs = "";

if ($user_ok === true) {
    //populate selecetd favs html
    $sql = "SELECT * FROM selectedfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    $selectedFavsArray = array();
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $tagName = $row['tagname'];
        $selectedFavs .= '<div class="tag_module" title="' . $tagName . '" type="tag"><div class="tag_text" tag="' . $tagName . '">' . $tagName . '</div><div class="delete-tag button" title="remove this tag" onclick="removeTag($(this).parent()); remove_from_filter(this.parentNode.title);">x</div></div>';
        array_push($selectedFavsArray, $tagName);
    }
    //pull the avatar image link from the db
    $sql = "SELECT * FROM users WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    //trial variation of query and print
    $row1 = mysqli_fetch_assoc($query);
    $avatar = $row1['avatar'];
    //////////////////////////////////////////////////
    //generate profile pic html
    $profile_pic_src = $s3root . '/user/' . $log_username . '/' . $avatar;
    $profile_pic = '<div id="profile_image" class="centered"><a href="settings.php"><img src="' . $s3root . '/user/' . $log_username . '/' . $avatar . '" alt="' . $log_username . '" title="' . $log_username . '" onload="move_furniture();"></a></div>';
    //set to default if null
    if ($avatar === NULL) {
        $profile_pic_src = $root . '/sourceImagery/default_avatar.png';
        $profile_pic = '<div id="profile_image" class="centered"><a href="settings.php"><img src="' . $root . '/sourceImagery/default_avatar.png" alt="' . $log_username . '" title="' . $log_username . '"></a></div>';
    }
} else {
    $log_username = "guest";
    $profile_pic = '<div id="profile_bar_text" class="centered"><a id="login_here" href="#">Login</a><span> | </span><a id="signup_here" href="#">Signup</a></div>';
    //$profile_pic = '<a href="index.php?login">Login</a><span> | </span><a href="index.php?s">Signup</a>';

    $session_filters = array("f1", "f2", "f3", "f4", "f5");
    foreach ($session_filters as $f) {
        if (isset($_SESSION[$f])) {
            $tagName = $_SESSION[$f];
            $selectedFavs .= '<div class="tag_module" title="' . $tagName . '" type="tag"><div class="tag_text" tag="' . $tagName . '">' . $tagName . '</div><div class="delete-tag button" title="remove this tag" onclick="removeTag($(this).parent()); remove_from_filter(this.parentNode.title);">x</div></div>';
            array_push($selectedFavsArray, $tagName);
        }
    }
}
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" href="<?php echo $root; ?>/sourceImagery/dot_icon.ico" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">

<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Play:400,700' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Cedarville+Cursive' rel='stylesheet' type='text/css'>
<link href="http://fonts.googleapis.com/css?family=Russo+One|Audiowide|Righteous|Comfortaa:400,700|Prosto+One&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css">

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/page_variables.js?version=<?php echo $version_variable; ?>"></script>

<script type="text/javascript">

    var frenetic = new Object();
    
    frenetic.pagename = '<?php echo $p_type; ?>';
    
    frenetic.classifieds = new Object();
    
    frenetic.classifieds.user = new Object();
    frenetic.classifieds.user.payment_status = '<?php echo $regular_post_cost; ?>';
    
    frenetic.root = '<?php echo $root; ?>';

    frenetic.s3root = '<?php echo $s3root; ?>';

    frenetic.user = new Object();

    frenetic['user'].username = '<?php echo $log_username; ?>';
    frenetic['user'].avatar = '<?php echo $profile_pic_src; ?>';
    frenetic['user'].avatar_ratio = '<?php echo $profile_ratio; ?>';

    ////////MARTIN THIS NEEDS TO BE CREATED IN PHP///////////////////////////////////////////////////////////////////////////////////////
    frenetic['user'].score = '<?php echo $user_score; ?>';
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if (frenetic['user'].username === 'guest') {
        frenetic['user'].user_id = 'stranger_' + Math.round(Math.random() * 10000000000);
        frenetic['user'].login_status = 'not_logged_in';
    } else {
        frenetic['user'].user_id = frenetic['user'].username;
        frenetic['user'].login_status = 'logged_in';
    }

    frenetic.page_owner = new Object();

    frenetic['page_owner'].username = frenetic['user'].username;
    frenetic['page_owner'].avatar = frenetic['user'].avatar;
    frenetic['page_owner'].avatar_ratio = frenetic['user'].avatar_ratio;
    frenetic['page_owner'].score = frenetic['user'].score;
    
    frenetic.gate_id = 'fresh';

    frenetic.media = 'mixed';

    frenetic.scope = 'tribe';

    frenetic.link = new Object();

    frenetic['link'].username = '<?php echo $l_pid; ?>';
    frenetic['link'].avatar = '<?php echo $l_pid_avatar; ?>';
    frenetic['link'].avatar_ratio = '<?php echo $l_pid_avatar_ratio; ?>';

    frenetic['link'].rn = '<?php echo $rn; ?>';
    frenetic['link'].uid = '<?php echo $l_uid; ?>';
    frenetic['link'].media = '<?php echo $l_media; ?>';
    frenetic['link'].cid = '<?php echo $l_cid; ?>';

    frenetic['link'].login_status = '<?php echo $login_status; ?>';
    frenetic['link'].specific_user_status = '<?php echo $specific_user; ?>';
    frenetic['link'].signup_status = '<?php echo $new_signup; ?>';
    frenetic['link'].load_status = '<?php echo $load_link; ?>';

    frenetic.column_count = get_columns();

//    frenetic.column_width = ((window.innerWidth - 14 - 10 * (frenetic.column_count + 1)) / frenetic.column_count).toFixed(2);
    frenetic.column_width = preload_column_width();  
    

    if (detectmob()) {
        if (location.search.match("m=") !== null && location.search.match("u=") !== null) {
            var get_u = '<?php echo $_GET["u"]; ?>';
            var get_m = '<?php echo $_GET["m"]; ?>';
        }
    }

</script>

<script type="text/javascript" src="<?php echo $root; ?>/js/ajax.js?version=<?php echo $version_variable; ?>"></script>

<?php echo $soundcloud_sdk; ?>

<link rel="stylesheet" href="<?php echo $root; ?>/style/modal.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/normalize.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/textsize.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/pagetop.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/tags.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/debug.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">
<link rel="stylesheet" href="<?php echo $root; ?>/style/jquery.datetimepicker.css?version=<?php echo $version_variable; ?>" type="text/css" media="screen">

<link rel="stylesheet" href="<?php echo $root; ?>/style/relative.css?version=<?php echo $version_variable; ?>"/>


<script type="text/javascript" src="<?php echo $root; ?>/js/main.js?version=<?php echo $version_variable; ?>"></script>

<script type="text/javascript" src="<?php echo $root; ?>/js/jquery.datetimepicker.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/data_wrappers.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/modal_object_creator.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/object_creators.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/jquery.easing.1.3.js?version=<?php echo $version_variable; ?>"></script>


<script type="text/javascript" src="<?php echo $root; ?>/js/masonry.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/content_upload.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/new-modal.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/checkingloadmore.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/tracking.js?version=<?php echo $version_variable; ?>"></script>

<script type="text/javascript" src="<?php echo $root; ?>/js/spinner.js?version=<?php echo $version_variable; ?>"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/dot.js?version=<?php echo $version_variable; ?>"></script>

<!--google maps api script-->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB6ypiORs4dOPlNd3XVmWV2tJlbycc8rv8" type="text/javascript"></script>

<!--http://ubilabs.github.io/geocomplete/-->
<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo $root; ?>/js/jquery.geocomplete.min.js?version=<?php echo $version_variable; ?>"></script>

<!--stripe-->
<script src="https://checkout.stripe.com/checkout.js"></script>

