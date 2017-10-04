<?php
include_once("../php_includes/check_login_status.php");
//return all tags with associated numbers
if(isset($_POST["get_all_tags"])){
    //declare variables
    $return_string = "";
    $sql = "SELECT * FROM tags ORDER BY total DESC";
    $query = mysqli_query($db_conx, $sql);
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $name = $row["name"];
        $total = $row["total"];
        if($name !== "null" && $name !== "undefined"){
            $return_string .= $name." x ".$total.",";
        }
    }
    rtrim($return_string, ",");
    echo $return_string;
    exit();
}
//returns tag html to tag page
if (isset($_POST['filterString'])) {
    //generate favorites array
    $sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
    $query = mysqli_query($db_conx, $sql);
    $favoritesArray = array();
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $tagName = $row['tagname'];
        array_push($favoritesArray, $tagName);
    }
    $tagArray = array();
    $tagTestArray = array();
    $filterString = $_POST['filterString'];
    $sortP = $_POST['sortParameter'];
    //modify query if SORT by most popular
    if($sortP === "popular"){
        $sql = "SELECT * FROM tags ORDER BY total DESC";
    }elseif($sortP === "alphabetical"){
        $sql = "SELECT * FROM tags ORDER BY name";
    }
    //execute query that gets all the ordered tags
    $query = mysqli_query($db_conx, $sql);
    if($filterString === ""){
        //colect all tags into arrays
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
            $total = $row['total'];
            $tagName = $row['name'];
            $current = array($tagName, $total);
            array_push($tagArray, $current);
            array_push($tagTestArray, $tagName);
        }
    }else{
        //collect ony tags that match FILTER string into array
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
            $total = $row['total'];
            $tagName = $row['name'];
            if(strpos($tagName, $filterString) !== false){
                $current = array($tagName, $total);
                array_push($tagArray, $current);
                array_push($tagTestArray, $tagName);
            }
        }
    }
    //SORT the array according to sortParameter, this might be redundant
    if($sortP === "alphabetical"){
        sort($tagArray);
    }
    $tagContainerContents = "";
    $i=0;
    //filter favorites out
    $remainingTags = array_diff($tagTestArray, $favoritesArray);
    //iterate through tags arrays 
    foreach($tagArray as $tag){
        $name = $tag[0];        
        //only allow remaining tags ie non-favorites | and non-nul
        if(in_array($name, $remainingTags) && $name !== "null"){
            $total = $tag[1];
            //$tagContainerContents .= '<div title="'.$name.'" class="tag_module" id="tag_'.$name.'" draggable="true" ondragstart="drag(event)">'.$name."<div class='tag_total'> x ".$total.'</div></div>';
            $tagContainerContents .= '<div class="tag_module" title="'.$name.'" type="search" draggable="true" ondragstart="drag(event)"><div class="tag_text button" tag="'.$name.'">'.$name.'</div><div class="tag_total"> x '.$total.'</div></div>';
            $i++;
        }
    }
    echo $tagContainerContents;
    exit();
}
?>
