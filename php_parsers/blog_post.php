<?php
include_once('../php_includes/check_login_status.php');
include_once("../scraping/posting_modules/photo_post.php");
if (isset($_FILES["advert"]["name"]) && $_FILES["advert"]["tmp_name"] != "") {
    if(strtolower($_POST["password"]) !== "treasuretribe"){
        echo "get out";
        exit();
    }
    $fileName = $_FILES["advert"]["name"];
    $fileTmpLoc = $_FILES["advert"]["tmp_name"];
    $fileType = $_FILES["advert"]["type"];
    $fileSize = $_FILES["advert"]["size"];
    $fileErrorMsg = $_FILES["advert"]["error"];
    $link = $_POST["link"];
    $tag1 = $_POST["tag1"];
    $tag2 = $_POST["tag2"];
    $tag3 = $_POST["tag3"];
    $tag4 = $_POST["tag4"];
    $tag5 = $_POST["tag5"];
    $size = $_POST["size"];
    $customer_id = $_POST["customer_id"];
    $kaboom = explode(".", $fileName);
    $fileExt = end($kaboom);
    list($width, $height) = getimagesize($fileTmpLoc);
    if ($width < 10 || $height < 10) {
        header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();
    }
    $db_file_name = rand(100000000000, 999999999999) . "." . $fileExt;
    if ($fileSize > (1048576) * 5) {
        header("location: /message.php?msg=ERROR: Your image file was larger than 3mb");
        exit();
    } else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName)) {
        header("location: /message.php?msg=ERROR: Your image file was not jpg, gif or png type");
        exit();
    } else if ($fileErrorMsg == 1) {
        header("location: ../message.php?msg=ERROR: An unknown error occurred");
        exit();
    }
    //if (!file_exists("../user/$log_username")) { mkdir("../user/$log_username", 0755); }
    /* $moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name");
      if ($moveResult != true) {
      header("location: ../message.php?msg=ERROR: File upload failed");
      exit();
      } */
    include_once("../php_includes/image_resize.php");
    $target_file = $fileTmpLoc;
    $resized_file = $fileTmpLoc;
    $wmax = 728;
    $hmax = 90;
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
        'Key' => 'banner_ads/' . $db_file_name,
        'SourceFile' => $resized_file
    ));
    //$db_file_name = $result['ObjectURL'];
    //
    $uniqueID = $uniqueID = date("DMjGisY") . "" . rand(1000, 9999);
    //        
    $sql = "INSERT INTO adverts(link, image_url, serv_count, upload_date, tag1, tag2, tag3, tag4, tag5, customer_id, uniqueID, size) VALUES('$link', '$db_file_name', '0', now(), '$tag1', '$tag2', '$tag3', '$tag4', '$tag5', '$customer_id', '$uniqueID', '$size')";
    $query = mysqli_query($db_conx, $sql);
    mysqli_close($db_conx);
    exit();
}
?>
<!-- wysihtml5 parser rules -->
<script src="/path-to-wysihtml5/parser_rules/advanced.js"></script>
<!-- Library -->
<script src="/path-to-wysihtml5/dist/wysihtml5-0.3.0.min.js"></script>

<form id="blog_post" enctype="multipart/form-data" method="post" action="">
<!--    http://xing.github.io/wysihtml5/-->
    <input placeholder="Post Title">
    
    <textarea id="wysihtml5-textarea" placeholder="Enter your text ..." autofocus></textarea>
</form>
    <div id="wysihtml5-toolbar" style="display: none;">
  <a data-wysihtml5-command="bold">bold</a>
  <a data-wysihtml5-command="italic">italic</a>
  
  <!-- Some wysihtml5 commands require extra parameters -->
  <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red">red</a>
  <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green">green</a>
  <a data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue">blue</a>
  
  <!-- Some wysihtml5 commands like 'createLink' require extra paramaters specified by the user (eg. href) -->
  <a data-wysihtml5-command="createLink">insert link</a>
  <div data-wysihtml5-dialog="createLink" style="display: none;">
    <label>
      Link:
      <input data-wysihtml5-dialog-field="href" value="http://" class="text">
    </label>
    <a data-wysihtml5-dialog-action="save">OK</a> <a data-wysihtml5-dialog-action="cancel">Cancel</a>
  </div>
</div>
<script>
var editor = new wysihtml5.Editor("wysihtml5-textarea", { // id of textarea element
  toolbar:      "wysihtml5-toolbar", // id of toolbar element
  parserRules:  wysihtml5ParserRules // defined in parser rules set 
});
</script>