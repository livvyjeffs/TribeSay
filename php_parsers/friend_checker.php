<?php
include_once("../php_includes/check_login_status.php");
?><?php

//if (isset($_POST['check_friend']) && isset($_POST['username'])) {
if (isset($_POST['username'])) {
    
    //check viewers relationships to the page owner
    $u = $_POST['username'];
    $isFriend = 'stranger';
    
//    $ownerBlockViewer = false;
//    $viewerBlockOwner = false;
    if ($u !== $log_username && $user_ok === true) {
        $friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' LIMIT 1";
        if (mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0) {
            $isFriend = 'friend';
        }
        /* $block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
          if (mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0) {
          $ownerBlockViewer = true;
          }
          $block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
          if (mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0) {
          $viewerBlockOwner = true;
          } */
    } else if ($u === $log_username) {
        $isFriend = 'self';
    }
//create friending and blocking buttons/functionality...adapt this to the div olivia created below
//    $friend_button = '';
//    $block_button = '<button disabled>Block User</button>';
//// LOGIC FOR FRIEND BUTTON
////for friendBtn instead put id of elem where you want to print out request info from ajax
//    if ($isFriend === true) {
//        $friend_button = '<div id="friend_status" class="friend" onclick="friendToggle(\'' . $u . '\')"> - </div>';
//    } else if ($user_ok == true && $u != $log_username && $ownerBlockViewer == false) {
//        $friend_button = '<div id="friend_status" class="stranger" onclick="friendToggle(\'' . $u . '\')"> + </div>';
//    }
//// LOGIC FOR BLOCK BUTTON
//    if ($viewerBlockOwner === true) {
//        $block_button = '<button onclick="blockToggle(\'unblock\',\'' . $u . '\',\'blockBtn\')">Unblock User</button>';
//    } else if ($user_ok === true && $u != $log_username) {
//        $block_button = '<button onclick="blockToggle(\'block\',\'' . $u . '\',\'blockBtn\')">Block User</button>';
//    }
//    //echo out response text with the type of button desired...
    echo $isFriend;
    exit();
}
?>
