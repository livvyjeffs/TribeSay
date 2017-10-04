<?php
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *');
error_reporting(E_ERROR | E_PARSE);
?><?php
//AJAX calls this login code to execute
if (isset($_GET["e"]) && $_GET["p"]) {
    //connect to the database
    include_once("../php_includes/db_conx.php");
    //gather the posted data into local variables and sanitize
    $e = mysqli_real_escape_string($db_conx, $_GET['e']);
    //get salt form db
    $p = $_GET['p'];
    //get user ip address
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
    //form data error handling
    if ($e === "" || $p === "") {
        $data = json_encode(array("error"=>"invalide request"));
        print_r($data);
        exit();
    } else {
        //end form data error handling
        $sql = "SELECT id, username, password, salt FROM users WHERE (email='$e' AND activated='1') OR (username='$e' AND activated='1') LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $num_row = mysqli_num_rows($query);
        if($num_row < 1){
            $data = json_encode(array("error" => "user not found"));
            print_r($data);
            exit();
        }
        $row = mysqli_fetch_row($query);
        $db_id = $row[0];
        $db_username = $row[1];
        $db_pass_str = $row[2];
        $db_salt = $row[3];
        if($db_pass_str === "facebook"){
            $data = json_encode(array("error" => "facebook login not supported yet"));
            print_r($data);
            exit();
        } elseif ($db_salt === "reset") {
            $data = json_encode(array("error" => "password must be reset"));
            print_r($data);
            exit();
        }
        $p = hash("sha512", $db_salt . $p);
        if ($p !== $db_pass_str) {
            //echo "salt: ".$db_salt." P: ".$p;
            echo "Incorrect Password";
            exit();
        } else {
            //generate id token and save new client credentials
            $id_token = rand(10000000, 99999999); //update this for more randomness
            //$salt = mcrypt_create_iv(16, MCRYPT_DEV_RANDOM);
            //$id_hash = hash("sha512", $salt . $id_token);
            //clear any previously existing entries for this user
            $sql = "DELETE FROM app_clients WHERE username='$db_username'";
            $query = mysqli_query($db_conx, $sql);
            //insert new entry with updated id_hash and salt
            $sql = "INSERT INTO app_clients(username, id_hash) VALUES('$db_username', '$id_token')";
            $query = mysqli_query($db_conx, $sql);
            //return successful 
            $data = json_encode(array("error" => "no_error", "username"=>$db_username,"id_token"=>$id_token));//if too slow, skip hashing and just store id_token directly and authenticate directly
            print_r($data);
            exit();
        }
    }
}
?>