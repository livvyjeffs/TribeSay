<?php
error_reporting(E_ERROR | E_PARSE);
include_once("php_includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if ($user_ok !== true || $log_username === "") {
    header("location: index.php?s");
    exit();
}
//generate list of draggable tags
$sql = "SELECT * FROM tags ORDER BY total DESC";
$query = mysqli_query($db_conx, $sql);
$i = 0;
$tagContentsArray = array();
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $tagName = $row['name'];
    array_push($tagContentsArray, $tagName);
    $i++;
}
//generate favorites
$favoriteTags = "";
$sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
$query = mysqli_query($db_conx, $sql);
$i = 0;
$favoritesArray = array();
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $tagName = $row['tagname'];
    $favoriteTags .= '<div class="tag_module" title="' . $tagName . '" type="favorite" draggable="true" ondragstart="drag(event)"><div class="tag_text" tag="' . $tagName . '">';
    $favoriteTags .= $tagName;
    $favoriteTags .= '</div><div class="delete-tag button" title="remove this tag" onclick="removeTag($(this).parent()); removeFavorite(this.parentNode.title);">x</div>';
    $favoriteTags .= '</div>';
    array_push($favoritesArray, $tagName);
    $i++;
}
$tagContentsArray = array_diff($tagContentsArray, $favoritesArray);
$sql = "SELECT * FROM tags ORDER BY total DESC";
$query = mysqli_query($db_conx, $sql);
$i = 0;
$tagContainerContents = "";
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $tagName = $row['name'];
    if (in_array($tagName, $tagContentsArray) && $tagName !== "null") {
        $total = $row['total'];
        $tagContainerContents .= '<div class="tag_module" title="' . $tagName . '" type="search" draggable="true" ondragstart="drag(event)"><div class="tag_text" tag="' . $tagName . '">' . $tagName . "</div><div class='tag_total'> x " . $total . '</div></div>';
        $i++;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TribeSay Tag Search</title>
        
        <?php include_once("standardhead.php") ?>            
        
        <link rel="stylesheet" href="<?php echo $root; ?>/style/tagsearch.css"/> 
        <link rel="stylesheet" href="<?php echo $root; ?>/style/grid.css"/> 
        
        <script src="<?php echo $root; ?>/js/tags.js"></script>
        <script src="<?php echo $root; ?>/js/dragndrop.js"></script> 
      
    </head>
    <body>
        <?php include_once("analyticstracking.php") ?>
        <?php include_once("template_pageTop.php"); ?>
   
    <div id="main_tags">
        <h1>Tags</h1>
        <div id="tag_search">
            <input id="tagFilter" type="search" placeholder="search for tags" onkeyup="filterTags();">
            <select id="tagSearchFilter" onchange="filterTags();">
                <option value="popular">Most Popular</option>
                <option value="alphabetical" >Alphabetical</option>
            </select>
        </div>
        <div id="tag_container">
            <?php echo $tagContainerContents; ?>
        </div>
        <div id="favorite_tags">
            <h1>My Favorite Tags <p style="font-size: 10px;">(drag your favorite tags here)</p></h1>
            <div id="favorite_tags_container" ondrop="drop(event); updateFavorites(event);" ondragover="allowDrop(event);">
                <?php echo $favoriteTags; ?>
            </div>
        </div>

    </div>
   
    <?php include_once("template_pageBottom.php"); ?>
        
</body>
</html>