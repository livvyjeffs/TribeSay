<?php
include_once("php_includes/check_login_status.php");
if(isset($_POST['friendUsername']) && $_POST['friendUsername'] !== ""){
    $friendUsername = $_POST['friendUsername'];
    $strLength = strlen($friendUsername);
    $partialStr = substr($friendUsername, 0, $strLength);
    $matchingFriends = array();
    //query db for all users
    $sql = "SELECT username,avatar FROM users";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $username = $row['username'];
        $avatar = $row['avatar'];
        if($partialStr === substr($username, 0, $strLength)){
            $friend = array($username, $avatar);
            array_push($matchingFriends, $friend);
        }
    }
    if(count($matchingFriends !== 0)){
        $outputFriends = "";
        foreach($matchingFriends as $match){
            $outputFriends .= '<img src="'.$s3root.'/user/'.$match[0].'/'.$match[1].'" alt="'.$match[0].'" title="'.$match[0].'">';
        }
        echo $outputFriends;
        exit();
    }else{
        echo "failed";
        exit();
    }
}elseif(isset($_POST['friendUsername']) && $_POST['friendUsername'] === ""){
    echo "failed";
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script>
            function friendFinder(){
                var username = _("friendsUsername").value;
                _("status").innerHTML = "loading";
                var ajax = new ajaxObj("POST", "friendSearchBackEnd.php");
                ajax.onreadystatechange = function(){
                    if(ajaxReturn(ajax) === true){
                        if(ajax.responseText === "failed"){
                            _("status").innerHTML = "no members match that username";
                            _("friendOutput").innerHTML = "";
                        }else{
                            _("status").innerHTML = "";
                            _("friendOutput").innerHTML = ajax.responseText;
                        }
                    }
                };
                ajax.send("friendUsername=" + username);
            }
        </script>
    </head>
    <body>
        <form>
            <h4>enter username here:</h4>
            <input type="text" id="friendsUsername" onkeyup="friendFinder();">
            <span id="status"></span>
        </form>
        <div id="friendOutput"></div>
    </body>
</html>