<?php
session_start();
$filter_array = array("f1","f2","f3","f4","f5");
if(isset($_POST["add_tag"])){
    foreach($filter_array as $f){
        if(!isset($_SESSION[$f])){
            $_SESSION[$f] = $_POST["add_tag"];
            break;
        }
    }
    echo "added tag to filter";
}elseif(isset($_POST["remove_tag"])){
    foreach ($filter_array as $f) {
        if (isset($_SESSION[$f])) {
            if ($_SESSION[$f] === $_POST["remove_tag"]) {
                unset($_SESSION[$f]);
                break;
            }
        }
    }
    echo "removed tag from filter";
}elseif(isset($_POST["clear_all"])){
    foreach ($filter_array as $f) {
        if (isset($_SESSION[$f])) {
            unset($_SESSION[$f]);
        }
    }
    echo "cleared all from filter";
}else{
    echo "invalid POST variable";
}
//test session vars
/*foreach($filter_array as $f){
    if(isset($_SESSION[$f])){
        echo $f." = ".$_SESSION[$f]." ";
    }else{
        echo $f." not set ";
    }
}*/
//mobile filter
if(isset($_POST["mobile_filter"])){
    $_SESSION["f1"] = $_POST["mobile_filter"];
}
exit();
?>
