<?php

include_once("../../php_includes/check_login_status.php");
//receive posted variables
if (isset($_POST["qs"]) || false) {
    //collect posted variables
    $qs = preg_replace('#[^a-z0-9]#i', '', $_POST["qs"]); 
    //Declare databases to cycle through
    $dbs = array("videos"=>"title","articles"=>"title,content","photostream"=>"description","audio"=>"title");
    //full test search should query titles of all 4 media types and body of article
    $result_array = array();
    foreach ($dbs as $db => $cols) {
        if (!($a_stmt = $mysqli->prepare("SELECT uniqueID, title, MATCH (".$cols.") 
      AGAINST (?) 
      AS score FROM ".$db." WHERE MATCH (".$cols.") 
      AGAINST (?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        //bind query string
        if (!$a_stmt->bind_param("ss", $qs, $qs)) {
            echo "Binding parameters failed: (" . $a_stmt->errno . ") " . $a_stmt->error;
        }
        //execute query
        if (!$a_stmt->execute()) {
            echo "Execute failed: (" . $a_stmt->errno . ") " . $a_stmt->error;
        }
        //get results
        if (!($res = $a_stmt->get_result())) {
            echo "Getting result set failed: (" . $a_stmt->errno . ") " . $a_stmt->error;
        }
        //cycle through results
        for ($row_no = ($res->num_rows - 1); $row_no >= 0; $row_no--) {
            $res->data_seek($row_no);
            $row = $res->fetch_assoc();
            $row["type"] = $db;
            array_push($result_array, $row);
        }
        $res->close();
        $a_stmt->close();
    }
    echo json_encode($result_array);
    exit();
}
?>
<script src="/js/ajax.js"></script>
<script>
    //testing post above
    var search = function(){
        var qs = document.getElementById("query_string").value;
        var ajax = ajaxObj("POST","full_text_search.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) === true){
                alert(ajax.responseText);
                var json = JSON.parse(ajax.responseText);
                console.log(json);
                document.getElementById("output").innerHTML = JSON.stringify(json);
            }
        };
        ajax.send("qs="+qs);
    };
</script>
<input type="text" id="query_string">
<br>
<button onclick='search();'>Search</button>
<br>
<div id='output'></div>
