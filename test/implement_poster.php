<?php
include_once("../scraping/posting_modules/photo_post.php");
error_reporting(E_ERROR | E_PARSE);
if (isset($_POST["url"])) {
    $postLink = urldecode($_POST['url']);
    echo "postLink: ".$postLink."<br>";
    
    if (!file_exists("/tmp")) {
        mkdir("/tmp", 0755);
    }

    $newLink = explode("?", $postLink);
    $postLink = $newLink[0];
    
    $get = file_get_contents($postLink);
    //echo "get: ".$get."<br>";
    
    //$base = "/tmp/" . basename($postLink); //needs to be tested for proper name type
    $base = "/tmp/test_image_06.jpg";
    echo "base: ".$base."<br>";
    
    $put = file_put_contents($base, $get);
    echo "put: ".$put."<br>";
    
    $ext = pathinfo($put);
    print_r($ext);
    echo "<br>";
    
    $moveResult = rename($base, $s3root."/stream/martin/test_image_06.jpg");
    
    echo "<img src='/tmp/snicker.png' alt='none'>";
    
    exit();

    /*
    $post_this = new image_to_post($postLink, "martin");
    $post_this->process_url(); //folded remove url var into here
    $post_this->make_tmp();
    $post_this->file_get_and_put();
    $post_this->check_size();
    $post_this->gen_db_name();
    $post_this->move_to_permanent();
    $post_this->resize_image();
     */
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="../js/ajax.js"></script>
        <script src="../scraping/api_modules/js_scraping_services.js"></script>
        <script>
            //ajax implementation
            function post(){
                //var url = document.getElementById("url").value;
                //var url = "http://s1.reutersmedia.net/resources/r/?m=02&d=20131205&t=2&i=817503382&w=&fh=&fw=&ll=580&pl=378&r=CBRE9B401AG00";
                //var url = "http://www.wired.com/images_blogs/design/2014/02/xstat-01-660x495.jpg";
                var url = "http://storage.canoe.ca/v1/dynamic_resize/sws_path/suns-prod-images/1297497090862_ORIGINAL.jpg?quality=80&size=420x";
                url = encodeURIComponent(url);
                var ajax = ajaxObj("POST","implement_poster.php");
                ajax.onreadystatechange = function (){
                    if(ajaxReturn(ajax) === true){
                       ////console.log(ajax.responseText);          
                       document.getElementById("pictures").innerHTML = ajax.responseText;
                    }
                };
                ajax.send("url="+url);
            }
        </script>
    </head>
    <body>
        <form>
            <input id="url" type="url">
            <input type="button" onclick="post();" value="post">
        </form>
        <div id="pictures"></div>
    </body>
</html>