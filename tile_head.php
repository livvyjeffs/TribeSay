<?php
error_reporting(E_ERROR | E_PARSE);
//prevent autologin u=martin since might be save in cookies or session
include_once("php_includes/abort_martin.php");
//delete above code after a few weeks
include_once("php_includes/check_login_status.php");
include_once("php_includes/convert_date.php");
include_once("php_parsers/meta_getter.php");
//indicate to highlight tribe content
$highlight = "tribe_content";
//stupid yc autologin crap                                                      UPDATE THIS YC LOGIN CODE FOR ONLINE LOGIN VALUES
if ($user_ok === false && $login_status === "no" && $new_signup !== "yes") {
    //header("location: stealthpage.php");
}


function tag($tag_id, $tag_type, $tag_state) {

    $delete_favorite = "<div class='delete-tag button' title='remove this tag' onclick='removeTag($(this).parent()); removeFavorite(this.parentNode.title);'>x</div>";
    $add_favorite = "<div class='add-tag' onclick='add_to_filter(event, \"".$tag_id."\");'>+</div>";
    $delete_tag = "<div class='delete-tag button' title='remove this tag' onclick='removeTag($(this).parent()); remove_from_filter(this.parentNode.title);'>x</div>";
    $add_tag = "<div class='add-tag' onclick='add_to_filter(event, \"".$tag_id."\");'>+</div>";

    $tag_action = "";

    switch ($tag_state) {
        case 'delete_favorite':
            $tag_action = $delete_favorite;
            break;
        case 'add_favorite':
            $tag_action = $add_favorite;
            break;
        case 'delete':
            $tag_action = $delete_tag;
            break;
        case 'add':
            $tag_action = $add_tag;
            break;
    }
    return "<div title='" . $tag_id . "' class='tag_module' type='" . $tag_type . "' draggable='true' ondragstart='drag(event)'><div class='tag_text' tag='". $tag_id ."'>" . $tag_id . "</div>" . $tag_action . "</div>";
}

//olivia code
//generate available selected favs array
//populate selecetd favs html
$sql = "SELECT * FROM selectedfavorites WHERE user='$log_username'";
$query = mysqli_query($db_conx, $sql);
$selectedFavsArray = array();
while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
    $tagName = $row['tagname'];
    array_push($selectedFavsArray, $tagName);
}
//puts in tribe filtering
$sql = "SELECT * FROM userfavorites WHERE user='$log_username'";
$query = mysqli_query($db_conx, $sql);
$listed_favs = "";
if (mysqli_num_rows($query) === 0) {
    $listed_favs = "<p>Make your own tribe. Drag and drop your favorite tags here.</p>";
} else {
    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        $tagName = $row['tagname'];
        //if (in_array($tagName, $selectedFavsArray) === false) {
        $listed_favs .= tag($tagName, 'favorites', 'add_favorite');
        //"<div class='tag_module' id='tag_" . $tagName . "' title='" . $tagName . "'>" . $tagName . "<div class='add-tag' title='" . $tagName . "' onclick='add_to_filter(event);'></div></div>";
        //}
    }
}
?>
<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/Article">
    <head>
        <!--Redirect  to special page-->
        <meta property="og:title" content="<?php echo $meta_title; ?>"/>
        <meta property="og:description" content="<?php echo $meta_description; ?>"/>
        <meta property="og:image" content="<?php echo $meta_image_src; ?>"/>

        <meta itemprop="name" content="<?php echo $meta_title; ?>">
        <meta itemprop="description" content="<?php echo $meta_description; ?>">
        <meta itemprop="image" content="<?php echo $meta_image_src; ?>">

        <title><?php echo $meta_title; ?></title>
        <meta name="description" content="<?php echo $meta_description; ?>" />

        <link rel="stylesheet" href="<?php echo $root; ?>/style/mainpage.css?version=<?php echo $version_variable; ?>"/>   
        <link rel="stylesheet" href="<?php echo $root; ?>/style/comment_testing.css?version=<?php echo $version_variable; ?>"/>   
        <link rel="stylesheet" href="<?php echo $root; ?>/style/modal.css?version=<?php echo $version_variable; ?>"/> 
        <link rel="stylesheet" href="<?php echo $root; ?>/style/searchbar.css?version=<?php echo $version_variable; ?>"/>
        <link rel="stylesheet" href="<?php echo $root; ?>/style/debug.css?version=<?php echo $version_variable; ?>"/>
        
        <?php include_once("standardhead.php") ?>
        
         <link rel="stylesheet" href="<?php echo $root; ?>/style/small_phone.css?version=<?php echo $version_variable; ?>"/>

        <script src="<?php echo $root; ?>/scraping/api_modules/js_scraping_services.js?version=<?php echo $version_variable; ?>"></script>

        <script src="<?php echo $root; ?>/js/toggle_functions.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/ajax.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/load_more_content.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/vote_system.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/new-modal.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/article_formatting.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/sound.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/masonry.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/tags.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/dragndrop.js?version=<?php echo $version_variable; ?>"></script> 
        <script src="<?php echo $root; ?>/js/checkingloadmore.js?version=<?php echo $version_variable; ?>"></script> 
        <script src="<?php echo $root; ?>/js/mainpage.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/comments.js?version=<?php echo $version_variable; ?>"></script>
        <script src="<?php echo $root; ?>/js/ellipsis.js?version=<?php echo $version_variable; ?>"></script>
        <script src='<?php echo $root; ?>/js/searchbar_TEST.js?version=<?php echo $version_variable; ?>'></script>

        <script src='<?php echo $root; ?>/js/extractor_api.js?version=<?php echo $version_variable; ?>'></script>

        <script src='<?php echo $root; ?>/js/index_bottom.js?version=<?php echo $version_variable; ?>'></script>
        <script src='<?php echo $root; ?>/js/averagecolor.js?version=<?php echo $version_variable; ?>'></script>
        
        

        <?php include_once("analyticstracking.php") ?>

    </head>