<?php
/*
require_once("./php_includes/db_conx.php");
//this block deletes all accounts that do not activate after 3 days...signup<=CURRENT_DATE - INTERVAL 3 DAY AND 
$sql = "SELECT id, username FROM users WHERE activated='0'";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
//test of eval with echo
    echo "this is working";
if($numrows > 0){
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $id = $row['id'];
        $username = $row['username'];
        $userFolder = "./user/$username";
        if(is_dir($userFolder)){
            rmdir($userFolder);
        }
        $query = mysqli_query($db_conx, "DELETE FROM users WHERE id='$id' AND username='$username' AND activated='0' LIMIT 1");
        $query = mysqli_query($db_conx, "DELETE FROM useroptions WHERE username='$username' LIMIT 1");
    }
}
 */
?>
