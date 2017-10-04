<?php
//WARNING ONLY RUN THIS ONCE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! THEN COMMENT OUT THE CODE
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/db_conx.php");
$array = array();
$sql = "SELECT * FROM users";
$query = mysqli_query($db_conx, $sql);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $username = $row['username'];
    array_push($array, $username);
}
print_r($array);
/*foreach($array as $username){
    $sql = "INSERT INTO userfavorites (user, tagname, dateadded)
                VALUES ('$username', 'business', now()),
                 ('$username', 'edm', now()),
                 ('$username', 'funny', now())";
        $query = mysqli_query($db_conx, $sql);
        echo "selections added to ".$username.".<br>";
}*/
?>
