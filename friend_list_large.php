<?php

include_once("php_includes/check_login_status.php");
//get array of friends of logged in user
$friends = array();
//Query friends table for all of posted user's array of friends
$sql = "SELECT * FROM friends WHERE user1='$log_username' AND accepted='1' OR user2='$log_username' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    if ($row['user1'] === $u) {
        array_push($friends, $row['user2']);
    } elseif ($row['user2'] === $u) {
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
    $friendColumn .= '<img src="'.$s3root.'/user/' . $friend . '/' . $friendsPic . '">';
    $friendColumn .= '<div>' . $friendsName . '</div>';
    $friendColumn .= '</a>';
    $friendColumn .= '</div>';
}
?>

<?php echo $friendColumn;
?>
