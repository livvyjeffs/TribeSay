<?php
if(isset($_POST["toggle_notif"])){
    switch($_POST["toggle_notif"]){
        case 'activate':
            $state = 1;
            break;
        case 'deactivate':
            $state = 0;
            break;
        default:
            echo "invalid argument";
            exit();
    }
    $sql = "UPDATE users SET activated='$state' WHERE username='$logusername' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    if($query !== false){
        echo "success";
    }else{
        echo "failure";
    }
    exit();
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
    var toggle_notif = function(state){//can be 'activate' or 'deactivate'
        var ajax = ajaxObj("POST", "php_parsers/toggle_notifications.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                if(ajax.responseText === "success"){
                    //confirm that change has been made
                }else{
                    //alert user that save has failed and to please try again
                }
            }
        };
        ajax.send("toggle_notif="+state);
    };
</script>