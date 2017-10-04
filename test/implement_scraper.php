<?php
//SCRAPER IMPLEMENTATION FRAMEWORK
error_reporting(E_ERROR | E_PARSE);
//necessary files
include_once ("../scraping/main_scraper.php");
include_once ("../scraping/api_modules/main_api_scrape.php");
include_once ("../scraping/load_html_modules/main_load_scrape.php");
if(isset($_POST["url"])){
    //instatiate page to scrape
    $page = new scraped_page($_POST["url"]);
    //check if link is to an image, if so then pull it. if not then scrape it
    $image_test = $page->check_if_image();
    if($image_test === true){
        echo "image test returned true";
        exit();
    }else{
        //parse the url into host, scheme, etc
        $page->parse_url();
        //check against our lists of known hosts
        $page->check_api_or_load();
        //pass control off to trait packages depending on api or load result
        $page->diverge_service_flow();
        //echo out source array as string, this will go to ajax soon
        $page->pass_back();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="../js/ajax.js"></script>
        <script src="../scraping/api_modules/js_scraping_services.js"></script>
        <script>
            //ajax implementation
            function scrape(){
                var url = document.getElementById("url").value;
                var ajax = ajaxObj("POST","implement_scraper.php");
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
            <input type="button" onclick="scrape();" value="scrape">
        </form>
        <div id="pictures"></div>
    </body>
</html>