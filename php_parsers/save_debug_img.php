<?php
error_reporting(E_ERROR | E_PARSE);
include_once('../php_includes/check_login_status.php');
require($_SERVER["DOCUMENT_ROOT"]."/libs/aws/aws-autoloader.php");
?><?php

if (isset($_FILES["image"]["name"])) {
    if ($_FILES["image"]["tmp_name"] != "") {
        $fileName = $_FILES["image"]["name"];
        $fileTmpLoc = $_FILES["image"]["tmp_name"];
        $fileType = $_FILES["image"]["type"];
        $fileSize = $_FILES["image"]["size"];
        $fileErrorMsg = $_FILES["image"]["error"];
        $kaboom = explode(".", $fileName);
        $fileExt = end($kaboom);
        list($width, $height) = getimagesize($fileTmpLoc);


        if ($width < 10 || $height < 10) {
            echo "That image has no dimensions";
            exit();
        }


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
            'Key' => 'debug_pics/' . $log_username . "/" . $db_file_name,
            'SourceFile' => $fileTmpLoc
        ));
    }
    //get email from database
    if ($user_ok === true) {
        $sql = "SELECT email FROM users WHERE username='$log_username' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        $user_email = $row[0];
    }else{
        $user_email = "debug@tribesay.com";
    }
    $subject = $_POST["subject"];
    $message = $_POST["message"];
    $mobile = $_POST["mobile"];
    $browser = $_POST["browser"];
    $window_width = $_POST["width"];
    $window_height = $_POST["height"];
    $debug_message = "Debug from: ".$log_username." (".$user_email.")
            
            Subject: ".$subject."
                
            Message: ".$message."
                
            Mobile: " . $mobile . "
            Browser: " . $browser . "
            Width x Height: " . $window_width . " x " . $window_height;

    $headers = 'From: '.$user_email;

    //PUT THIS INTO A DEBUG DB TABLE...AND MAIL FOR INSTANT RESPONSE
    mail("martin@tribesay.com, olivia@tribesay.com, jp@tribesay.com", "2cents", $debug_message." img url: " .$result['ObjectURL'], $headers);

    echo "success";
    exit();
}
?>