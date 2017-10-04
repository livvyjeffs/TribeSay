<?php
include_once("../php_includes/check_login_status.php");
include_once("../php_includes/convert_date.php");
error_reporting(E_ERROR | E_PARSE);
//go on to ajax reponse functionality
if (isset($_POST["media"]) && isset($_POST["uid"])) {
    //declare response array
    //collect uid
    $uid = $_POST["uid"];
    $media = $_POST["media"];
    $cid = $_POST["cid"];
    //define databases to query
    switch ($media) {
        case "sound":
            $conten_db = "audio";
            $votes_db = "audiovotes";
            break;
        case "video":
            $conten_db = "videos";
            $votes_db = "videovotes";
            break;
        case "article":
            $conten_db = "articles";
            $votes_db = "articlevotes";
            break;
        case "image":
            $conten_db = "photostream";
            $votes_db = "imagevotes";
            break;
    }
    //query content db
    $sql = "SELECT * FROM " . $conten_db . " WHERE uniqueID='$uid' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    if(mysqli_num_rows($query) !== 1){
        //echo mysqli_num_rows($query);
        echo "no_data";
        exit();
    }
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
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
                if ($media === "advert") {
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
                    "media" => $media);
                $content_unit["rgb_r"] = $row["rgb_r"];
                $content_unit["rgb_g"] = $row["rgb_g"];
                $content_unit["rgb_b"] = $row["rgb_b"];
                //include any non universal database fields here and generate unit array
                //
                switch ($media) {
                    case "sound":
                        $content_unit["ratio"] = 1.0; //soundcloud constant                       
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
                    case "advert":
                        //non-universal advert fields go here
                        break;
                }
    }
    //query vote db
    $sql = "SELECT * FROM " . $votes_db . " WHERE content_id='$uid' AND voter='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $content_unit["vote"] = $row["token"];
        }
    }else{
        $content_unit["vote"] = "no_vote";
    }
    //get user avatar
    $sql = "select * FROM users WHERE username='$postedUser' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $avatar = $row["avatar"];
        if($avatar === null){
            $content_unit["avatar_path"] = $root."/sourceImagery/default_avatar.png";
        }else{
            $content_unit["avatar_path"] = $s3root."/user/".$postedUser."/".$avatar;
        }
    }
    //define html
    $responseText = json_encode($content_unit);
    //echo back response text
    echo $responseText;
    exit();
    //remember to include special instructions to retreive comments based on 
    //link data
    
}