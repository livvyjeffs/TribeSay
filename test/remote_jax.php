<?php

//posting api designed for video and article posting

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
$data = array();
$auth = $_GET["auth"];
if ($auth === "japes") {
    $db_conx = mysqli_connect("localhost", "martianmartin147", "sunny", "social");
    if (!mysqli_ping($db_conx)) {
        $data["message"] = "ping failed";
    } else {
        $data["message"] = "api_ping_successful";
        $url = $_GET["url"];
        $tags = explode(",", $_GET["tags"]);
        $type = 'article';//change this
        $username = $_GET["username"];
        $description = $_GET["description"];
        //parse tags into separate variables
        $tags_array = array("tag1", "tag2", "tag3", "tag4", "tag5");
        $i = 0;
        foreach ($tags_array as $tag) {
            if (isset($tags[$i])) {
                $$tag = $tags[$i];
            } else {
                $$tag = "NULL";
            }
            $i++;
        }
        //get info with api's
        switch ($type) {
            case 'article':
            case 'video':
                //call extract api here using the url received
                $key = "130ce75a704544ad9007ea0d381c1d6b";
                $endpoint = "http://api.embed.ly/1/extract?key=" . $key . "&url=" . $url;
                $json = json_decode(file_get_contents($endpoint));

                $link = $json->provider_url;
                $title = $json->title;
                $img_src = $json->images[0]->url;
                break;
        }
    
        //get content differently
        switch ($type) {
            case 'article':
                $content = $json->content;
                break;
            case 'video':
                $content = $json->media->html;               
                break;
        }
        //encode content for posting
        $content = htmlentities($content);

        //POST TO appropriate system
        //sample return for now
        $data["username"] = $username;
        $data["description"] = $description;
        $data["url"] = $link;
        $data["title"] = $title;
        $data["tag1"] = $tag1;
        $data["tag2"] = $tag2;
        $data["tag3"] = $tag3;
        $data["tag4"] = $tag4;
        $data["tag5"] = $tag5;
        $data["source"] = $img_src;
        $data["content"] = $content;
        
        $data["rgb_r"] = 1;
        $data["rgb_g"] = 1;
        $data["rgb_b"] = 1;
        $data["frame_stat"] = "";
        $data["ratio"] = 1;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $root."/php_parsers/article_system.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch); //reponse text
        $post_response = explode("||", $server_output);
        curl_close($ch);
        
        if ($post_response[0] === "success") {
            $data2 = array();
            //post description as first comment.
            $data2['action'] = "comment_post";
            $data2['parent_unique'] = $post_response[1];                                 
            $data2['content_unique'] = $post_response[1];                                  
            $data2['data'] = $description;
            $data2['content_type'] = $type;
            $data2["level"] = 0;
            $data2["username"] = $username;
            $data2["api_call"] = "calling";

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $root."/php_parsers/comment_system.php");
            curl_setopt($ch2, CURLOPT_POST, 1);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($data2));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $server_output2 = curl_exec($ch2); //reponse text
            $post_response2 = explode("||", $server_output2);
            curl_close($ch2);
            $data["comment_response"] = $server_output2;
        } else {
            $data["message"] = "post_failure";
        }

        $data["test"] = $server_output;


// further processing ....
    }
} else {
    $data["auth_message"] = "failure";
    //JSON-encode and return
}
print json_encode($data);