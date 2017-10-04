<?php
if(isset($_POST['link'])){
    $link = $_POST['link'];
    $scrapings = "";
    $article = new DOMDocument;
    $article ->loadHTMLFile($link);
    

    //simple xml method
    $xml = simplexml_import_dom($article);
    $images = $xml -> xpath('//img/@src');
    
    print_r($images);
    
    //domxpath method
    $xpath = new DOMXPath($article);
    $imgs = $xpath->query('//img');

    $titles = $article->getElementsByTagName("title");
    foreach($titles as $title){
        echo $title->nodeValue, PHP_EOL;
    }
    foreach($imgs as $image){
        $source = $image->getAttribute("src");
        $scrapings .= '<img src="'.$source.'" alt="default">';
    }
}
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <form method="POST" action="image_scraper.php">
            <input type="text" name="link">
            <input type="submit" value="submit">
        </form>
        <?php echo $scrapings; ?>
    </body>
</html>

