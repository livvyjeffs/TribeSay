<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
if(isset($_POST["article_id"])){
    $id = $_POST["article_id"];
    $sql = "SELECT * FROM articles WHERE uniqueID='$id'";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query)){
        $content = $row["content"];
        $title = $row["title"];
    }
    //$content = html_entity_decode($content);
    //$content = nl2br($content);       
    $response = array();
    $response["title"] = $title;
    $response["text"] = $content;
    
    echo json_encode($response);
    exit();
}
?>
