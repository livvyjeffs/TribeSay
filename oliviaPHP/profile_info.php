<?php
include_once("./php_includes/check_login_status.php");
//get page owner info form database
$sql = "SELECT * FROM users WHERE username='$u' LIMIT 1";
$query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $ownersUsername = $row['username'];
    $ownersPicture = $row['avatar'];
    $ownerJoined = $row['signup'];
    $ownerLastLogin = $row['lastlogin'];
    $ownerBio = "this is a filler bio until we include it as signup and editable.";
}
//contextualize owner pic
if ($ownersPicture === NULL) {
    $pictureRef = $root . '/sourceImagery/default_avatar.png';
} else {
    $pictureRef = $s3root . "/user/$u/$ownersPicture";
}
$pictureHTML = '<img src="' . $pictureRef . '" alt="profileImage">';
?><?php
//check viewers relationships to the page owner
$isFriend = false;
$ownerBlockViewer = false;
$viewerBlockOwner = false;
if ($u !== $log_username && $user_ok === true) {
    $friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
    if (mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0) {
        $isFriend = true;
    }
    $block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
    if (mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0) {
        $ownerBlockViewer = true;
    }
    $block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
    if (mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0) {
        $viewerBlockOwner = true;
    }
}
?><?php
//create friending and blocking buttons/functionality...adapt this to the div olivia created below
$friend_button = '';
$block_button = '<button disabled>Block User</button>';
// LOGIC FOR FRIEND BUTTON
//for friendBtn instead put id of elem where you want to print out request info from ajax
if ($isFriend === true) {
    $friend_button = '<div id="friend_status" class="friend" onclick="friendToggle(\'unfriend\',\'' . $u . '\')"></div>';
} else if ($user_ok == true && $u != $log_username && $ownerBlockViewer == false) {
    $friend_button = '<div id="friend_status" class="stranger" onclick="friendToggle(\'friend\',\'' . $u . '\')"></div>';
}
// LOGIC FOR BLOCK BUTTON
if ($viewerBlockOwner === true) {
    $block_button = '<button onclick="blockToggle(\'unblock\',\'' . $u . '\',\'blockBtn\')">Unblock User</button>';
} else if ($user_ok === true && $u != $log_username) {
    $block_button = '<button onclick="blockToggle(\'block\',\'' . $u . '\',\'blockBtn\')">Block User</button>';
}
?>
<div id="profile_panel">
    <?php echo $friend_button; ?>
       
    <div id="owner_profile_container_box">
        <?php echo $pictureHTML; ?>
        <p><?php echo $ownersUsername; ?></p>
    </div>
    <div class="profile_info">Joined TribeSay on <?php echo $ownerJoined; ?></div>
    <div class="profile_info">Was last active on <?php echo $ownerLastLogin; ?></div>
    <!--<div class="profile_info">About <?php echo $ownersUsername; ?>: <?php echo $ownerBio; ?></div>-->
</div>