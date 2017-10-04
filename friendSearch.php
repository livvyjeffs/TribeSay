<?php
error_reporting(E_ERROR | E_PARSE);
include_once("php_includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if ($user_ok !== true || $log_username === "") {
    header("location: index.php?s");
    exit();
}
$columns = $_POST['columns'];

$spaceholder = "<img id='holderimage' style='visibility: hidden' src='".$root."/sourceImagery/Cbrain2.jpg'>";

//$spaceholder = "<img id='holderimage' style='display: none' src='".$root."/sourceImagery/Cbrain2.jpg' onload='masonry(" . $columns . ",10,\'friendPanel_large\',\'invisible\');" setTimeout(function() {masonry(" . $columns . ",10,\"friendPanel_large\");}, 10);'>";

if (isset($_POST['friendUsername']) && $_POST['friendUsername'] !== "" && isset($_POST['pool']) && $_POST['pool'] === "tribe") {
    $friendUsername = strtolower($_POST['friendUsername']);
    $strLength = strlen($friendUsername);
    $partialStr = substr($friendUsername, 0, $strLength);
    $matchingFriends = array();

    //query db for all users
    $sql = "SELECT username,avatar FROM users WHERE activated='1'";
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $username = $row['username'];
        $username_l = strtolower($username);
        $avatar = $row['avatar'];
        if ($partialStr === substr($username_l, 0, $strLength)) {
            $friend = array($username, $avatar);
            array_push($matchingFriends, $friend);
        }
    }
    if (count($matchingFriends !== 0)) {
        $friendColumn = "";
        foreach ($matchingFriends as $match) {
            $friendColumn .= '<div id="' . $match[0] . '" class="friend_large">';
            $friendColumn .= '<a href="'.$root.'/index.php?p=' . $match[0] . '" target="_blank">';
            if ($match[1] === NULL) {
                $friendColumn .= '<img src="'.$root.'/sourceImagery/default_avatar.png" onload="masonry(' . $columns . ',10,\'friendPanel_large\',\'invisible\');">';
            } else {
                $friendColumn .= '<img src="'.$s3root.'/user/' . $match[0] . '/' . $match[1] . '" onload="masonry(' . $columns . ',10,\'friendPanel_large\',\'invisible\');">';
            }

            $friendColumn .= '<div>' . $match[0] . '</div>';
            $friendColumn .= '</a>';
            $friendColumn .= '</div>';
        }

        echo $friendColumn . $spaceholder;
        exit();
    }
} 
else if ((isset($_POST['pool']) && $_POST['pool'] === "allTribe") || (isset($_POST['pool']) && $_POST['pool'] === "tribe" && isset($_POST['friendUsername']) && $_POST['friendUsername'] === "")) {
    //query db for all users
    $matchingFriends = array();
    $sql = "SELECT username,avatar FROM users WHERE activated='1' ORDER BY RAND() LIMIT 50";
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $username = $row['username'];
        $avatar = $row['avatar'];
        $friend = array($username, $avatar);
        array_push($matchingFriends, $friend);
    }
    $friendColumn = "";
    foreach ($matchingFriends as $match) {
        $friendColumn .= '<div id="' . $match[0] . '" class="friend_large">';
        $friendColumn .= '<a href="index.php?p=' . $match[0] . '">';
        if ($match[1] === NULL) {
            $friendColumn .= '<img src="'.$root.'/sourceImagery/default_avatar.png" onload="masonry(' . $columns . ',10,\'friendPanel_large\',\'invisible\');">';
        } else {
            $friendColumn .= '<img src="'.$s3root.'/user/' . $match[0] . '/' . $match[1] . '" onload="masonry(' . $columns . ',10,\'friendPanel_large\',\'invisible\');">';
        }
        $friendColumn .= '<div>' . $match[0] . '</div>';
        $friendColumn .= '</a>';
        $friendColumn .= '</div>';
    }


    echo $friendColumn . $spaceholder;
    exit();
} 
else if ((isset($_POST['pool']) && $_POST['pool'] === "allFriends") || (isset($_POST['pool']) && $_POST['pool'] === "friends" && isset($_POST['friendUsername']) && $_POST['friendUsername'] === "")) {
    //get array of friends of logged in user
    $friends = array();
    //Query friends table for all of posted user's array of friends
    $sql = "SELECT * FROM friends WHERE user1='$log_username' AND accepted='1' OR user2='$log_username' AND accepted='1'";
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        if ($row['user1'] === $log_username) {
            array_push($friends, $row['user2']);
        } elseif ($row['user2'] === $log_username) {
            array_push($friends, $row['user1']);
        }
    }
    $friendColumn = "";
    foreach ($friends as $friend) {
        $sql = "SELECT * FROM users WHERE username='$friend' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $friendsName = $row['username'];
            $friendsPic = $row['avatar'];
        }
        $friendColumn .= '<div class="friend_large">';
        $friendColumn .= '<a href="index.php?p=' . $friend . '">';
        if ($friendsPic === NULL) {
            $friendColumn .= '<img src="'.$root.'/sourceImagery/default_avatar.png" onload="masonry(' . $columns . ',10,\'friendPanel_large\',\'invisible\');">';
        } else {
            $friendColumn .= '<img src="'.$s3root.'/user/' . $friend . '/' . $friendsPic . '" onload="masonry(' . $columns . ',10,\'friendPanel_large\',\'invisible\');">';
        }
        $friendColumn .= '<div>' . $friendsName . '</div>';
        $friendColumn .= '</a>';
        $friendColumn .= '</div>';
    }


    echo $friendColumn . $spaceholder;
    exit();
} 
else if (isset($_POST['friendUsername']) && $_POST['friendUsername'] !== "" && isset($_POST['pool']) && $_POST['pool'] === "friends") {
    $friendUsername = strtolower($_POST['friendUsername']);
    $strLength = strlen($friendUsername);
    $partialStr = substr($friendUsername, 0, $strLength);
    $matchingFriends = array();
    //get array of friends of logged in user
    $friends = array();
    //Query friends table for all of posted user's array of friends
    $sql = "SELECT * FROM friends WHERE user1='$log_username' AND accepted='1' OR user2='$log_username' AND accepted='1'";
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        if ($row['user1'] === $log_username) {
            array_push($friends, $row['user2']);
        } elseif ($row['user2'] === $log_username) {
            array_push($friends, $row['user1']);
        }
    }
    foreach ($friends as $match) {
        $match_l = strtolower($match);
        if ($partialStr === substr($match_l, 0, $strLength)) {
            array_push($matchingFriends, $match);
        }
    }
    $friendColumn = "";
    foreach ($matchingFriends as $match) {
        $sql = "SELECT avatar FROM users WHERE username='$match' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $avatar = $row['avatar'];
            $friendColumn .= '<div id="' . $match[0] . '" class="friend_large">';
            $friendColumn .= '<a href="index.php?p=' . $match . '">';
            if ($avatar === NULL) {
                $friendColumn .= '<img src="'.$root.'/sourceImagery/default_avatar.png">';
            } else {
                $friendColumn .= '<img src="'.$s3root.'/user/' . $match . '/' . $avatar . '">';
            }
            $friendColumn .= '<div>' . $match . '</div>';
            $friendColumn .= '</a>';
            $friendColumn .= '</div>';
        }
    }
    echo $friendColumn . $spaceholder;
    exit();
}
?>

