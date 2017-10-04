<?php

include_once("../scraping/posting_modules/photo_post.php");
if (isset($_POST["post_link"])) {
    $post_this = new image_to_post($_POST["post_link"], "martinman", "stream");
    $post_this->remove_url_variables();
    echo $post_this->filenameIn;
    $post_this->make_tmp();
    $post_this->file_get_and_put();
    echo $post_this->fileSize;
    $post_this->check_size();
    echo $post_this->loop_tracker;
    if ($post_this->loop_tracker === "exit") {
        $post_this->check_size();
    }
    $post_this->gen_db_name();
    echo $post_this->fileExt;
    echo $post_this->db_file_name;
    $post_this->resize_image();
    
    $post_this->move_to_permanent();
    $db_file_name = $post_this->db_file_name;
}
?>
<!DOCUMENT html>
<html>
    <head>
        <script src="../js/ajax.js"></script>
        <script>
            var post = function(){
                var link = document.getElementById("link").value;
                alert(link);
                var ajax = ajaxObj("POST", "image_ul_test.php");
                ajax.onreadystatechange = function(){
                    if(ajaxReturn(ajax) === true){
                        
                    }
                };
                ajax.send("post_link="+link);
            };
        </script>
    </head>
    <body>
        <input type="text" id="link">
        <br>
        <button onclick="post();">submit</button>
    </body>
</html>