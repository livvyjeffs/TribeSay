<?php
if(isset($_POST['link'])){
    $link = $_POST['link'];
    $scrapings = "";
    $sourceArray = array();
    $sizeArray = array();
    $article = new DOMDocument;
    $article ->loadHTMLFile($link);
    $titles = $article->getElementsByTagName("title");
    foreach($titles as $title){
        echo $title->nodeValue, PHP_EOL;
        echo $title->nodeName, PHP_EOL;
        echo $title->nodeType, PHP_EOL;
    }
    
    $images = $article->getElementsByTagName("img");
    foreach($images as $image){
        $source = $image->getAttribute("src");
        //$scrapings .= '<img src="'.$source.'" alt="default">';
        array_push($sourceArray, $source);
        $sizeProfile = getimagesize($source);
        $imgArea = $sizeProfile[0] * $sizeProfile[1];
        array_push($sizeArray, $imgArea);
    }
    array_multisort($sizeArray, SORT_DESC, $sourceArray);
    
    
    $scrapings .= '<img src="'.$sourceArray[0].'" alt="default">';
    $elementArray = array();
    foreach($H1s as $h1){
        array_push($elementArray, $h1);
    }
    $firstH1 = $elementArray[0];
    echo $firstH1->nodeValue, PHP_EOL;
    echo $firstH1->nodeName, PHP_EOL;
    echo $firstH1->nodeType, PHP_EOL;
    /*
    //Recursive iteration based on firstChild and nextSibling tests.
    $currentNode = $firstH1;
    $searchComplete = false;
    do{
        if($currentNode->nodeName === "img"){
            $searchComplete = true;
        }elseif($currentNode->firstChild !== null){
            $currentNode = $currentNode->firstChild;
        }elseif($currentNode->nextSibling !== null){
            $currentNode = $currentNode->nextSibling;
        }else{
            do{
                $fertileRelative = false;
                $currentNode = $currentNode->parentNode;
                if($currentNode->nextSibling !== null){
                    $fertileRelative = true;
                    $currentNode = $currentNode->nextSibling;
                }
            }while($fertileRelative === false);
        }
    }while($searchComplete = false);
    echo "<br>";
    echo "this is the first img Node after h1: ";
    echo $currentNode->nodeValue, PHP_EOL;
    echo $currentNode->nodeName, PHP_EOL;
    echo $currentNode->nodeType, PHP_EOL;
     */
}
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <form method="POST" action="otherScraper.php">
            <input type="text" name="link">
            <input type="submit" value="submit">
        </form>
        <?php echo $scrapings; ?>
    </body>
</html>