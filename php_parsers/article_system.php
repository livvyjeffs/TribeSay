<?php
error_reporting(E_ERROR | E_PARSE);
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/checkTags.php");
include_once("../scraping/posting_modules/photo_post.php");
//files for scraping
include_once ("../scraping/main_scraper.php");
//for posting
include_once("../php_includes/image_resize.php");//this could be used as a trait as well


if(isset($_POST['link']) && $_POST['link'] !== ""){
    $link = $_POST['link'];
    //instatiate page to scrape
    $page = new scraped_page($link);
    //check if link is to an image, if so then pull it. if not then scrape it
    $image_test = $page->check_if_image();//don't let this happen for articles
    if($image_test === true){
        exit();
    }
    //parse the url into host, scheme, etc
    $page->parse_url();
    //check against our lists of known hosts
    $page->check_api_or_load();
    //pass control off to trait packages depending on api or load result
    $page->diverge_service_flow();
    //get title
    $page->get_title();
    //check for iframe allowance, define $page->frame;
    $page->check_frame();
    //echo out source array as string, this will go to ajax soon
    $page->pass_back();
    //need to echo back title and image array and display in the uploader html
    //$summary1 = "xxx";  
    //$responseText .= $sourceHTML;
    exit();
}
    //redefine variables to dissalowed content
    //$summary2 = htmlentities($summary);
    
    //THIS PARSER WILL BE SPLIT INTO HALVES. 1 TO AJAX BACK TITLE AND IMAGE OPTIONS FOR USER TO SELECT FROM/EDIT. THIS ONE WILL BE TRIGGERED WITH URL POST FROM UPLOAD FORM
    //                                       2 TO ACCEPT CHOSEN DATA AND SAVE NEW ARTICLE CONTENT TO THE DATABASE. THEN HEADER THEM BACK TO THE USER.PHP REFRESHED. ALSO,
    //                                       3 ALSO SMALL PIC WILL BE SAVED (file get/put contents and imageresize();)
//url, title, imagesrc, description, tags, paragraph.
// only reqs are:
//url, title, tags 
if(isset($_POST['title']) && isset($_POST['source'])){
   
    if($_POST["message"] === "api_ping_successful"){
        $log_username = $_POST["username"];
    }
    //maybe all of this could be JS
    $tag1 = $_POST['tag1'];
    $tag2 = $_POST['tag2'];
    $tag3 = $_POST['tag3'];
    $tag4 = $_POST['tag4'];
    $tag5 = $_POST['tag5'];
    //collect and sanititze variables and put them into db then header.
    $content = htmlentities($_POST['content']);
    $content = stripcslashes($content);
    $content = mysqli_real_escape_string($db_conx, $content);
    $description = htmlentities($_POST['description']);
    $description = stripcslashes($description);
    $description = mysqli_real_escape_string($db_conx, $description);
    $title = htmlentities($_POST['title']);
    $title = stripcslashes($title);
    $title = mysqli_real_escape_string($db_conx, $title);
    $link = $_POST['url'];
    $rgb_r = $_POST["rgb_r"];
    $rgb_g = $_POST["rgb_g"];
    $rgb_b = $_POST["rgb_b"];
    
    //explode the tags...or serparate them as unique url encoded variables
    //importing and saving image so it loads faster
    //get frame_stat
    $frame_stat = $_POST["frame_stat"];
    $postLink = urldecode($_POST['source']);
    $ratio = $_POST["ratio"];
    
    ///
    if ($postLink !== "sourceImagery/spaceholder.jpg") {
        $post_this = new image_to_post($postLink, $log_username, "stream");
        $post_this->remove_url_variables();
        $post_this->make_tmp();
        $post_this->file_get_and_put();
        $post_this->check_size();
        if($post_this->loop_tracker === "exit"){
                $post_this->check_size();
            }
        $post_this->gen_db_name();
        
        $post_this->resize_image();       
        $post_this->move_to_permanent();
        $db_file_name = $post_this->db_file_name;
        
        $post_this->get_img_specs();
        if ($_POST["message"] === "api_ping_successful") {
            $ratio = $post_this->img_ratio;
        }
    } else {
        $db_file_name = $postLink;
    }

    $uniqueID = date("DMjGisY")."".rand(1000,9999);
    //get the article's paragraph text
    $article = new DOMDocument;
    $article ->loadHTMLFile($link);
    $paragraphText = $article->getElementsByTagName("p");
    $summary = "";
    foreach($paragraphText as $paragraph){
        $text = $paragraph->textContent;
        $summary .= $text."<br><br>";
    }
    //
    $parsedArray = parse_url($link);
    $hostName = $parsedArray['host'];
    //
    //check/update tags and add if necessary
    $content_type = "articles";
    
    checkForTag($tag1, $content_type, $db_conx, $log_username);
    checkForTag($tag2, $content_type, $db_conx, $log_username);
    checkForTag($tag3, $content_type, $db_conx, $log_username);
    checkForTag($tag4, $content_type, $db_conx, $log_username);
    checkForTag($tag5, $content_type, $db_conx, $log_username);
    
    //$summary1 = mysqli_real_escape_string($db_conx, $summary);
    //insert new article into database
    $postArticle = "INSERT INTO articles (poster, title, imagesrc, postdate, uniqueID, link, tag1, tag2, tag3, tag4, tag5, description, hostname, frame_stat, ratio, rgb_r, rgb_g, rgb_b, content)
                    VALUES ('$log_username', '$title', '$db_file_name', now(), '$uniqueID', '$link', '$tag1', '$tag2', '$tag3', '$tag4', '$tag5', '$description', '$hostName', '$frame_stat','$ratio', '$rgb_r', '$rgb_g', '$rgb_b', '$content')";
    $query = mysqli_query($db_conx, $postArticle);
    //autoupvote by poster
    
    $log_vote = "INSERT INTO articlevotes (content_id, voter, token, postdate) VALUES ('$uniqueID', '$log_username', 'UP', now())";
    $query = mysqli_query($db_conx, $log_vote);

    //update vote tally in appropriate DB
    $update_count = "UPDATE articles SET vote_state=(vote_state+1) WHERE uniqueID='$uniqueID' LIMIT 1";
    $query = mysqli_query($db_conx, $update_count);

    //internal email notification
    
    
    
    $content_post_message = "Posted by: ".$log_username." at (http://www.tribesay.com/index.php?u=".$uniqueID."&m=article)";
    $headers = 'From: notifications@tribesay.com';
    
    $user_email = get_user_email($db_conx, $log_username);
    if(strpos($user_email,'@japes.com') === false){
        mail("martin@tribesay.com, olivia@tribesay.com, jp@tribesay.com", "New Content Posted", $content_post_message,$headers);
    }

    mysqli_close($db_conx);
    echo "success||";
    //echo "stream/".$log_username."/".$post_this->db_file_name;
    echo $uniqueID;
    echo "||";
    exit();
}



?>