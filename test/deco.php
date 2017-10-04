<?php
include_once("../php_includes/db_conx.php");
if(isset($_POST['cid']) && isset($_POST['type'])){
    if($_POST["pass"] !== "treasuretribe"){
        echo "go away";
        exit();
    }
    $uid = $_POST['cid'];
    $content_type = $_POST['type'];
    if ($content_type === "sound") {
        $db_type = "audio";
        $tag_type = "sound";
    } elseif ($content_type === "video") {
        $db_type = "videos";
        $tag_type = "videos";
    } elseif ($content_type === "image") {
        $db_type = "photostream";
        $tag_type = "images";
    } elseif ($content_type === "article") {
        $db_type = "articles";
        $tag_type = "articles";
    } elseif ($content_type === "comment") {
        $db_type = "comments";
    }

    if ($content_type !== "comment") {
        //reduce tag number apptly
        $sql = "SELECT * FROM " . $db_type . " WHERE uniqueID='$uid' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $tags = array();
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            array_push($tags, $row["tag1"]);
            array_push($tags, $row["tag2"]);
            array_push($tags, $row["tag3"]);
            array_push($tags, $row["tag4"]);
            array_push($tags, $row["tag5"]);
        }
        foreach ($tags as $tag) {
            $update_count = "UPDATE tags SET total=(total-1), ".$tag_type."=(".$tag_type."-1) WHERE name='$tag' LIMIT 1";
            $query = mysqli_query($db_conx, $update_count);
        }

        //delete all associated comments
        $sql = "DELETE FROM comments WHERE content_id='$uid'";
        $query = mysqli_query($db_conx, $sql);
        //delete all associated notifications
        $sql = "DELETE FROM notifications WHERE content_id='$uid' AND content_type='$content_type'";
        $query = mysqli_query($db_conx, $sql);
    }

    //delet actual content
    $sql = "DELETE FROM " . $db_type . " WHERE uniqueID='$uid' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);

    echo "delete_successful";
    exit();
}
?>
<script src='../js/ajax.js'></script>
<script>
    function poofire(){
        var pass = prompt("enter passcode");
        var cid = prompt("enter uid");
        var type = prompt("enter 'article' or 'image' or 'video' or 'sound' or 'comment'");
        
        
        
        var ajax = ajaxObj("POST", "deco.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                alert(ajax.responseText);
            }
        };
        ajax.send("pass="+pass+"&cid="+cid+"&type="+type);
    }
</script>
<button onclick='poofire();'>update</button>