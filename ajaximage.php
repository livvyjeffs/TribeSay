<?php

//session_start();
//$session_id = '1'; // User session id
$path = "/tmp/";

$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg");
if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
    header("location: message.php?msg=ERROR: you suck less");
    exit();
    $name = $_FILES['avatar_file']['name'];
    $size = $_FILES['avatar_file']['size'];
    if (strlen($name)) {
        list($txt, $ext) = explode(".", $name);
        if (in_array($ext, $valid_formats)) {
            if ($size < ((1024 * 1024) * 3)) { // Image size max 1 MB
                $actual_image_name = $name . time() . "." . $ext;
                $tmp = $_FILES['avatar_file']['tmp_name'];
                if (move_uploaded_file($tmp, $path . $actual_image_name)) {
                    header("location: ../message.php?msg=ERROR: " . $path . $actual_image_name);
                    exit();
                    echo $path . $actual_image_name;
                } else
                    echo "failed";
            } else
                echo "Image file size max 1 MB";
        } else
            echo "Invalid file format..";
    } else
        echo "Please select image..!";
    exit;
}
?>