<?php

//this stream generator is designed so that new media types can be easily 
//incorprated. The implementation steps are as follows:
//  1) update $type_array and $databases array with matching key.
//  2) confirm db_schema requirements are met for new_type: 
//      a) tag1-5, postdate
//      b) title, poster, vote_state, uniqueID, description, link, hostname
//      c) new_type_votes (table): voter='logusername'
//  3) update both (2) type-switch statments to account for 1) special variables and 2)html output

error_reporting(E_ERROR | E_PARSE);
include_once($_SERVER["DOCUMENT_ROOT"]."/php_includes/check_login_status.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/php_includes/convert_date.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/php_includes/getFriendsArray.php");
if (isset($_POST['scope']) && isset($_POST['current_id_list']) && isset($_POST['page_owner']) && isset($_POST['splode_status'])) {
    //declare available content types in array
    //declare target content databases in an array with type as key. eg video=>youtube, video=>vimeo, audio=>soundcloud
    $type_array = array("video", "article", "image", "sound", "comment");
    $databases = array("video" => "videos", "article" => "articles", "image" => "photostream", "sound" => "audio", "comment" => "comments");
    //collect posted variables
    $stream_media_type = $_POST['stream_media_type'];
    $current_id_array = $_POST['current_id_list'];
    $column_width = $_POST['column_width'];
    $scope = $_POST['scope'];
    $splode_status = $_POST['splode_status'];
    $trigger = $_POST['trigger'];
    $single_type = "no";
    if (($trigger === "filter" && $splode_status !== "no") || ($trigger === "scope" && $splode_status !== "no")) {
        $single_type = "yes";
    }
    if ($trigger === "filter" || $trigger === "scope") {
        $trigger = "fresh_load";
    }
    if ($splode_status === "no" && $trigger !== "fresh_load") {
        $lim = 2;
    } else {
        $lim = 3;
    }
    if ($trigger === "fresh_load") {
        //load all media types ie splode is natural
        $current_id_array = "non_purge";
    }
    //purging factors
    $page_owner = $_POST['page_owner'];
    //add in conditions for advert
    if ($stream_media_type === "advert") {
        $type_array = array("advert");
        $databases = array("advert" => "advert");
        $single_type = "yes";
        $splode_status = "advert";
        $scope = "tribe";
        //all other variables ie: media_type, current_ids remain same. 
        //trigger=fresh||scroll
        //db_columns, switch statements, html need to be updated
        //sort function needs and "if" and to be tailored
    }
    //delta filter and delta scope will be checked for in JS and if either is positive current_id_array = "non_purge"
    //because js will be replacing all of the content html rather than adding to the currently displayed content.
    //check if current content is being cleared or not. If so, create array from list of currently displayed uniques
    //if it IS being purged
    if ($current_id_array !== "non_purge") {
        $current_id_array = explode(",", $_POST['current_id_list']);
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
          
            }
        }
    }

    //if splode status is not equal to "no", then modify the type_array and 
    //databases so that only data that is current sploded get processed
    if ($trigger !== "fresh_load" || $single_type === "yes") {
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
    //generate criteria that applies to all queries and is based upon selected 
    //favorites for filtering
    $criteria = "";
    $n = 0;
    if (count($selectedFavsArray) !== 0) {
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
            //echo "count: ".$$type[$type] . "\n";
            //echo "start: ".$start_count;
        }else{
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
        } elseif ($type === "advert") {
            $votes_db = "";
        }
        switch ($scope) {
            case "single":
                $sql = "SELECT * FROM " . $base_of_data . " WHERE poster='$page_owner'" . $criteria . " ORDER BY id DESC LIMIT ".$start_count.",40";
                $sql2 = "SELECT * FROM " . $votes_db . " WHERE voter='$page_owner' AND token='UP' ORDER BY id LIMIT ".$start_count.",40";
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
                    $sql = "SELECT * FROM " . $base_of_data . " WHERE" . $friend_criteria . $criteria." ORDER BY id DESC LIMIT ".$start_count.",40";
                    $sql2 = "SELECT * FROM " . $votes_db . " WHERE" . $friend_voters . " token='UP' ORDER BY id DESC LIMIT ".$start_count.",40";
                } else {
                    $sql = "";
                    $sql2 = "";
                }
                break;
            case "tribe":
                if (count($selectedFavsArray) !== 0) {
                    $where = " WHERE";
                } else {
                    $where = "";
                }
                $sql = "SELECT * FROM " . $base_of_data . $where . substr($criteria, 4)." ORDER BY id DESC LIMIT ".$start_count.",40";
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
                }
                //for advert, $voteState = # servs, and score is equal to this.
                if ($type === "advert") {
                    $voteState = $row['serv_count'];
                    $score = $voteState;
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
                    "media_type" => $stream_media_type);
                $content_unit["rgb_r"] = $row["rgb_r"];
                $content_unit["rgb_g"] = $row["rgb_g"];
                $content_unit["rgb_b"] = $row["rgb_b"];
                //include any non universal database fields here and generate unit array
                //
                switch ($type) {
                    case "sound":
                        $content_unit["ratio"] = 1.0; //soundcloud constant                       
                        $content_unit["audio_code"] = $row['audioCode'];
                        $content_unit["sc_user"] = $row["sc_user"];
                        $content_unit["art_url"] = $row["art_url"];
                        break;
                    case "video":
                        $imageName = $row['img_src'];
                        if ($imageName === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageSource"] = $root."/".$imageName;
                        } else {
                            $imageSource = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                            $content_unit["imageSource"] = $imageSource;
                        }

                        $content_unit["ratio"] = 0.74898; //youtube constant
                        $content_unit["videoID"] = $row['videoID'];
                        break;
                    case "article":
                        //$content = html_entity_decode($row['content']);
                        //$content_unit["content"] = nl2br($content);
                        $content_unit["ratio"] = $row["ratio"];
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
                        $content_unit["ratio"] = $row["ratio"];
                        $imageName = $row['filename'];
                        if ($imageName === "sourceImagery/spaceholder.jpg") {
                            $content_unit["imageLocation"] = $root."/".$imageName;
                        } else {
                            $imageLocation = $s3root . '/stream/' . $postedUser . '/' . $imageName;
                            $imageLink = $row['imageLink'];
                            $content_unit["imageLocation"] = $imageLocation;
                        }
                        $content_unit["imageLink"] = $imageLink;
                        break;
                    case "comment":
                        $content_unit["parent_id"] = $row["parent_id"];
                        $content_unit["data"] = nl2br(html_entity_decode($row["data"]));
                        $content_unit["content_id"] = $row["content_id"];
                        $content_unit["content_type"] = $row["content_type"];
                        break;
                    case "advert":
                        //non-universal advert fields go here
                        break;
                }
                //single scope html requires sorting by recentness, so pop off the score
                //otherwise the score can be left first as default for proper sorting
                if ($scope === "single") {
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
        if ($type === "advert") {
            sort($$type);
        } else {
            rsort($$type);
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
            $j = $$type["count"] + 1;
        }
        foreach ($$type as $unit) {
            $unit["type"] = $type;
            //collect all variables for current piece of content
            //must universalize this and create a type depedent switch statment
            //to handle exceptions.
            /*$thisDate = $unit["uploadDate"];
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
            //generate tag array for html tag production
            $tagArray = array();
            array_push($tagArray, $unit["tag1"]);
            array_push($tagArray, $unit["tag2"]);
            array_push($tagArray, $unit["tag3"]);
            array_push($tagArray, $unit["tag4"]);
            array_push($tagArray, $unit["tag5"]);*/
             //query for voting data
            if ($type === "sound") {
                $sql = "SELECT * FROM audiovotes WHERE voter='$log_username' AND content_id='$thisUID' LIMIT 1";
            } else {
                $sql = "SELECT * FROM " . $type . "votes WHERE voter='$log_username' AND content_id='$thisUID' LIMIT 1";
            }
            $query = mysqli_query($db_conx, $sql);
            $row = mysqli_num_rows($query);
            //vote jank
            if ($row < 1) {
                $unit["token"] = "none";
            } else {
                while ($row1 = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                    $unit["token"] = $row1['token'];
                }
         
            }
            //collect variables unique to current type
            /*switch ($type) {
                case "sound":
                    $thisRatio = $unit["ratio"];
                    $audioCode = $unit["audio_code"];
                    $sc_user = $unit["sc_user"];
                    $art_url = $unit["art_url"];
                    break;
                case "video":
                    $thisRatio = $unit["ratio"];
                    $videoID = $unit["videoID"];
                    $imageLocation = $unit["imageSource"];
                   break;
                case "image":
                    $imageLink = $unit["imageLink"];
                    $imageSource = $unit["imageLocation"];
                    break;
                case "article":
                    //$thisContent = $unit["content"];
                    $thisFrame_stat = $unit["frame_stat"];
                    $imageLocation = $unit["imageSource"];
                    break;
                case "comment":
                    $data = $unit["data"];
                    $parent_id = $unit["parent_id"];
                    $content_id = $unit["content_id"];
                    $content_type = $unit["content_type"];
                    //display data (partially atleast) and link to convo using
                    //parent and unique id's
                    //$thisTitle = $parent_id;
                    
                    break;
                case "advert":
                    //non-universal advert html goes here.
                    //EITHER UPDATE serv_count here or as separate ajax
                    //if performance gains are needed.
                    $upate_serv_count = "UPDATE adverts SET serv_count=(serv_count+1) WHERE unique_id='$thisUID' LIMIT 1";
                    $query_serv_c = mysqli_query($db_conx, $upate_serv_count);
                    break;
            }*/
            $unit["order"] = $j;
            $unit["nth"] = $n;
            $n++;
            $j++;

            array_push($response, $unit);
            if ($n >= $lim) {
                break 1;
            }
        }    
        //put lim back
        if ($type === "comment") {
            $lim = $tmp_lim;
        }
      
    }
    echo json_encode($response);
    exit();
    //upon receiveing response in the JS, break up according to delimiters
    //then check for type and allocate appropriately.
    //Once option is to loop the ajax and only load a couple peices of content
    //at a time.
}
?>