<?php include_once("standardhead.php") ?>   

<!DOCTYPE html>
<html>
    <head>
        <title>TribeSay Search</title>        
        
        <link rel="stylesheet" href="<?php echo $root; ?>/style/friendsearch.css"/> 
        <link rel="stylesheet" href="<?php echo $root; ?>/style/grid.css"/> 
        
        <script src="<?php echo $root; ?>/js/tags.js"></script>
        <script src="<?php echo $root; ?>/js/dragndrop.js"></script> 
        
        <script>
            function friendFinder(pool) {
                var columns = get_columns();
                if (pool === "check") {
                    _("friendName").value = "";
                    var selectBox = document.getElementById("tribeSearchFilter");
                    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
                    pool = selectedValue;
                    if (pool === "allFriends") {
                        _("friendName").getAttributeNode("onkeyup").nodeValue = "friendFinder('friends');";
                    }
                    else if (pool === "allTribe") {
                        _("friendName").getAttributeNode("onkeyup").nodeValue = "friendFinder('tribe');";
                    }
                }
                var username = _("friendName").value;
                _("theStatus").innerHTML = "loading";
                var ajax = new ajaxObj("POST", frenetic.root + "/friendSearch.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) === true) {

                        //alert(ajax.responseText);

                        if (ajax.responseText === "failed") {
                            _("theStatus").innerHTML = "no results";
                            _("friendStream").innerHTML = "";
                        } else {
                            _("theStatus").innerHTML = "";
                            _("friendStream").innerHTML = ajax.responseText;
                            if (_("friendStream").innerHTML === "") {
                                _("friendStream").innerHTML = "no results";
                            }
                        }
                    }
                };
                ajax.send("friendUsername=" + username + "&pool=" + pool + "&columns=" + columns);
            }
        </script>

    </head>

    <body onload="//friendFinder('check');">
<?php include_once("analyticstracking.php") ?>
<?php include_once("template_pageTop.php"); ?>
        <div id="main_tags">
            <div class='grid_6' style="height: 50px; padding-top: 50px;">

                <div id="friendSearch">Search for friends: <input type="text" id="friendName" onkeyup="friendFinder('friends');"> <span id="theStatus">Here is the status</span>
                </div>
            </div>
            <div class='grid_6 omega' style="height: 50px; padding-top: 50px;">
                <div id="friendSearchDropdownBar">
                    <div>Search the Tribe by: </div>
                    <select id="tribeSearchFilter" onchange="friendFinder('check');">
                        <option value="allTribe" >All Members</option>
                        <option value="allFriends">Following</option>
                    </select>

                </div>
            </div>

            <div class='grid_12 omega' style="height: 80%">
                <div id="friendPanel_large">
                    <div id="friendStream"></div>
                </div>
            </div>


        </div>
       
    </body>
</html>
