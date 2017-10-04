<?php
include_once("php_includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if ($log_username === "") {
    header("location: " . $root);
    exit();
}
$notification_list = "";
/* $sql = "SELECT * FROM notifications WHERE username LIKE BINARY '$log_username' ORDER BY date_time DESC";
  $query = mysqli_query($db_conx, $sql);
  $numrows = mysqli_num_rows($query);
  if ($numrows < 1) {
  $notification_list = "You do not have any notifications";
  } else {
  while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
  $noteid = $row["id"];
  $initiator = $row["initiator"];
  $app = $row["app"];
  $note = $row["note"];
  $date_time = $row["date_time"];
  $date_time = strftime("%b %d, %Y", strtotime($date_time));
  $notification_list .= "<p><a href='index.php?p=$initiator'>$initiator</a> | $app<br />$note</p>";
  }
  }
  mysqli_query($db_conx, "UPDATE users SET notescheck=now() WHERE username='$log_username' LIMIT 1"); */
?><?php
$friend_requests = "";
$sql = "SELECT * FROM friends WHERE user2='$log_username' AND accepted='0' ORDER BY datemade ASC";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
if ($numrows < 1) {
    $friend_requests = 'No friend requests';
} else {
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $reqID = $row["id"];
        $user1 = $row["user1"];
        $datemade = $row["datemade"];
        $datemade = strftime("%B %d", strtotime($datemade));
        $thumbquery = mysqli_query($db_conx, "SELECT avatar FROM users WHERE username='$user1' LIMIT 1");
        $thumbrow = mysqli_fetch_row($thumbquery);
        $user1avatar = $thumbrow[0];
        $user1pic = '<img src="'.$root.'/user/' . $user1 . '/' . $user1avatar . '" alt="' . $user1 . '" class="user_pic">';
        if ($user1avatar == NULL) {
            $user1pic = '<img src="'.$root.'/images/avatardefault.jpg" alt="' . $user1 . '" class="user_pic">';
        }
        $friend_requests .= '<div id="friendreq_' . $reqID . '" class="friendrequests">';
        $friend_requests .= '<a href="'.$root.'/index.php?p=' . $user1 . '">' . $user1pic . '</a>';
        $friend_requests .= '<div class="user_info" id="user_info_' . $reqID . '">' . $datemade . ' <a href="'.$root.'/index.php?p=' . $user1 . '">' . $user1 . '</a> requests friendship<br /><br />';
        $friend_requests .= '<button onclick="friendReqHandler(\'accept\',\'' . $reqID . '\',\'' . $user1 . '\',\'user_info_' . $reqID . '\')">accept</button> or ';
        $friend_requests .= '<button onclick="friendReqHandler(\'reject\',\'' . $reqID . '\',\'' . $user1 . '\',\'user_info_' . $reqID . '\')">reject</button>';
        $friend_requests .= '</div>';
        $friend_requests .= '</div>';
    }
}
?> 
    
<!DOCTYPE html>
<html>
    <head>
        <title>TribeSay Settings</title>
        
        <?php include_once("standardhead.php") ?>   

        <link rel="stylesheet" href="<?php echo $root; ?>/style/settings.css?version=<?php echo $version_variable; ?>"/> 

        <script src="<?php echo $root; ?>/js/settings.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/tags.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/dragndrop.js?version=<?php echo $version_variable; ?>"></script> 
      
    </head>
    <body>

        <?php include_once("analyticstracking.php") ?>
        <?php include_once("template_pageTop.php"); ?>

        <div id="main" class="internal">

            <div class="modalBackground closed">
                <div class="modal_center_canvas">
                    <div class="modal_container closed" id="modal_change_profile"  option="1">
                        <h1>Change Your Profile Picture</h1>
                        <form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">
                            <input type="file" name="avatar" required="">
                                <input type="submit" value="Upload">
                        </form>
                    </div>
                    <div class="modal_container closed" id="modal_change_pw"  option="2">
                        <h1>Change Your Password</h1>
                        <form id="change_password_form">
                            <input type="password" required="" class="old" placeholder="Old Password">
                            <input type="password" required="" class="new_1" placeholder="New Password">
                            <input type="password" required="" class="new_2" placeholder="Confirm New Password">
                            <div class="submit-button button">Change</div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="body_container">
                <div class="settings_header">
                    <h1>Account Settings</h1>
                </div>
                <div class="settings_container">
                    <div class="option" option="0"><div class="profile_tile">
                                <img src="<?php echo $profile_pic_src; ?>">
                                <div><?php echo $log_username; ?></div>                                
                        </div></div>
                    <div class="option" option="1"><div class="option_container">
                            
                            <div class="button option-button">Change Your Profile Picture</div>
                        </div>
                    </div><div class="option last" option="2">
                        <div class="button option-button">Change Your Password</div>
                    </div><div class="option last" option="3">
                        <input id="email_notification_state" type="checkbox" checked="checked"><label>Receive Email Notifications</label>
                    </div>
                </div>
            </div>
        </div>

       
        <?php include_once("template_pageBottom.php"); ?>
    </body>







