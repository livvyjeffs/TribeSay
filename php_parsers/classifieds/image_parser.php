<?php
//use debug modile JQuery in order to send this over
error_reporting(E_ERROR | E_PARSE);
include_once("../../php_includes/check_login_status.php");
include_once("../../php_includes/image_resize.php");
require($_SERVER["DOCUMENT_ROOT"]."/libs/aws/aws-autoloader.php");
?><?php
if (isset($_FILES["image"]["name"])) {
    if ($_FILES["image"]["tmp_name"] != "") {
        //collect form data
        $fileName = $_FILES["image"]["name"];
        $fileTmpLoc = $_FILES["image"]["tmp_name"];
        $fileType = $_FILES["image"]["type"];
        $fileSize = $_FILES["image"]["size"];
        $fileErrorMsg = $_FILES["image"]["error"];
        $kaboom = explode(".", $fileName);
        $fileExt = end($kaboom);
        //check for image dimensions
        list($width, $height) = getimagesize($fileTmpLoc);
        if ($width < 10 || $height < 10) {
            echo "That image has no dimensions";
            exit();
        }

        $img_ratio = $height / $width;

        $db_file_name = rand(100000000000, 999999999999) . "." . $fileExt;


        if ($fileSize > (1048576 * 8)) {
            echo "Your image file was larger than 4mb";
            exit();
        } else if (!preg_match("/\.(gif|jpg|png)$/i", $fileName)) {
            echo "Your image file was not jpg, gif or png type";
            exit();
        } else if ($fileErrorMsg == 1) {
            echo "An unknown error occurred";
            exit();
        }

        $bucket = 'TribeSay_images';
        //create client
        $client = Aws\S3\S3Client::factory(array(
                    'key' => 'AKIAI3E72U4J2Q3264AA',
                    'secret' => '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'
        ));
        //upload an object
        $result = $client->putObject(array(
            'Bucket' => $bucket,
            'Key' => 'event_originals/' . $log_username . "/" . $db_file_name,
            'SourceFile' => $fileTmpLoc
        ));
        //create thumbnail
        $target_file = $fileTmpLoc;
        $resized_file = $fileTmpLoc;
        $wmax = 800;
        $hmax = 1200;
        img_resize($target_file, $resized_file, $wmax, $hmax, exif_imagetype($target_file));
        //upload an object
        $db_file_name2 = rand(100000000000, 999999999999) . "." . $fileExt;
        $result2 = $client->putObject(array(
            'Bucket' => $bucket,
            'Key' => 'event_thumbnails/' . $log_username . "/" . $db_file_name2,
            'SourceFile' => $resized_file
        ));
        //define image paths
        $original_image = 'event_originals/' . $log_username . "/" . $db_file_name;
        $thumbnail_image = 'event_thumbnails/' . $log_username . "/" . $db_file_name2;
        //return data to front end
        echo json_encode(array("original_image" => $original_image, "thumbnail_location" => $thumbnail_image, "ratio" => $img_ratio));
        exit();
    }else{
        echo "no_image_found";
        exit();
    }

    
    
}
?>