<?php
include_once("../../php_includes/check_login_status.php");
//classified poster module
if(isset($_POST["post_classified"])){//must do input validation - either front end or below
//
    $data = json_decode($_POST["post_classified"], true);
    //print_r($data);
    //exit();
    //
    //general
    $classified_type = "events"; //$data["classified_type"]; //should be 'events'
    $title = mysqli_real_escape_string($db_conx,stripcslashes(htmlentities($data["title"])));
    $description = mysqli_real_escape_string($db_conx,stripcslashes(htmlentities($data["description"])));
    $ticket_price = $data["ticket_price"];
    //$pinned_status = $data["pinned_status"]; //'pinned' or 'unpinned'
    $payment_link = $data["payment_link"];
    //dates
    $event_begin = date_format(date_create($data["event_begin"]),'Y-m-d H:i:s');
    $event_end = date_format(date_create($data["event_end"]),'Y-m-d H:i:s');
    
    //location
    $country = $data["country"];
    $city = $data["city"];
    $state = $data["state"];
    $location_html = $data["location_html"];
    $location_formatted = $data["location_formatted"];
    //process for safe sql 
    $location_html = htmlentities($location_html);
    $location_html = stripcslashes($location_html);
    $location_html = mysqli_real_escape_string($db_conx, $location_html);
    $location_formatted = htmlentities($location_formatted);
    $location_formatted = stripcslashes($location_formatted);
    $location_formatted = mysqli_real_escape_string($db_conx, $location_formatted);
    
    if(isset($data["zip"])){
        $zip = $data["zip"];
    }else{
        $zip = "null";
    } 
    if(isset($data["street_number"])){
        $street_number = $data["street_number"];
    }else{
        $street_number = "null";
    } 
    if(isset($data["street_name"])){
        $street_name = $data["street_name"];
    }else{
        $street_name = "null";
    } 
    $lat = $data["lat"];
    $long = $data["long"];
    $radius = $data["radius"];
    //tags
    $tag1 = $data["tag1"];
    $tag2 = $data["tag2"];
    $tag3 = $data["tag3"];
    //img data
    $img_location = $data["img_location"];
    $thumbnail_location = $data["thumbnail_location"];                          //still need all this
    $rgb_r = $data["rgb_r"];
    $rgb_g = $data["rgb_g"];
    $rgb_b = $data["rgb_b"];
    $ratio = $data["ratio"];
    //generated data
    $uniqueID = date("DMjGisY")."".rand(1000,9999);
    
    //db query
    $sql = "INSERT INTO ".$classified_type." (title, description, event_begin, event_end, country, city, zip, lat, `long`, tag1, tag2, tag3, img_location, rgb_r, rgb_g, rgb_b, ratio, postdate, poster, street_number, street_name, radius, payment_link, thumbnail_location, uniqueID, location_html, location_formatted, ticket_price) 
                                       VALUES('$title', '$description', '$event_begin', '$event_end', '$country', '$city', '$zip', '$lat', '$long', '$tag1', '$tag2', '$tag3', '$img_location', '$rgb_r', '$rgb_g', '$rgb_b', '$ratio', now(), '$log_username', '$street_number', '$street_name', '$radius', '$payment_link', '$thumbnail_location', '$uniqueID', '$location_html', '$location_formatted', '$ticket_price')";
    /*
    $sql = "INSERT INTO events (title, description, pinned_status, event_begin, event_end, campaign_begin, campaign_end, country, city, zip, lat, long, tag1, tag2, tag3, img_location, rgb_r, rgb_g, rgb_b, ratio, postdate, poster, street_address, radius, payment_link, thumbnail_location, uniqueID) 
                                       VALUES('title1', 'description1', 'pinned', now(), now(), now(), now(), 'usa', 'mclean', '22101', '3834', '3242348', 'fire', 'fire', 'fire', 'location', 'red', 'green', 'blue', '2.3', now(), '$log_username', '1841 baldwin', '1000', 'http:.//bitch.com', 'fuckyou.org.png', '$uniqueID')";
    */
    //$sql = "INSERT INTO events (title, description, pinned_status, event_begin, event_end, campaign_begin, campaign_end, country, city, zip, lat, `long`, tag1, tag2, tag3, img_location, rgb_r, rgb_g, rgb_b, ratio, postdate, poster, street_address, radius, payment_link, thumbnail_location, uniqueID) VALUES('title1', 'description1', 'pinned', now(), now(), now(), now(), 'usa', 'mclean', 22101, 234234, 23423, 'fire', 'fire', 'fire', 'location', '234', '23423', '234', '2.3', now(), '$log_username', '1841 baldwin', '1000', 'http:.//bitch.com', 'fuckyou.org.png', '$uniqueID')";
    $query = mysqli_query($db_conx, $sql);
    //test for success and echo
    if($query !== false){
        echo "success";
    }else{
        echo $sql;
        //echo "failure";
    }
    exit();
}
