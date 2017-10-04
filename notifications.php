<?php
include_once("php_includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if ($user_ok !== true || $log_username === "") {
    header("location: index.php?s");
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
  $date_time = $row["date_time"];s
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
        $user1pic = '<img src="'.$s3root.'/user/' . $user1 . '/' . $user1avatar . '" alt="' . $user1 . '" class="user_pic">';
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
        <title>TribeSay Notifications</title>

        <link rel="stylesheet" href="<?php echo $root; ?>/style/notifications.css?version=<?php echo $version_variable; ?>"/> 
        
          <?php include_once("standardhead.php") ?>  
        

        <script src="<?php echo $root; ?>/js/tags.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/dragndrop.js?version=<?php echo $version_variable; ?>"></script> 
        <script src="<?php echo $root; ?>/js/ellipsis.js?version=<?php echo $version_variable; ?>"></script> 
        
        <script>  function friendReqHandler(action, reqid, user1, elem) {
                var conf = confirm("Press OK to '" + action + "' this friend request.");
                if (conf != true) {
                    return false;
                }
                _(elem).innerHTML = "processing ...";
                var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
                ajax.onreadystatechange = function() {
                    if (ajaxReturn(ajax) == true) {
                        if (ajax.responseText == "accept_ok") {
                            _(elem).innerHTML = "<b>Request Accepted!</b><br />Your are now friends";
                        } else if (ajax.responseText == "reject_ok") {
                            _(elem).innerHTML = "<b>Request Rejected</b><br />You chose to reject friendship with this user";
                        } else {
                            _(elem).innerHTML = ajax.responseText;
                        }
                    }
                }
                ajax.send("action=" + action + "&reqid=" + reqid + "&user1=" + user1);
            }
        </script>
       
    </head>
    <body>

        <?php include_once("analyticstracking.php") ?>
        <?php include_once("template_pageTop.php"); ?>


        <div id="main" class="internal">
            <div class="container">
                <div class="note_header">
                    <h1>Notifications</h1>
                    <div class="options">
                        <div class="mark_all button unread" status="unread">mark all as <strong>new</strong></div> | <div class="mark_all button read" status="read">mark all as <strong>read</strong></div>

                    </div>
                </div>
                <div class="notification_container">
                </div>
            </div>
        </div>

        <script src="<?php echo $root; ?>/js/notifications.js?version=<?php echo $version_variable; ?>"></script> 

    </body>





