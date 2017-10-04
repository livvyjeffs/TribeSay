<?php
//include_once("../php_includes/db_conx.php");
$meta_uid = $_GET["u"];
$meta_media_type = $_GET["m"];
switch($meta_media_type){
    case 'image':
        $sql = "SELECT description, imageLink FROM photostream WHERE uniqueID='$meta_uid' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        $meta_description = $row[0];
        $meta_image_src = $row[1];
        $meta_title = 'Check it out on TribeSay';
        
        break;
    case 'video':
        $sql = "SELECT title, description, poster, img_src FROM videos WHERE uniqueID='$meta_uid' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        $meta_title = $row[0];
        $meta_description = $row[1];
        $poster = $row[2];
        $src = $row[3];
        $meta_image_src = $s3root.'/stream/'.$poster.'/'.$src;
        
        break;
    case 'article':
        $sql = "SELECT title, description, poster, imagesrc FROM articles WHERE uniqueID='$meta_uid' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        $meta_title = $row[0];
        $meta_description = $row[1];
        $poster = $row[2];
        $src = $row[3];
        $meta_image_src = $s3root.'/stream/'.$poster.'/'.$src;
        
        break;
    case 'sound':
        $sql = "SELECT title, description, art_url FROM audio WHERE uniqueID='$meta_uid' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
        $meta_title = $row[0];
        $meta_description = $row[1];
        $meta_image_src = $row[2];
        
        break;
    default:

        $meta_image_src = 'https://pbs.twimg.com/profile_images/453263180538470400/memATyVA.png';

        if ($pagename === 'news') {
            $meta_description = 'Social News Now.';
            $meta_title = 'TribeSay | Social News Now';
        } else if ($pagename === 'classifieds') {
            $meta_description = 'Part of the Startup Tribe? We are your source for Startup events in the DC Area.';
            $meta_title = 'TribeSay Classifieds | Find your Tribe in Realtime';
        }

        break;
}
?>
