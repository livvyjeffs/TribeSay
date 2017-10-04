<?php
error_reporting(E_ERROR | E_PARSE);
include_once('../php_includes/check_login_status.php');
include_once("../php_includes/checkTags.php");
include_once("../scraping/posting_modules/photo_post.php");
//files for scraping
include_once ("../scraping/main_scraper.php");
//for posting
include_once("../php_includes/image_resize.php");//this could be used as a trait as well
?><?php 
if (isset($_POST["show"]) && $_POST["show"] == "galpics"){
	$picstring = "";
	$gallery = preg_replace('#[^a-z 0-9,]#i', '', $_POST["gallery"]);
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST["user"]);
	$sql = "SELECT * FROM photos WHERE user='$user' AND gallery='$gallery' ORDER BY uploaddate ASC";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$id = $row["id"];
		$filename = $row["filename"];
		$description = $row["description"];
		$uploaddate = $row["uploaddate"];
		$picstring .= "$id|$filename|$description|$uploaddate|||";
    }
	mysqli_close($db_conx);
	$picstring = trim($picstring, "|||");
	echo $picstring;
	exit();
}
?><?php
if($user_ok !== true || $log_username == "") {
	exit();
}
?><?php 
if (isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["tmp_name"] != ""){
	$fileName = $_FILES["avatar"]["name"];
    $fileTmpLoc = $_FILES["avatar"]["tmp_name"];
    $fileType = $_FILES["avatar"]["type"];
    $fileSize = $_FILES["avatar"]["size"];
    $fileErrorMsg = $_FILES["avatar"]["error"];
    $kaboom = explode(".", $fileName);
    $fileExt = end($kaboom);
    list($width, $height) = getimagesize($fileTmpLoc);
    $db_file_name = rand(100000000000, 999999999999) . "." . $fileExt;
    if ($fileSize > (1048576) * 6) {
        header("location: /message.php?msg=ERROR: Your image file was larger than 3mb");
        exit();
    } else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName)) {
        header("location: /message.php?msg=ERROR: Your image file was not jpg, gif or png type");
        exit();
    } else if ($fileErrorMsg == 1) {
        header("location: ../message.php?msg=ERROR: An unknown error occurred");
        exit();
    } elseif ($width < 10 || $height < 10) {
        header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();
    }
    //if (!file_exists("../user/$log_username")) { mkdir("../user/$log_username", 0755); }
	/*$moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name");
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}*/
	include_once("../php_includes/image_resize.php");
	$target_file = $fileTmpLoc;
	$resized_file = $fileTmpLoc;
	$wmax = 200;
	$hmax = 300;
	img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
        
        $bucket = 'TribeSay_images';
        //create client
        $client = Aws\S3\S3Client::factory(array(
                    'key' => 'AKIAI3E72U4J2Q3264AA',
                    'secret' => '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'
        ));
        //upload an object
        $result = $client->putObject(array(
            'Bucket' => $bucket,
            'Key' => 'user/'.$log_username."/".$db_file_name,
            'SourceFile' => $resized_file
        ));
        //$db_file_name = $result['ObjectURL'];
        $img = imagecreatefromjpeg($target_file);//also detect type and and png and gif
        $h = imagesy($img);
        $w = imagesx($img);
        $ratio = $h/$w;
        
        //
	$sql = "UPDATE users SET avatar='$db_file_name', ratio='$ratio' WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	mysqli_close($db_conx);
	header("location: ../settings.php");
	exit();
}
?><?php 
if (isset($_FILES["photo"]["name"]) && isset($_POST["gallery"])){
	$sql = "SELECT COUNT(id) FROM photos WHERE user='$log_username'";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	if($row[0] > 14){
		header("location: /message.php?msg=The demo system allows only 15 pictures total");
        exit();	
	}
	$gallery = preg_replace('#[^a-z 0-9,]#i', '', $_POST["gallery"]);
	$fileName = $_FILES["photo"]["name"];
        $fileTmpLoc = $_FILES["photo"]["tmp_name"];
	$fileType = $_FILES["photo"]["type"];
	$fileSize = $_FILES["photo"]["size"];
	$fileErrorMsg = $_FILES["photo"]["error"];
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	$db_file_name = date("DMjGisY")."".rand(1000,9999).".".$fileExt; // WedFeb272120452013RAND.jpg
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: /message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	if($fileSize > 1048576) {
		header("location: /message.php?msg=ERROR: Your image file was larger than 1mb");
		exit();	
	} else if (!preg_match("/\.(gif|jpg|png)$/i", $fileName) ) {
		header("location: /message.php?msg=ERROR: Your image file was not jpg, gif or png type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: /message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$moveResult = move_uploaded_file($fileTmpLoc, $s3root."/user/$log_username/$db_file_name");
	if ($moveResult != true) {
		header("location: /message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("../php_includes/image_resize.php");
	$wmax = 800;
	$hmax = 600;
	if($width > $wmax || $height > $hmax){
		$target_file = $s3root."/user/$log_username/$db_file_name";
	    $resized_file = $s3root."/user/$log_username/$db_file_name";
		img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	}
	$sql = "INSERT INTO photos(user, gallery, filename, uploaddate) VALUES ('$log_username','$gallery','$db_file_name',now())";
	$query = mysqli_query($db_conx, $sql);
	mysqli_close($db_conx);
	header("location: /photos.php?u=$log_username");
	exit();
}
?><?php 
if (isset($_POST["delete"]) && $_POST["id"] != ""){
	$id = preg_replace('#[^0-9]#', '', $_POST["id"]);
	$query = mysqli_query($db_conx, "SELECT user, filename FROM photos WHERE id='$id' LIMIT 1");
	$row = mysqli_fetch_row($query);
    $user = $row[0];
	$filename = $row[1];
	if($user == $log_username){
		$picurl = $s3root."/user/$log_username/$filename"; 
	    if (file_exists($picurl)) {
			unlink($picurl);
			$sql = "DELETE FROM photos WHERE id='$id' LIMIT 1";
	        $query = mysqli_query($db_conx, $sql);
		}
	}
	mysqli_close($db_conx);
	echo "deleted_ok";
	exit();
}
?><?php 
//Runs if user uploads new stream item 
if (isset($_FILES["streamItem"]["name"]) && $_FILES["streamItem"]["tmp_name"] != ""){
        //collect uploaded file parameters into local variables
	$fileName = $_FILES["streamItem"]["name"];
        $fileTmpLoc = $_FILES["streamItem"]["tmp_name"];
	$fileType = $_FILES["streamItem"]["type"];
	$fileSize = $_FILES["streamItem"]["size"];
	$fileErrorMsg = $_FILES["streamItem"]["error"];
        //separate $filename inot pieces and collect file ext into variable
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
        //collect size parameters into array elements and check for minumum size
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: /message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
        //assign image a novel, unique filename
	$db_file_name = date("DMjGisY")."".rand(1000,9999).".".$fileExt; // WedFeb272120452013RAND.jpg
        //check that image isn't too large
	if($fileSize > 1048576) {
		header("location: ../message.php?msg=ERROR: Your image file was larger than 1mb");
		exit();	
	}
        //check that image is of appropriate filetype
        else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName) ) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
        //this code moves uploaded file into avatar locatio, we just want to add it...so it actaully still works for us...
	$moveResult = move_uploaded_file($fileTmpLoc, $s3root."/stream/$log_username/$db_file_name");
	if ($moveResult !== true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
        //resize images to within reasonable parameters
	include_once("../php_includes/image_resize.php");
	$target_file = $s3root."/stream/$log_username/$db_file_name";
	$resized_file = $s3root."/stream/$log_username/$db_file_name";
	$wmax = 200;
	$hmax = 300;
	img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
        $uniqueID = date("DMjGisY")."".rand(1000,9999);
        //update database with link to new image file
	$sql = "INSERT INTO photostream (user, filename, uniqueID, uploaddate)
                                  VALUES('$log_username', '$db_file_name', '$uniqueID', now())";
	$query = mysqli_query($db_conx, $sql);
	mysqli_close($db_conx);
        //header people to refreshed profile page
	header("location: ../index.php?p=$log_username");
	exit();
}
?><?php
if(isset($_POST['imagesLink']) && $_POST['imagesLink'] !== ""){
    //instatiate page to scrape
    $page = new scraped_page($_POST["imagesLink"]);
    //check if link is to an image, if so then pull it. if not then scrape it
    $image_test = $page->check_if_image();
    if($image_test === true){
        exit();
    }
    //parse the url into host, scheme, etc
    $page->parse_url();
    //check against our lists of known hosts
    $page->check_api_or_load();
    //pass control off to trait packages depending on api or load result
    $page->diverge_service_flow();
    //echo out source array as string, this will go to ajax soon
    $page->pass_back();
    exit();
}
//old scraping code
/*if(isset($_POST['imagesLink']) && $_POST['imagesLink'] !== ""){
    $sourceHTML = "";
    $responseText = "";
    //extract relevant article info from link
    $sourceArray = array();
    $sizeArray = array();
    $link = urldecode($_POST['imagesLink']);
    //if((strpos($link, ".jpg") !== false) || (strpos($link, ".png") !== false) || (strpos($link, ".gif") !== false)){
    if(exif_imagetype($link) !== false){
        $id = 'image0';
        $source = $link;
        $sourceHTML .= '<img id="'.$id.'" class="notSelectedPicture" style="visibility:hidden" src="'.$source.'" onclick="toggleSelectedPicture(\''.$id.'\');" alt="alt">';
    }else{
        //generate new DOMdoc
        $article = new DOMDocument;
        $article ->loadHTMLFile($link);
        $parsedURL = parse_url($link);
        $host = $parsedURL['host'];
        $scheme = $parsedURL['scheme'];
        //get the largest image
        $images = $article->getElementsByTagName("img");
        foreach($images as $image){
            $source = $image->getAttribute("src");
            if($source[0] === "/" && $source[1] !== '/'){
                $source = $scheme."://".$host.$source;
            }elseif($source[0] === "/" && $source[1]==='/'){
                $source = $scheme.":".$source;
            }
            if(strpos($source, "http://") !== false || strpos($source, "https://") !== false){
                $sizeProfile = getimagesize($source);
                $imgArea = $sizeProfile[0] * $sizeProfile[1];
                if($imgArea > 100){
                    array_push($sizeArray, $imgArea);
                    array_push($sourceArray, $source);
                }
            }
        }   
        array_multisort($sizeArray, SORT_DESC, $sourceArray);
        $i = 0;
        foreach($sourceArray as $source){
            $id = 'image'.$i;
            $sourceHTML .= '<img id="'.$id.'" class="notSelectedPicture" src="'.$source.'" onclick="toggleSelectedPicture(\''.$id.'\');" alt="alt">';
            $i++;
        }
    }
    $sourceHTML .= '<img onload="clean_images()" alt="holderImage" src="sourceImagery/spaceholder.jpg">';
    
    
   //masonry(4, 10, 'picture_selector', 'invisible'); setTimeout(function() {masonry(4, 10, 'picture_selector');}, 750);
    
    //need to echo back title and image array and display in the uploader html
    $responseText .= $sourceHTML;
    $responseText .= "|delimiter|";
    $responseText .= $link;
    echo $responseText;
    exit();
}*/
?><?php
//process image url submission from photostreamtemplate.php
if (isset($_POST['chosenLink']) && $_POST['chosenLink'] !== ""){
    $postLink = urldecode($_POST['chosenLink']);
    $link = $_POST['url'];
    if ((strpos($postLink, "http://") !== false) || (strpos($postLink, "https://") !== false || $postLink === $root."/sourceImagery/spaceholder.jpg")) {
        if ($postLink !== $root."/sourceImagery/spaceholder.jpg") {
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
            //testing public variables
           /* echo "post link: ".$post_this->postLink;
            echo "||post dbname: ".$post_this->db_file_name;
            echo "||post filIn: ".$post_this->filenameIn;
            echo "||post tmploc: ".$post_this->fileTmpLoc;
            echo "||post size: ".$post_this->fileSize;
            echo "||post ext: ".$post_this->postExt;*/
            //old posting code
 /*           
            //create a new photoparsing mechanism that actually saves a file to the server
            //all of this comes after the photo has actually been selected   

            if (strpos($postLink, "?") !== false) {
                $newLink = explode("?", $postLink);
                $filenameIn = $newLink[0];
            } else {
                $filenameIn = $postLink;
            }
            
            
            $fileTmpLoc = "/tmp/" . basename($filenameIn);
            $newFileName = str_replace(" ", "%20", $filenameIn);
            ////^pre process
            ////post process below
            $contentOrFalseOnFailure = file_get_contents($newFileName);
            
            //check to make sure that the tmp directory exists, if not then make
            if (!file_exists("/tmp")) {
                mkdir("/tmp", 0755);
            }

            $byteCountOrFalseOnFailure = file_put_contents($fileTmpLoc, $contentOrFalseOnFailure);
            
            $fileSize = $byteCountOrFalseOnFailure;
            //separate $filename inot pieces and collect file ext into variable
            $fileExt = pathinfo($fileTmpLoc, PATHINFO_EXTENSION);
            //$kaboom = explode(".", $fileName);
            //$fileExt = end($kaboom);
            //collect size parameters into array elements and check for minumum size
            list($width, $height) = getimagesize($fileTmpLoc);

            if ($width < 10 || $height < 10) {
                header("location: ../message.php?msg=ERROR: That image has no dimensions");
                exit();
            }
            //assign image a novel, unique filename
            $db_file_name = date("DMjGisY") . "" . rand(1000, 9999) . "." . $fileExt; // WedFeb272120452013RAND.jpg
            //check that image isn't too large
            if ($fileSize > 1048576) {
                header("location: ../message.php?msg=ERROR: Your image file was larger than 1mb");
                exit();
            }
            //check that image is of appropriate filetype
            else if (exif_imagetype($fileTmpLoc) === FALSE) {
                header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png type");
                exit();
            }
            //this code moves uploaded file into avatar locatio, we just want to add it...so it actaully still works for us...
            $moveResult = rename($fileTmpLoc, "../stream/$log_username/$db_file_name");
            if ($moveResult !== true) {
                header("location: ../message.php?msg=ERROR: File upload failed");
                exit();
            }
            //resize images to within reasonable parameters
            include_once("../php_includes/image_resize.php");
            $target_file = "../stream/$log_username/$db_file_name";
            $resized_file = "../stream/$log_username/$db_file_name";
            $wmax = 400;
            $hmax = 600;
            img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
*/
            ///
        }else{
            $db_file_name = $postLink;
        }
        $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
        //update database with link to new image file
        $ratio = $_POST["ratio"];
        $tag1 = $_POST['tag1'];
        $tag2 = $_POST['tag2'];
        $tag3 = $_POST['tag3'];
        $tag4 = $_POST['tag4'];
        $tag5 = $_POST['tag5'];
        $rgb_r = $_POST["rgb_r"];
        $rgb_g = $_POST["rgb_g"];
        $rgb_b = $_POST["rgb_b"];
        $description = htmlentities($_POST['description']);
        $description = stripcslashes($description);
        $description = mysqli_real_escape_string($db_conx, $description);

        //check/update tags and add if necessary
        $content_type = "images";
        checkForTag($tag1, $content_type, $db_conx, $log_username);
        checkForTag($tag2, $content_type, $db_conx, $log_username);
        checkForTag($tag3, $content_type, $db_conx, $log_username);
        checkForTag($tag4, $content_type, $db_conx, $log_username);
        checkForTag($tag5, $content_type, $db_conx, $log_username);
	$sql = "INSERT INTO photostream (poster, filename, uniqueID, postdate, link, imageLink, tag1, tag2, tag3, tag4, tag5, description, ratio, rgb_r, rgb_g, rgb_b)
                                  VALUES('$log_username', '$db_file_name', '$uniqueID', now(), '$link', '$postLink', '$tag1', '$tag2', '$tag3', '$tag4', '$tag5', '$description', '$ratio', '$rgb_r', '$rgb_g', '$rgb_b')";
	$query = mysqli_query($db_conx, $sql);
         //autoupvote by poster
        $log_vote = "INSERT INTO imagevotes (content_id, voter, token, postdate) VALUES ('$uniqueID', '$log_username', 'UP', now())";
        $query = mysqli_query($db_conx, $log_vote);
        //update vote tally in appropriate DB
        $update_count = "UPDATE photostream SET vote_state=(vote_state+1) WHERE uniqueID='$uniqueID' LIMIT 1";
        $query = mysqli_query($db_conx, $update_count);
        
        echo "success||";
        echo $uniqueID;
        echo "||";

        //internal email notification
        $content_post_message = "Posted by: " . $log_username . " at (http://www.tribesay.com/index.php?u=" . $uniqueID . "&m=image)";
        $headers = 'From: notifications@tribesay.com';
    
        $user_email = get_user_email($db_conx, $log_username);
        if (strpos($user_email, '@japes.com') === false) {
            mail("martin@tribesay.com, olivia@tribesay.com, jp@tribesay.com", "New Content Posted", $content_post_message." user_email: ".$user_email, $headers);
        }
        
        mysqli_close($db_conx);
        exit();
    } else {
        echo "failure";
        exit();
    }
}
?>