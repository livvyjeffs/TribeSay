<?php
error_reporting(E_WARNING | E_PARSE);
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/geo_targeting.php");
//check if isset criteria for modal advert
if (isset($_POST["tag_array"])) {
    $size = $_POST["size"];
    //create array out of string
    $tag_array = explode(",", $_POST["tag_array"]);
    //generate SQL criteria from tags array
    $criteria = "";
    $n = 0;
    foreach ($tag_array as $fav) {
        if ($fav !== "null") {
            if ($n !== 0) {
                $criteria .= ' OR ';
            } else {
                $criteria .= "(";
            }
            $criteria .= '(tag1="' . $fav . '")';
            $criteria .= ' OR (tag2="' . $fav . '")';
            $criteria .= ' OR (tag3="' . $fav . '")';
            $criteria .= ' OR (tag4="' . $fav . '")';
            $criteria .= ' OR (tag5="' . $fav . '")';
            $n++;
        }
    }
    $criteria .= ')';
    //generate full query
    $sql = "SELECT image_url, link, uniqueID, id, customer_id, target_type, geo_data FROM adverts WHERE " . $criteria . " AND size='$size' ORDER BY serv_count";//add target_type and geo_data to DB!!!!!!!!!!!
    //execute query
    $query = mysqli_query($db_conx, $sql);
    if (mysqli_num_rows($query) > 0) {
        while ($ad = mysqli_fetch_array($query)) {
            //check if geo is necessary - if doesn't meet           
            if($ad[5] === "radius" || $ad[5] === "bounds"){//add geo to column to adverts database
                $ip = getenv('REMOTE_ADDR');
                $target = new geo_target($ip, $ad[5], $ad[6]);
                $qualifies = $target->evaluate_geo();  
                if($qualifies !== true){
                    continue;
                }
            }          
            //collect ad data
            $img_src = $ad[0];
            $link = $ad[1];
            $unique_id = $ad[2];
            $ad_id = $ad[3];
            $customer_id = $ad[4];
            $response = array("img_src" => $img_src, "link" => $link, "ad_id" => $ad_id, "customer_id" => $customer_id);
            //update the serve count. might want to do this in separate ajax call if 
            //ads are loading too slow.
            $sql = "UPDATE adverts SET serv_count=(serv_count+1) WHERE uniqueID='$unique_id' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
            //echo back the ad
            echo json_encode($response);
            exit();
        }
    }
    exit();
}