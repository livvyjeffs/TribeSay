<?php
function checkForTag($tagName, $content_type, $db_conx, $log_username){
    $sql = "SELECT * FROM tags WHERE name='$tagName' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    if(mysqli_num_rows($query) < 1){
        $sql = "INSERT INTO tags (name, creationdate, createdby, ".$content_type.", total)
                VALUES ('$tagName', now(), '$log_username', 1, 1)";
        $query = mysqli_query($db_conx, $sql);
    }elseif($content_type === "articles"){
        $sql = "UPDATE tags SET articles=(articles+1), total=(total+1) WHERE name='$tagName' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
    }elseif($content_type === "images"){
        $sql = "UPDATE tags SET images=(images+1), total=(total+1) WHERE name='$tagName' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
    }elseif($content_type === "videos"){
        $sql = "UPDATE tags SET videos=(videos+1), total=(total+1) WHERE name='$tagName' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
    }elseif($content_type === "sound"){
        $sql = "UPDATE tags SET sound=(sound+1), total=(total+1) WHERE name='$tagName' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
    }
}
?>
