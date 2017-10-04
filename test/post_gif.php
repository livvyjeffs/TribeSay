<?php
include_once("../scraping/posting_modules/photo_post.php");
//$url = "http://i.imgur.com/nJnD26r.gif";
$url = "http://static6.businessinsider.com/image/53ad616feab8ead3194f8cb5/google-is-offering-free-coding-lessons-to-women-and-minorities.jpeg";

$gif = new image_to_post($url, "martin", "test");
$gif->remove_url_variables();
echo $gif->filenameIn."<br>";
$gif->make_tmp();
$gif->file_get_and_put();
echo $gif->fileTmpLoc."<br>";
echo $gif->fileSize." <br>";

echo "<img src='".$gif->fileTmpLoc."'>";

//$img = imagecreatefrompng($gif->fileTmpLoc); //breaks here
//exit();

$gif->check_size();
if ($gif->loop_tracker === "exit") {
    $gif->check_size();
}

$gif->gen_db_name();
echo $gif->fileExt;
$gif->resize_image();
$gif->move_to_permanent();
$gif->get_img_specs();

