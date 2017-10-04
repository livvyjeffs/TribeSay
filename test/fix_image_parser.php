<?php
$fd = fopen("http://www.nytimes.com/2014/02/07/us/huge-leak-of-coal-ash-slows-at-north-carolina-power-plant.html?hp", "r");

echo $fd;



$content = fread($fd, 40000);
fclose($fd);

echo $content;
exit();
error_reporting(E_ERROR | E_PARSE);
if (isset($_POST['imagesLink']) && $_POST['imagesLink'] !== "") {
    $sourceHTML = "";
    $responseText = "";
    $sourceArray = array();
    $link = urldecode($_POST['imagesLink']);


    
$inputw = file_get_contents($link);
//preg_match_all("~<img src=([^>]+)>~i", $inputw, $output);
preg_match_all("~<img [^>]+>~", $inputw, $output);
print_r($output);

exit();


$article = new DOMDocument;
        $article->loadHTMLFile($link);
        //generate image array
        $images = $article->getElementsByTagName("img");
        foreach ($images as $image) {
            $source = $image->getAttribute("src");
            echo "<img src=".$source.">";
        }
        exit();
    
    echo "this is the link: ".$link."<br><br>"; 
    //checks if the link is directly to an image and if it is then just spits it back as is.
    if (exif_imagetype($link) !== false) {
        $source = $link;
        $sourceHTML .= '<img src="' . $source . '" alt="alt">';
    } else {
        //generate new DOMdoc
        $article = new DOMDocument;
        $article->loadHTMLFile($link);
        //get scheme and host
        $parsedURL = parse_url($link);
        $host = $parsedURL['host'];
        $scheme = $parsedURL['scheme'];
        //generate image array
        $images = $article->getElementsByTagName("img");
        foreach ($images as $image) {
            $source = $image->getAttribute("src");
            echo '<img src="' . $source . '" alt="alt"><br><br>';
            //usually if src starts with one slash then the scheme and host are missing
            if ($source[0] === "/" && $source[1] !== '/') {
                $source = $scheme . "://" . $host . $source;
            } 
            //if starts with // then usually only the scheme is missing
            elseif ($source[0] === "/" && $source[1] === '/') {
                $source = $scheme . ":" . $source;
            }
            //final check to make sure we have a full path
            if (strpos($source, "http://") !== false || strpos($source, "https://") !== false) {
                $sizeProfile = getimagesize($source);
                $imgArea = $sizeProfile[0] * $sizeProfile[1];
                if ($imgArea > 100) {
                    array_push($sourceArray, $source);
                }else{
                    echo "image is too smal <br>";
                }
            }
        }
        foreach ($sourceArray as $source) {
            $sourceHTML .= '<img src="' . $source . '" alt="alt">';
        }
    }
    $responseText .= $sourceHTML;
    echo $responseText;
    exit();
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="../js/ajax.js"></script>
    </head>
    <body>
        <script>
            function scrape(){
                
                var imageLink = encodeURIComponent(document.getElementById("url").value);
                
                var ajax = ajaxObj("POST","fix_image_parser.php");
                ajax.onreadystatechange = function(){
                    if(ajaxReturn(ajax) === true){
                        ////console.log(ajax.responseText);
                        document.getElementById("image_display").innerHTML = ajax.responseText;
                    }
                };
                ajax.send("imagesLink="+imageLink);
            }
        </script>
        <form>
            <input type="url" id="url">
            <input type="button" onclick="scrape();" value="submit">
        </form>
        <div id="image_display">This is where the images will go.</div>
    </body>
</html>