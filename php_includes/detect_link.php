<?php

error_reporting(E_ERROR | E_PARSE);
include_once("check_login_status.php");
include_once("php_parsers/classifieds/customer_status_update.php");
//include this block in index.php
//link to specific piece of media
if (isset($_GET["m"]) && isset($_GET["u"])) {
    $load_link = "yes";
    $l_media = $_GET["m"];
    $l_uid = $_GET["u"];
    $l_cid = $_GET["c"];
} else {
    $load_link = "no";
}
//link to specific person
if (isset($_GET["p"])) {
    $specific_user = "yes";
    $l_pid = $_GET["p"];
    //get avatar path and ratio
    $sql = "SELECT avatar,ratio,username FROM users WHERE username='$l_pid' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $avatar = $row[0];
    if ($avatar === null) {
        $l_pid_avatar = $root . "/sourceImagery/default_avatar.png";
    } else {
        $l_pid_avatar = $s3root . "/user/" . $row[2] . "/" . $avatar;
    }
    $l_pid_avatar_ratio = $row[1];
} else {
    $specific_user = "no";
}
//link to signup page
if (isset($_GET["s"])) {
    $new_signup = "yes";
    $l_signup_code = $_GET["s"];
} else {
    $new_signup = "no";
}
//link to login page
if (isset($_GET["login"])) {
    $login_status = 'yes';
} else {
    $login_status = 'no';
}
//linkt to ...?
if (isset($_GET["rn"])) {
    $rn = $_GET["rn"];
}
//link to events vs news
if (isset($_GET["p_type"])) {
    if ($_GET["p_type"] === "events" || $_GET["p_type"] === "news") {
        $p_type = $_GET["p_type"];
    }
} else {
    $p_type = "events";
}
//detect filter params
$f_count = 0;
if (isset($_GET["f1"])) {
    //create $filters_to_add
    $filter_to_add = "";
    $f_array = array("f1", "f2", "f3", "f4", "f5");
    //collect all filter specifications
    foreach ($f_array as $f) {
        if (isset($_GET[$f])) {
            $$f = $_GET[$f];
            $f_count++;
            //create $filters_to_add
            $filter_to_add .= "('user','" . $$f . "',now()),";
            //create session variable for non-users
            $_SESSION[$f] = $_GET[$f];
        }
    }
    //remove trailing comma from filter_to_add
    $filter_to_add = rtrim($filter_to_add, ",");
    //echo "add_filter".$filter_to_add;
    //clear current selected favs and add the ones we collected
    if ($user_ok === true) {
        //replace 'user' in filter query with log_username
        $filter_to_add = str_replace('user', $log_username, $filter_to_add);
        //store filter in db as usual
        $clear_selected = "DELETE FROM selectedfavorites WHERE user='$log_username'";
        $query_clear = mysqli_query($db_conx, $clear_selected);
        //add linked filters
        $add_linked_filters = "INSERT into selectedfavorites (user, tagname, dateadded) VALUES" . $filter_to_add;
        $query_add = mysqli_query($db_conx, $add_linked_filters);
    }
} else {
    $_SESSION["f1"] = "startup";
}
//end block to include into index.php
?>
