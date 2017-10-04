<?php
if(isset($_POST['link'])){
    echo "link is posted";
    $link = $_POST['link'];
    $scrapings = "";
    $article = new DOMDocument;
    $article ->loadHTMLFile($link);
    
    //get first h1 element
    $allH1 = $article->getElementsByTagName('h1');
    print_r($allH1);
    $firstH1 = $allH1[0];
    echo $firstH1->nodeName, PHP_EOL;
    echo $firstH1->nodeValue, PHP_EOL;
    echo $firstH1->nodeType, PHP_EOL;
    echo "<br>";
    //loop through everything that comes after firstH1 and check if its an image, break when you find the first one...
    echo "begin for <br>";
    for($a=0; $a<$firstH1->childNodes->length; $a++){
        echo $firstH1->childNodes->item($a)->nodeName, PHP_EOL;
        echo $firstH1->childNodes->item($a)->nodeValue, PHP_EOL;
        echo $firstH1->childNodes->item($a)->nodeType, PHP_EOL;
    }
    echo "begin foreach <br>";
    foreach($firstH1->childNodes as $child){
        echo $child->nodeName, PHP_EOL;
        echo $child->nodeValue, PHP_EOL;
        echo $child->nodeType, PHP_EOL;
    }
    $titles = $article->getElementsByTagName("title");
    foreach($titles as $title){
        echo $title->textContent;
    }
}
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <form method="POST" action="article_Scraper.php">
            <input type="text" name="link">
            <input type="submit" value="submit">
        </form>
    </body>
</html>

