<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
if(isset($_POST["video_id"])){
    $id = $_POST["video_id"];
    $sql = "SELECT * FROM videos WHERE uniqueID='$id' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query)){
        $html = $row["videoHTML"];
        $title = $row["title"];
    }  
    $response = array();
    $response["title"] = $title;
    $response["html"] = nl2br(html_entity_decode($html));
    echo json_encode($response);
    exit();
}
?>
