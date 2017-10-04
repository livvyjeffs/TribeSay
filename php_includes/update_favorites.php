<?php
include_once("check_login_status.php");
if(isset($_POST['tagname'])){
    $tagName = $_POST['tagname'];
    //$sql = "SELECT * FROM userfavorites WHERE user='$log_username' AND tagname='$tagName' LIMIT 1";
    //$query = mysqli_query($db_conx, $sql);
    //if(mysqli_num_rows($query) < 1){
        $sql = "INSERT INTO userfavorites (user, tagname, dateadded)
                VALUES ('$log_username', '$tagName', now())";
        $query = mysqli_query($db_conx, $sql);
//}
    echo $tagName." added to favorites".$log_username;
    exit();
}if(isset($_POST['selectedtagname'])){
    $tagName = $_POST['selectedtagname'];
    //$sql = "SELECT * FROM userfavorites WHERE user='$log_username' AND tagname='$tagName' LIMIT 1";
    //$query = mysqli_query($db_conx, $sql);
    //if(mysqli_num_rows($query) < 1){
        $sql = "INSERT INTO selectedfavorites (user, tagname, dateadded)
                VALUES ('$log_username', '$tagName', now())";
        $query = mysqli_query($db_conx, $sql);
//}
    echo $tagName." added to selected favorites".$log_username;
    exit();
}if(isset($_POST['tagToRemove'])){
    $tagName = $_POST['tagToRemove'];
    $sql = "DELETE FROM userfavorites WHERE user='$log_username' AND tagname='$tagName' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    echo "deletion complete nuggah";
    exit();
}if(isset($_POST['tagToDeselect'])){
    $tagName = $_POST['tagToDeselect'];
    $sql = "DELETE FROM selectedfavorites WHERE user='$log_username' AND tagname='$tagName' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    echo $tagName;
    exit();
}if(isset($_POST['select_all'])){
    //get an array of all of the current users' favorites
    $sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    $favorites_array = array();
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        array_push($favorites_array, $row['tagname']);
    }
    //clear all of the users' current selected favorites
    $sql = "DELETE FROM selectedfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    //add all tagnames in the favorites_array to selectedfavorites
    $responseText = "";
    foreach($favorites_array as $tag){
        $sql = "INSERT INTO selectedfavorites (user, tagname, dateadded)
                                       VALUES ('$log_username', '$tag', now())";
        $query = mysqli_query($db_conx, $sql);
        $responseText .= $tag."||";
    }
    $responseText = substr($responseText, 0, -2);
    echo $responseText;
    exit();
}if(isset($_POST['clear_all'])){
    //clear all of the users' current selected favorites
    $sql = "DELETE FROM selectedfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    echo "success";
    exit();
}if(isset($_POST['get_all_favs'])){
    //declare favs list
    $favs = "";
    //clear all of the users' current selected favorites
    $sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $name = $row["tagname"];
        $favs .= $name.",";
    }
    rtrim($favs, ",");
    echo $favs;
    exit();
}if (isset($_POST['add_remove'])) {
    //clear all of the users' current selected favorites
    $sql = "DELETE FROM selectedfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    $tagName = $_POST['add_remove'];
    $sql = "INSERT INTO selectedfavorites (user, tagname, dateadded)
                VALUES ('$log_username', '$tagName', now())";
    $query = mysqli_query($db_conx, $sql);
    echo "success";
    exit();
}
?>
