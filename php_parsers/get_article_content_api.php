<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
//include_once("../app_parsers/auth_functions.php");
include_once("../php_includes/check_login_status.php");
//check for json encoded request
/*$headers = getallheaders();
if ($headers["Content-Type"] == "application/json") {
    $_GET = json_decode(file_get_contents("php://input"), true) ? : [];
}
if ($_SERVER['HTTP_REFERER'] === $_SERVER["HTTP_HOST"]) {
    //dont exit
    include_once("check_login_status.php");
} elseif (isset($_GET["username"]) && isset($_GET["id_token"])) {
    if (auth_user($_GET["username"], $_GET["id_token"], $db_conx)) {
        //dont exit      
        //set session variables
        $log_username = $_GET["username"];
        if ($_GET["app_filter_array"] !== "") {
            $app_filter_array = explode(',', $_GET["app_filter_array"]);
        }
    } else {
        $data = json_encode(array("error" => "Invalid Credentials"));
        print_r($data);
        exit();
    }
} else {
    $data = json_encode(array("error" => "Invalid Referer", "Referer" => $_SERVER['HTTP_REFERER'], "HOST" => $_SERVER["HTTP_HOST"]));
    print_r($data);
    exit();
}*/
if(isset($_GET["article_id"])){
    $id = $_GET["article_id"];
    $sql = "SELECT * FROM articles WHERE uniqueID='$id'";
    $query = mysqli_query($db_conx, $sql);
    if(mysqli_num_rows($query) < 1 || $query === false){
        $data = json_encode(array("error" => "article not found"));
        print_r($data);
        exit();
    }
    while($row = mysqli_fetch_array($query)){
        $content = $row["content"];
        $title = $row["title"];
    }     
    $response = array();
    $response["title"] = $title;
    $response["text"] = $content;   
    echo json_encode($response);
    exit();
}else{
    $data = json_encode(array("error" => "please send article id"));
        print_r($data);
        exit();
}
?>
