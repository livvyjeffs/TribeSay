<?php
function compare_score($a, $b) {
    if($a['score'] < $b['score']){
        return 1;
    }elseif($a['score'] > $b['score']){
        return -1;
    }else{
        return 0;
    }
}
function compare_event($a, $b) {
    if($a['score'] > $b['score']){
        return 1;
    }elseif($a['score'] < $b['score']){
        return -1;
    }else{
        return 0;
    }
}
function compare_date($a, $b) {
    if($a['uploadDate'] < $b['uploadDate']){
        return 1;
    }elseif($a['uploadDate'] > $b['uploadDate']){
        return -1;
    }else{
        return 0;
    }
}
//this stream generator is designed so that new media types can be easily 
//incorprated. The implementation steps are as follows:
//  1) update $type_array and $databases array with matching key.
//  2) confirm db_schema requirements are met for new_type: 
//      a) tag1-5, postdate
//      b) title, poster, vote_state, uniqueID, description, link, hostname
//      c) new_type_votes (table): voter='logusername'
//  3) update both (2) type-switch statments to account for 1) special variables and 2)html output

error_reporting(E_ERROR | E_PARSE);
include_once("check_login_status.php");
include_once("convert_date.php");
include_once("getFriendsArray.php");
include_once("../php_parsers/classifieds/classified_generator.php");
if (isset($_POST['scope']) && isset($_POST['current_id_list']) && isset($_POST['page_owner']) && isset($_POST['splode_status'])) {
    //declare available content types in array
    //declare target content databases in an array with type as key. eg video=>youtube, video=>vimeo, audio=>soundcloud
    $type_array = array("video", "article", "image", "sound"); //"comment"
    $databases = array("video" => "videos", "article" => "articles", "image" => "photostream", "sound" => "audio"); //"comment" => "comments"
    //add in comments if logged in
    if($user_ok !== false){
        //array_push($type_array, "comment");
        //$databases["comment"] = "comments";
    }
    //collect posted variables
    $stream_media_type = $_POST['stream_media_type'];
    $current_id_array = $_POST['current_id_list'];
    $column_width = $_POST['column_width'];
    $scope = $_POST['scope'];
    $infinite = $_POST["infinite"];
    $splode_status = $_POST['splode_status'];
    $trigger = $_POST['trigger'];
    $event_filter = $_POST["event_filter"];
    $single_type = "no";
    if (($trigger === "filter" && $splode_status !== "no" && $splode_status !== "mixed") || ($trigger === "scope" && $splode_status !== "no" && $splode_status !== "mixed")) {
        $single_type = "yes";
    }
    if ($trigger === "filter" || $trigger === "scope") {
        $trigger = "fresh_load";
    }
    if ($splode_status === "no" && $trigger !== "fresh_load") {
        $lim = 3;
    } else {
        $lim = 9;
    }
    if ($trigger === "fresh_load") {
        //load all media types ie splode is natural
        $current_id_array = "non_purge";
    }
    //purging factors
    $page_owner = $_POST['page_owner'];
    //add in conditions for advert
    if ($stream_media_type === "event") {
        $type_array = array("event");
        $databases = array("event" => "events");
        $single_type = "yes";
        $splode_status = "event";
        $scope = "tribe";
        //all other variables ie: media_type, current_ids remain same. 
        //trigger=fresh||scroll
        //db_columns, switch statements, html need to be updated
        //sort function needs and "if" and to be tailored
    }
    //collect event filter
    if($stream_media_type === "event"){
        switch($event_filter){
            case 'today':
                $start = date_format(date_create(date('Y-m-d 06:00:00')), 'Y-m-d H:i:s'); //am This Morning
                $end = date_format(date_create(date('Y-m-d 23:59:59')), 'Y-m-d H:i:s'); //am This Morning
                $criteria_date = " (event_begin > '$start' AND event_begin < '$end')";
                break;
            case 'tomorrow':
                $start = $mid_tomorrow = date_format(date_add(date_create(date('Y-m-d 06:00:00')), date_interval_create_from_date_string('1 day')),'Y-m-d H:i:s');
                $end = $mid_tomorrow = date_format(date_add(date_create(date('Y-m-d 23:57:59')), date_interval_create_from_date_string('1 day')),'Y-m-d H:i:s');
                $criteria_date = " (event_begin > '$start' AND event_begin < '$end')";
                break;
            case 'weekend':
                $today = date_format(date_create(date('Y-m-d H:i:s')), 'Y-m-d H:i:s');
                $dw = date("w", strtotime($today));
                switch ($dw) {
                    case 5://Friday
                    case 6://Saturday
                    case 7://Sunday
                        $start = date_format(date_create(date('Y-m-d 6:00:00')), 'Y-m-d H:i:s'); //am This Morning
                        $end = date_format(date_create(date("Y-m-d 23:59:59", strtotime('next Friday'))), 'Y-m-d H:i:s'); //Sunday Midnight
                        break;
                    default:
                        $start = date_format(date_create(date("Y-m-d 12:00:00", strtotime('next Friday'))), 'Y-m-d H:i:s'); // Noon This Friday
                        //$end = date_format(date_create(date('Y-m-d 23:59:59', strtotime('next Sunday', strtotime($date)))), 'Y-m-d H:i:s'); //Sunday Midnight
                        $end = date('Y-m-d 23:59:59', strtotime('next Sunday', strtotime($start)));
                        break;
                }
                $criteria_date = " (event_begin > '$start' AND event_begin < '$end')";
                break;
            default:
                $criteria_date = " (event_begin > now())";
                break;
        }
        //echo $event_filter;echo $criteria_date;
    }
   
    //delta filter and delta scope will be checked for in JS and if either is positive current_id_array = "non_purge"
    //because js will be replacing all of the content html rather than adding to the currently displayed content.
    //check if current content is being cleared or not. If so, create array from list of currently displayed uniques
    //if it IS being purged
    if ($current_id_array !== "non_purge") {
        $total_count = 0;
        $current_id_array = explode(",", $_POST['current_id_list']);
        if($trigger !== "fresh_load"){
            //print_r($current_id_array);
        }
        $count_array = array();
        foreach ($current_id_array as $element) {
            //this requires that the javascript which encodes the unique string
            //include one element of the structure: type||count||"count"
            //for each type
            if (strpos($element, "count") !== false) {
                $current = explode("||", $element);
                $current_type = $current[0];
                $current_count = $current[1];
                $$current_type = array();
                $$current_type[$current_type] = $current_count;
                $total_count += $current_count;
            }
        }
    }

    //if splode status is not equal to "no", then modify the type_array and 
    //databases so that only data that is current sploded get processed
    if (($trigger !== "fresh_load" && $stream_media_type !== "mixed") || $single_type === "yes") {
        $type_array = array($stream_media_type);
        $databases = array_intersect_key($databases, array_flip($type_array));
    }




    //////////////////ABOVE DETERMINES WHAT TO DO, BELOW DOES IT////////////////



    $selectedFavsArray = array();
    if ($user_ok !== false) {
        //generate available selected favs array
        $sql = "SELECT * FROM selectedfavorites WHERE user='$log_username'";
        $query = mysqli_query($db_conx, $sql);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $tagName = $row['tagname'];
            array_push($selectedFavsArray, $tagName);
        }
    } else {
        //collect session variables into array
        $session_filters = array("f1", "f2", "f3", "f4", "f5");
        foreach ($session_filters as $f) {
            if (isset($_SESSION[$f]) && $_SESSION[$f] !== "") {
                array_push($selectedFavsArray, $_SESSION[$f]);
            }
        }
    }
     //event critera is included in classified_generator.php - overwright it if not loading events
    if($stream_media_type !== "event"){
        $event_criteria = "";
    }elseif(count($selectedFavsArray) !== 0 && $infinite !== "yes"){
        $event_criteria = " AND".$criteria_date;
    }else{
        $event_criteria = $criteria_date;
    }
    //generate criteria that applies to all queries and is based upon selected 
    //favorites for filtering
    $criteria = "";
    $n = 0;
    if (count($selectedFavsArray) !== 0 && $infinite !== "yes") {
        foreach ($selectedFavsArray as $fav) {
            if ($n !== 0) {
                $criteria .= ' OR ';
            } else {
                $criteria .= " AND (";
            }
            $criteria .= '(tag1="' . $fav . '")';
            $criteria .= ' OR (tag2="' . $fav . '")';
            $criteria .= ' OR (tag3="' . $fav . '")';
            $criteria .= ' OR (tag4="' . $fav . '")';
            $criteria .= ' OR (tag5="' . $fav . '")';
            $n++;
        }
        $criteria .= ')';
    }
    //generate raw list of unique id's based on posted variables without and vote or criteria purging
    foreach ($databases as $type => $base_of_data) {
        //set query start count
        if(isset($$type[$type])){
            $start_count = 40 * floor($$type[$type] / 40);
            //echo "type: ".$type;
            //echo "count: ".$$type[$type] . "\n";
            //echo "start: ".$start_count;
        }else{
            if($trigger !== "fresh_load"){
                //echo "type count not set";
            }
            $start_count = 0;
        }
        if ($type === "article") {
            $votes_db = "articlevotes";
        } elseif ($type === "video") {
            $votes_db = "videovotes";
        } elseif ($type === "image") {
            $votes_db = "imagevotes";
        } elseif ($type === "sound") {
            $votes_db = "audiovotes";
        } elseif ($type === "event") {
            $votes_db = "";
        }
        if ($type === "event") {
            $order_by = "event_begin ASC";
        } else {
            $order_by = "postdate DESC";
        }
        switch ($scope) {
            case "single":
                $sql = "SELECT * FROM " . $base_of_data . " WHERE poster='$page_owner'" . $criteria . $event_criteria. " ORDER BY ".$order_by." LIMIT ".$start_count.",40";
                if($page_owner === $log_username){
                    $sql2 = "SELECT * FROM " . $votes_db . " WHERE voter='$page_owner' AND token='UP' ORDER BY postdate DESC LIMIT ".$start_count.",40";
                }else{
                    $sql2 = "";
                }
                
                break;
            case "friends":
                //create a loop break if they dont have any friendsXXXXXXXX!!!!!
                //get friends array
                $friends = array();
                //Query friends table for all of posted user's array of friends
                $sql_f = "SELECT * FROM friends WHERE user1='$log_username' AND accepted='1'";
                $query_f = mysqli_query($db_conx, $sql_f);
                while ($row = mysqli_fetch_array($query_f, MYSQLI_ASSOC)) {
                    if ($row['user1'] === $log_username) {
                        array_push($friends, $row['user2']);
                    }
                }
                //generate query criteria for all friends
                if (count($friends) !== 0) {
                    $friend_criteria = "";
                    foreach ($friends as $friend) {
                        if ($friend === $friends[0]) {
                            $friend_criteria .= " poster='$friend'";
                        } else {
                            $friend_criteria .= " OR poster='$friend'";
                        }
                        if ($friend === end($friends)) {
                            $friend_criteria .= "";
                        }
                    }
                    //get friend votes
                    $friend_voters = "";
                    foreach ($friends as $friend) {
                        if ($friend === $friends[0]) {
                            $friend_voters .= " (voter='$friend'";
                        } else {
                            $friend_voters .= " OR voter='$friend'";
                        }
                        if ($friend === end($friends)) {
                            $friend_voters .= ") AND";
                        }
                    }
                    $sql = "SELECT * FROM " . $base_of_data . " WHERE" . $friend_criteria . $criteria. $event_criteria. " ORDER BY postdate DESC LIMIT ".$start_count.",40";
                    $sql2 = "";//"SELECT * FROM " . $votes_db . " WHERE" . $friend_voters . " token='UP' ORDER BY postdate DESC LIMIT ".$start_count.",40";
                } else {
                    $sql = "";
                    $sql2 = "";
                }
                break;
            case "tribe":
                if ((count($selectedFavsArray) !== 0 && $infinite !== "yes") || $event_criteria !== "") {
                    $where = " WHERE";
                } else {
                    $where = "";
                }
                
                $sql = "SELECT * FROM " . $base_of_data . $where . substr($criteria, 4). $event_criteria . " ORDER BY ".$order_by." LIMIT ".$start_count.",40";
                //echo $sql;
                
                $sql2 = "";
                break;
        }
        
        
        
        //generate array for current database and push type in
        $$base_of_data = array();
        //execute query and generate array of pre_purge unique_id's for
        $query = mysqli_query($db_conx, $sql);
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $uniqueID = $row['uniqueID'];
            array_push($$base_of_data, $uniqueID);
        }
        //repeat but for voting database that corresponds
        if ($sql2 !== "") {
            $up_voted = array();
            $query2 = mysqli_query($db_conx, $sql2);
            while ($row = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
                $uniqueID = $row['content_id'];
                array_push($up_voted, $uniqueID);
            }
            //create sql criteria based on up votes uniques in order to check against selected favs
            $vote_criteria = "";
            $x = 0;
            foreach ($up_voted as $unique) {
                if ($x !== 0) {
                    $vote_criteria .= ' OR';
                } else {
                    $vote_criteria .= "(";
                }
                $vote_criteria .= ' (uniqueID="' . $unique . '")';
                $x++;
            }
            $vote_criteria .= ')';
            //create a new query that reduces the up votes content to only those allowed by the filter
            $sql = "SELECT * FROM " . $base_of_data . " WHERE" . $vote_criteria . $criteria;
            $query = mysqli_query($db_conx, $sql);
            $accepted_upvoted_content = array();
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $uniqueID = $row['uniqueID'];
                array_push($accepted_upvoted_content, $uniqueID);
            }
            $$base_of_data = array_merge($$base_of_data, $accepted_upvoted_content);
            //exit();
        }
    }
    //if deltas are both negative, then initiate the purge of current uniques. only need to check for non_purge
    //if purging is required, is will apply regardless of scope.
    foreach ($databases as $type => $base_of_data) {
        if ($current_id_array !== "non_purge") {
            $$base_of_data = array_diff($$base_of_data, $current_id_array);
        }
        //regardless of case, all databases must be purged of duplicates
        $$base_of_data = array_unique($$base_of_data);
        //if posted to in proper fashion/context, the above block should generate an array of unique id's for each database
        //listed with the name of the array being the name of the database (ie one for each element in the database array.
        //the contents of each array will be appropriate to: 1) the scope 2) the pageowner 3) selected favorites 4) purged for duplicates and
        //current id's if part of load more functionality.
        //for cases of single and friends, upvoted content is also accounted for.
        //--------------------------------------------------------------------//
        //Generate an array of arrays for each type of content represented in databases thus far
        //each internal array will consist of specific content data in a set comprehensive to that content
        foreach ($$base_of_data as $id) {
            $sql = "SELECT * FROM " . $base_of_data . " WHERE uniqueID='$id'";
            $query = mysqli_query($db_conx, $sql);
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                //must ensure that database schemas are universal to content types
                //any idiosyncratic fields my be accounted for in type dependent
                //switch statement below                                        
                $uploadDate = $row['postdate'];
                $title = html_entity_decode($row['title']);
                $postedUser = $row['poster'];
                //get poster image path
                $query_avatar = "SELECT * FROM users WHERE username='$postedUser' LIMIT 1";
                $get_avatar = mysqli_query($db_conx, $query_avatar);
                while ($row_a = mysqli_fetch_array($get_avatar, MYSQLI_ASSOC)) {
                    $avatar = $row_a["avatar"];
                    if ($avatar === null) {
                        $avatar = $root . "/sourceImagery/default_avatar.png";
                    } else {
                        $avatar = $s3root . "/user/" . $postedUser . "/" . $avatar;
                    }
                    $avatar_ratio = $row_a["ratio"];
                }
                //for advert, $voteState = # servs, and score is equal to this.
                if ($type === "event") {
                    $voteState = $row['views'];
                    $score = $row["event_begin"]; //check if this gets properly evaluated
                } else {
                    $voteState = $row['vote_state'];
                    $score = convert_date($uploadDate, $voteState);
                }
                $time_ago = convert_date_timeago($uploadDate);
                $uniqueID = $row['uniqueID'];
                $tag1 = $row['tag1'];
                $tag2 = $row['tag2'];
                $tag3 = $row['tag3'];
                $tag4 = $row['tag4'];
                $tag5 = $row['tag5'];
                $description = html_entity_decode($row['description']);
                $description = nl2br($description);
                $originaLink = $row['link'];
                $hostName = $row['hostname'];
                //define content unit array
                $content_unit = array("score" => $score,
                    "uploadDate" => $uploadDate,
                    "title" => $title,
                    "poster" => $postedUser,
                    "vote_state" => $voteState,
                    "time_ago" => $time_ago,
                    "unique_id" => $uniqueID,
                    "tag1" => $tag1,
                    "tag2" => $tag2,
                    "tag3" => $tag3,
                    "tag4" => $tag4,
                    "tag5" => $tag5,
                    "description" => $description,
                    "originalLink" => $originaLink,
                    "hostName" => $hostName,
                    "avatar" => $avatar,
                    "avatar_ratio" => $avatar_ratio,
                    "media" => $type);
                $content_unit["rgb_r"] = $row["rgb_r"];
                $content_unit["rgb_g"] = $row["rgb_g"];
                $content_unit["rgb_b"] = $row["rgb_b"];
                //include any non universal database fields here and generate unit array
                $content_unit["ratio"] = $row["ratio"];
                switch ($type) {
                    case "sound":
                        $content_unit["audio_code"] = $row['audioCode'];
                        $content_unit["sc_user"] = $row["sc_user"];
                        $content_unit["imageSource"] = $row["art_url"];
                        break;
                    case "video":
                        $imageName = $row['img_src'];
                        if ($imageName === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageSource"] = $root."/".$imageName;
                        } else {
                            $imageSource = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                            $content_unit["imageSource"] = $imageSource;
                        }
                        $content_unit["videoID"] = $row['videoID'];
                        break;
                    case "article":
                        //$content = html_entity_decode($row['content']);
                        //$content_unit["content"] = nl2br($content);
                        $content_unit["frame_stat"] = $row["frame_stat"];
                        $imageName = $row['imagesrc'];
                        if ($imageName === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageSource"] = $root."/".$imageName;
                        } else {
                            $imageSource = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                            $content_unit["imageSource"] = $imageSource;
                        }
                        break;
                    case "image":
                        $imageName = $row['filename'];
                        if ($imageName === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageSource"] = $root."/".$imageName;
                        } else {
                            $imageLocation = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                            $imageLink = $row['imageLink'];
                            $content_unit["imageSource"] = $imageLocation;
                        }
                        $content_unit["imageLink"] = $imageLink;
                        break;
                    case "comment":
                        $content_unit["parent_id"] = $row["parent_id"];
                        $content_unit["data"] = nl2br(html_entity_decode($row["data"]));
                        $content_unit["content_id"] = $row["content_id"];
                        $content_unit["content_type"] = $row["content_type"];
                        break;
                    case "event":
                        //non-universal advert fields go here
                        $content_unit["pinned_status"] = $row["pinned_status"];
                        $content_unit["event_begin"] = str_replace("-","/",$row["event_begin"]);
                        $content_unit["event_end"] = str_replace("-","/",$row["event_end"]);
                        //$content_unit["campaign_begin"] = str_replace("-","/",$row["campaign_end"]);
                        $content_unit["country"] = $row["country"];
                        $content_unit["city"] = $row["city"];
                        $content_unit["zip"] = $row["zip"];
                        $content_unit["lat"] = $row["lat"];
                        $content_unit["long"] = $row["long"];
                        //get original image
                        $imageName = $row['img_location'];
                        if ($imageName === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageSource"] = $root."/".$imageName;
                        } else {
                            $imageSource = $s3root ."/". $imageName;
                            $content_unit["imageSource"] = $imageSource;
                        }
                        //get thumbnail image
                        $thumbnail = $row['thumbnail_location'];
                        if ($thumbnail === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageSource"] = $root."/".$thumbnail;
                        } else {
                            $imageSource = $s3root ."/". $thumbnail;
                            $content_unit["thumbnail_source"] = $imageSource;
                        }
                        $content_unit["street_address"] = $row["street_address"];
                        $content_unit["radius"] = $row["radius"];
                        $content_unit["views"] = $row["views"];
                        //get location data
                        $location_html = html_entity_decode($row['location_html']);
                        $content_unit["location_html"] = nl2br($location_html);
                        $location_formatted = html_entity_decode($row['location_formatted']);
                        $content_unit["location_formatted"] = nl2br($location_formatted);
                        $content_unit["ticket_price"] = $row["ticket_price"];
                        $content_unit["payment_link"] = $row["payment_link"];
                        break;
                }
                //single scope html requires sorting by recentness, so pop off the score
                //otherwise the score can be left first as default for proper sorting
                if ($scope === "single" || $scope === "friends") {
                    array_shift($content_unit);
                }
                //create array for this type if not created yet
                if (!isset($$type)) {
                    $$type = array();
                }
                //push content unit into type array
                array_push($$type, $content_unit);
            }
        }
        //sort the type arrays in order of descending. default goes according
        //to first element in array which should be score (unless single=>date).
        if($scope === "tribe" && $type !== "event"){
        // sort alphabetically by name
            usort($$type, 'compare_score');
        }elseif($type === "event"){
            usort($$type, 'compare_event');//may need a custom compare function for events...
        }else{
            usort($$type, 'compare_date');
        }
    }
    //the above block should generate four arrays of arrays. audio, article, video, image
    //each of these arrays should consist of another set of arrays holding all of the 
    //data necesary to dispay their respective content. they'll have been appropriately 
    //sorted according to scope. the call structure will vary by one (score or no score)
    //if scope is single. otherwise html can now be generated homogenously using foreach loop
    //and appropriate customization for content type using a switch? or something more elegant.
    //splode_status must also be taken into account to determine width etc.
    //------------------------------------------------------------------------//
    //
    //
    //
    //If stream media type === all, combine $$type into $all_media
    if($stream_media_type === "mixed"){
        $only_type = "mixed";
        $$only_type = array();
        foreach ($type_array as $type) {
            //echo "printing this type: ".$type;
            //print_r($$type);                                                        //TESTING
            foreach ($$type as $unit) {
                array_push($$only_type, $unit);
            }
        }
        if($scope === "tribe"){
        // sort alphabetically by name
            usort($$only_type, 'compare_score');
        }else{
            usort($$only_type, 'compare_date');
        }
        //rsort($$only_type);
        $type_array = array($only_type);
    }
    //echo "printing only type";
    //print_r($$only_type);
    //
    //
    //
    //
    $response = array();
    foreach ($type_array as $type) {
        //make lim larger for comments
        $tmp_lim = $lim;
        if ($type === "comment") {
            $lim = $lim * 4;
        }
        $n = 0;
        if ($current_id_array === "non_purge") {
            $j = 1;
        } else {
            //$j = $$type["count"] + 1;
            $j = $total_count + 1;
        }
        foreach ($$type as $unit) {
            $unit["stream_type"] = $type;
            //collect all variables for current piece of content
            //must universalize this and create a type depedent switch statment
            //to handle exceptions.
            $thisUID = $unit["unique_id"];
            /*    
            $thisDate = $unit["uploadDate"];
            $thisTitle = $unit["title"];
            $poster = $unit["poster"];
            $vote_state = $unit["vote_state"];
            $thisUID = $unit["unique_id"];
            $thisDescription = $unit["description"];
            $thisLink = $unit["originalLink"];
            $thisHostName = $unit["hostName"];
            $thisTimeAgo = $unit["time_ago"];
            $thisAvatar = $unit["avatar"];
            $this_rgb_r = $unit["rgb_r"];
            $this_rgb_g = $unit["rgb_g"];
            $this_rgb_b = $unit["rgb_b"];
            $thisRatio = $unit["ratio"];
            $splodeclass = "";
            $splodestyle = "";
            $metaType = "";
            $varyingimage = "";
            $color_block = "<div class='color_block button' style='background-color: rgb(" . $this_rgb_r . "," . $this_rgb_g . "," . $this_rgb_b . ")'></div>";


            if ($splode_status === 'no') {
                $thisHeight = $column_width * $thisRatio;
            } else {
                $thisHeight = "";
            }


            $id_tags = "";

            //generate tag array for html tag production
            $tagArray = array();
            array_push($tagArray, $unit["tag1"]);
            array_push($tagArray, $unit["tag2"]);
            array_push($tagArray, $unit["tag3"]);
            array_push($tagArray, $unit["tag4"]);
            array_push($tagArray, $unit["tag5"]);

            $taghtml = "";

            foreach ($tagArray as $tag) {
                if ($tag !== "null") {
                    //$taghtml .= '<div class="tag_module button" title="' . $tag . '" type="tag" onclick="add_to_filter(null, $(this))">' . $tag . '</div>';
                    $taghtml .= '<div class="tag_module" title="' . $tag . '" type="tag" draggable="true" ondragstart="drag(event);" onmouseup="add_to_filter(null, $(this))">' . $tag . '</div>';
                }
            }

            $id_tags = "class='modal_trigger' onclick='openModal(\"viewer\", $(this))' media='" . $type . "' user='" . $log_username . "' uid='" . $thisUID . "' poster='" . $poster . "' order='" . $j . "' date='" . $thisDate . "' alt='streamImage' original='" . $thisLink . "' votestate='" . $vote_state . "' avatar='" . $thisAvatar . "' content_id='" . $content_id . "'";
            
*/
            if ($unit["media"] === "article") {
                $votes_db = "articlevotes";
            } elseif ($unit["media"] === "video") {
                $votes_db = "videovotes";
            } elseif ($unit["media"] === "image") {
                $votes_db = "imagevotes";
            } elseif ($unit["media"] === "sound") {
                $votes_db = "audiovotes";
            }else{
                $votes_db = "no_media";
            }
            if ($votes_db !== "no_media") {
                //query for voting data
                $sql_num = "SELECT * FROM " . $votes_db . " WHERE voter='$log_username' AND content_id='$thisUID' LIMIT 1";
                $query_num = mysqli_query($db_conx, $sql_num);
                $num = mysqli_num_rows($query_num);
                //vote jank
                //$vote_container = "<div class='vote_container' uid='" . $thisUID . "' media='" . $type . "' date='" . $thisDate . "' votestate='" . $vote_state . "' user='" . $log_username . "' metatype='" . $metaType . "'>";
                if ($num < 1) {
                    $unit["vote"] = "no_vote";
                } else {
                    while ($row1 = mysqli_fetch_array($query_num, MYSQLI_ASSOC)) {
                        $unit["vote"] = $row1['token'];
                    }
                }
            }

            /*
            $vote_container .= "</div>";
            //collect variables unique to current type

            $descrip_block = "<div class='descrip_block'>Posted by <a href='#' onclick='go_to_person(\"" . $poster . "\")'>" . $poster . "</a> " . $thisTimeAgo . " with <span class='vote_tally' uid='" . $thisUID . "'>" . $vote_state . "</span> votes<p class='description_text'>" . $thisDescription . "</p></div>
                    <div class='info_block'>
                        <div class='tag_block'>" . $taghtml . "</div></div></div>";

            switch ($type) {
                case "sound":
                    //$thisRatio = $unit["ratio"];
                    $audioCode = $unit["audio_code"];
                    $sc_user = $unit["sc_user"];
                    $art_url = $unit["art_url"];
                    //also make sure to use $thisLink, as it is required by sc law
                    //$varyingimage = "<iframe width='100%' height='166' scrolling='no' frameborder='no' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/".$audioCode."'></iframe>";
                    //$varyingimage = $vote_container . "<div class='play_song' status='not_playing' onclick='load_sound(\"" . $thisLink . "\", this)' sclink='" . $thisLink . "' order='" . $j . "'></div><img " . $id_tags . " src='" . $art_url . "' audiocode='" . $audioCode . "' style='height: " . $thisHeight . "px' hwratio='" . $thisRatio . "'>" . $color_block . "<div class='song_counter'><div class='progress_bar'></div><div class='time_counter'><span class='current_time'></span> of <span class='total_time'></span></div></div>";
                    break;
                case "video":
                    //$thisRatio = $unit["ratio"];
                    $videoID = $unit["videoID"];
                    $imageLocation = $unit["imageSource"];
                    //$varyingimage = $vote_container . "<img " . $id_tags . " src='" . $imageLocation . "' vid='" . $videoID . "' style='height: " . $thisHeight . "px' hwratio='" . $thisRatio . "'>" . $color_block;
                    break;
                case "image":
                    $imageLink = $unit["imageLink"];
                    $imageSource = $unit["imageLocation"];
                    $varyingimage = $vote_container . "<img " . $id_tags . " src='" . $imageSource . "' imagelink='" . $imageLink . "' style='height: " . $thisHeight . "px' hwratio='" . $thisRatio . "'>" . $color_block;
                    break;
                case "article":
                    //$thisContent = $unit["content"];
                    $thisFrame_stat = $unit["frame_stat"];
                    $imageLocation = $unit["imageSource"];
                    $varyingimage = $vote_container . "<img " . $id_tags . " src='" . $imageLocation . "' hostname='" . $thisHostName . "' frame='" . $thisFrame_stat . "' style='height: " . $thisHeight . "px' hwratio='" . $thisRatio . "'>" . $color_block;
                    break;
                case "comment":
                    $data = $unit["data"];
                    $parent_id = $unit["parent_id"];
                    $content_id = $unit["content_id"];
                    $content_type = $unit["content_type"];
                    //display data (partially atleast) and link to convo using
                    //parent and unique id's
                    //$thisTitle = $parent_id;
                    $varyingimage = '<a class="modal_trigger" href="index.php?u=' . $content_id . '&m=' . $content_type . '&c=' . $thisUID . '"><div content_type=' . $content_type . ' content_id=' . $content_id . ' class="comment"><img src="' . $thisAvatar . '"><span><span onclick="go_to_person(\'' . $poster . '\')">' . $poster . '</span><span class="vote_tally" uid="' . $thisUID . '">, ' . $vote_state . '</span> votes</span><br>' . $data . '</div></a>';
                    $descrip_block = "</div>";
                    break;
                case "advert":
                    //non-universal advert html goes here.
                    //EITHER UPDATE serv_count here or as separate ajax
                    //if performance gains are needed.
                    $upate_serv_count = "UPDATE adverts SET serv_count=(serv_count+1) WHERE unique_id='$thisUID' LIMIT 1";
                    $query_serv_c = mysqli_query($db_conx, $upate_serv_count);
                    break;
            }

            if ($splode_status === 'no') {
                $splodeclass = " tristream";
            } else {
                $splodeclass = " unistream";
                $splodestyle = "style='visibility:hidden;'";
            }

            //take splode_status into account while determining width
            //$splode_status
            //generate html for this specific content_unit eg
            //get voting information on this piece of content



            $responseText .= "<div class='media_container " . $splodeclass . "' " . $splodestyle . " order='" . $j . "' uid='" . $thisUID . "' media='" . $type . "'>
                    <h1 class='button'>" . $thisTitle . "</h1>";


            if ($poster === $log_username) {
                $responseText .= "<a class='delete' href='#' onclick='delete_content(\"" . $thisUID . "\",\"" . $type . "\")'></a>";
            }
            //avatar='".$thisAvatar."'


            $responseText .= "<div class='glass'>" . $varyingimage . "<div class='share_content' content_type='" . $content_type . "' content_id='" . $content_id . "' uid='" . $thisUID . "' media='" . $type . "' onclick='share($(this))'></div></div>" . $descrip_block;
*/


            ///////////////////////////////////////////////////////////
            //make sure to include voting block html as well and check as 
            //necessary via DB queries
            $unit["order"] = $j;
            $unit["nth"] = $n;
            $n++;
            $j++;
            array_push($response, $unit);
            
            if ($n >= $lim) {
                break 1;
            }
        }
        //indicate that block of given type is finished with delimeter, but only if i>=18
        /*if ($n === 0) {
            $responseText .= "<div class='upload_more_image media_container" . $splodeclass . "' media='" . $type . "'><img src='" . $root . "/sourceImagery/upload_cloud.png' onload='attachFunctions()'><h1>Upload more content!</h1></div>";
        }else{
            $responseText .= "<div class='loadmore_image media_container" . $splodeclass . "' media='" . $type . "'><img src='" . $root . "/sourceImagery/spaceholder_small.png' onload='attachFunctions(); //detect_loadmore();'></div>";
        } */
        //put lim back
        if ($type === "comment") {
            $lim = $tmp_lim;
        }
        //$responseText .= "|*|*|delimiter|*|*|";
    }
    //$response["criteria"] = $criteria_date;
    echo json_encode($response);
    exit();
    //upon receiveing response in the JS, break up according to delimiters
    //then check for type and allocate appropriately.
    //Once option is to loop the ajax and only load a couple peices of content
    //at a time.
}
?>
