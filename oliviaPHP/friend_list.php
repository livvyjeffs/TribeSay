<?php
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/getFriendsArray.php");
$friendColumn = "";
foreach ($friends as $friend) {
    $sql = "SELECT * FROM users WHERE username='$friend' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $friendsName = $row['username'];
        $friendsPic = $row['avatar'];
    }
    $friendColumn .= '<div class="friend">';
    $friendColumn .= '<a href="index.php?p=' . $friend . '">';
    $friendColumn .= '<img src="'.$s3root.'/user/' . $friend . '/' . $friendsPic . '">';
    $friendColumn .= '<div>' . $friendsName . '</div>';
    $friendColumn .= '</a>';
    $friendColumn .= '</div>';
}
?>

<div id="friend_panel">
    <h1>Friends</h1>
    <?php echo $friendColumn; ?>
</div>