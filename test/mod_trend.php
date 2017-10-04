<?php
include_once("../php_includes/db_conx.php");
if(isset($_POST["pass"])){
    if($_POST["pass"] === "japes"){
        $one = $_POST['one'];
        $two = $_POST['two'];
        $three = $_POST['three'];
        $sql = "UPDATE trending SET one='$one', two='$two', three='$three'";
        $query = mysqli_query($db_conx, $sql);
        echo "success";
    }else{
        echo "get out";
    }
    exit();
}
?>
<script src='../js/ajax.js'></script>
<script>
    function fire(){
        var pass = prompt("input passcode");
        var one = prompt("first");
        var two = prompt("second");
        var three = prompt('third');
        
        var ajax = ajaxObj("POST", "mod_trend.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                alert(ajax.responseText);
            }
        };
        ajax.send("pass="+pass+"&one="+one+"&two="+two+"&three="+three);
    }
</script>
<button onclick="fire();">Don't you dare press this button</button>