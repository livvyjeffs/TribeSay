<?php
include_once("../php_includes/db_conx.php");
if (isset($_POST["pass"])) {
    if ($_POST["pass"] === "japes") {
        $cid = $_POST['cid'];
        $type = $_POST['type'];
        switch ($type) {
            case 'article':
                $db = "articles";
                break;
            case 'image':
                $db = "photostream";
                break;
            case 'video':
                $db = "videos";
                break;
            case 'sound':
                $db = "audio";
                break;
            case 'comment':
                $db = "comments";
                break;
            default:
                echo "that type is not valid";
                exit();
        }
        $vs = intval($_POST['vs']);
        if ($_POST["jibbler"] === "y") {
            
            //get post data
            $sql = "SELECT poster FROM " . $db . " WHERE uniqueID='$cid' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
            $row = mysqli_fetch_row($query);
            $original_poster = $row[0];
                       
            //get a list of posters already voted on this
            $sql = "SELECT poster FROM notifications WHERE receiver='$original_poster' AND content_id='$cid' AND content_type='$type'";
            $query = mysqli_query($db_conx, $sql);
            $used_posters = array();
            while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
                array_push($used_posters, $row["poster"]);
            }
            
            //get list of vs number of @japes usernames
            $username_array = array();
            $sql = "SELECT username FROM users WHERE email LIKE '%@japes.com' ORDER BY RAND()";
            $query = mysqli_query($db_conx, $sql);
            $i=0;
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if(!in_array($row["username"], $used_posters)){
                    array_push($username_array, $row["username"]);
                    $i++;
                }
                if($i>=$vs){break;}
            }
            //print_r($username_array);
            //exit();
            //generate matching list of dates
            $time_ago_array = array();
            $current = date('Y-m-d H:i:s');
            $date_c = date_create($current);
            for ($i = 0; $i < count($username_array); $i++) {
                $rand = rand(0, 15);
                $new = date_sub($date_c, date_interval_create_from_date_string($rand . ' minutes'));
                $date = date_format($new, 'Y-m-d H:i:s');
                array_push($time_ago_array, $date);
            }
            //combine username and time ago arrays
            $note_array = array_combine($username_array, $time_ago_array);
            //insert notifications
            foreach ($note_array as $username => $date) {
                $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
                $sql = "INSERT INTO notifications (did_read, poster, receiver, content_id, content_type, post_date, category, uniqueID)
                VALUES('0', '$username', '$original_poster', '$cid', '$type', '$date', 'vote', '$uniqueID')";
                $query = mysqli_query($db_conx, $sql);
            }
        }
        $sql = "UPDATE " . $db . " SET vote_state=(vote_state+'$vs') WHERE uniqueID='$cid' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        //check if update occured
        $sql = "SELECT * FROM " . $db . " WHERE vote_state>='$vs' AND uniqueID='$cid'";
        $query = mysqli_query($db_conx, $sql);
        $num_rows = mysqli_num_rows($query);
        if ($num_rows > 0) {
            echo "success";
        } else {
            echo "you failed for some reason";
            exit();
        }
    } else {
        echo "get out";
    }
    exit();
}
?>
<script src='../js/ajax.js'></script>
<script>
    function poofire(){
        var pass = prompt("enter passcode");
        var cid = prompt("enter cid");
        var type = prompt("enter 'article' or 'image' or 'video' or 'sound' or 'comment'");
        var jibbler = prompt("other jibbler? y or n");
        var vs = prompt("enter vs");
        
        
        
        var ajax = ajaxObj("POST", "mod_vs.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                alert(ajax.responseText);
            }
        };
        ajax.send("pass="+pass+"&cid="+cid+"&type="+type+"&vs="+vs+"&jibbler="+jibbler);
    }
</script>
<button onclick='poofire();'>update</button>