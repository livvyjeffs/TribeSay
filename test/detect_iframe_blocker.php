<?php
if(isset($_POST["url"])){
    $url = $_POST["url"];
    //get external html
    $page = file_get_contents($url);
    //check for blocker
    $pattern1 = "/window\.self.{1,10}window\.top/";
    $s1 = preg_match($pattern1, $page);
    //check for blocker2
    $pattern2 = "/window\.top.{1,10}window\.self/";
    $s2 = preg_match($pattern2, $page);
    //condition response
    if($s1===1||$s2===1){
        $html = file_get_contents($url);
        //$html = preg_replace("/if (window.top !== window.self) window.top.location = window.self.location.href;/", "", $html);
        echo $html;
    }else{
        echo "not_blocked";
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="../js/ajax.js"></script>
        <script>
            function check(e){
                e.preventDefault();
                var url = document.getElementById("url").value;
                var ajax = ajaxObj("POST", "detect_iframe_blocker.php");
                ajax.onreadystatechange = function(){
                    if(ajaxReturn(ajax) === true){
                        alert(ajax.responseText);
                        if(ajax.responseText !== "not_blocked"){                          
                            var ifrm = document.getElementById('myFrame');
                            ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;
                            ifrm.document.open();
                            ifrm.document.write(ajax.responseText);
                            ifrm.document.close();
                        }else if (ajax.responseText === "not_blocked") {
                            document.getElementById("frame").innerHTML = "<iframe style='height: 900px; width: 1500px;' src='" + url + "'>";
                        }
                     }
                };
                ajax.send("url=" + url);
            }   
            // window.self !== window.top - check for this using regex
            function inspect_frame() {
                var frame = document.getElementById("frame");
                alert(frame.innerHTML);
            }
        </script>
    </head>
    <body onload="">
        <form>
            <input type="URL" id="url">
            <input type="button" onclick="check(event);" value="check">
            <input type="button" onclick="inspect_frame();" value="inspect">
            <div id="frame"><iframe id="myFrame"></iframe></div>
        </form>
    </body>
</html>