<?php
error_reporting(E_ERROR | E_PARSE);
$url = file_get_contents("https://medium.com/meta/f774460d5a7d");
$url = preg_replace('/<body/', "<div .body ", $url);
$url = preg_replace('/<\/body>/', "<\/div>", $url);
//echo $url;

//preg_match_all('/body.+/', $url, $matches);
//print_r($matches);
//var_dump(stream_get_wrappers()); 


$page = new DOMDocument;
$page->loadHTMLFile("https://medium.com/meta/f774460d5a7d");
$para = $page->getElementsByTagName("body");
foreach($para as $p){
    //echo $p->textContent;
    //echo $para->ownerDocument->saveHTML($p);
    //echo $p->nodeValue;
}

//echo $page;
$images = $page->getElementsByTagName("img");
foreach($images as $img){
    //echo $img->getAttribute("src");
}

//$domhtml = DOMDocument; 
$xpath = new DOMXPath($page);
$query="/html/body/text()"; //gets all text nodes that are direct children of body

$txtnodes = $xpath->query($query);

foreach ($txtnodes as $txt) {
    //echo $txt->nodeValue;
}

?>

<form action="https://authorize.payments.amazon.com/pba/paypipeline" method="post">
  <input type="hidden" name="amount" value="USD 24.00" >
  <input type="hidden" name="processImmediate" value="0" >
  <input type="hidden" name="signatureMethod" value="HmacSHA256" >
  <input type="hidden" name="accessKey" value="11SEM03K88SD016FS1G2" >
  <input type="hidden" name="collectShippingAddress" value="0" >
  <input type="hidden" name="recurringFrequency" value="1 month" >
  <input type="hidden" name="description" value="BrainTribe Premium Advertising" >
  <input type="hidden" name="amazonPaymentsAccountId" value="XVXXNBZRYTXX3VMFJZ65DVKZ2LMQKXH1J6XFFS" >
  <input type="hidden" name="cobrandingStyle" value="logo" >
  <input type="hidden" name="signatureVersion" value="2" >
  <input type="hidden" name="immediateReturn" value="1" >
  <input type="hidden" name="signature" value="q4u3hDbyOMwTDEIkGFfpzpg1bki75y3sJDJY54qbXdU=" >
  <input type="image" src="http://g-ecx.images-amazon.com/images/G/01/asp/golden_small_subscribe_withlogo_whitebg.gif" border="0">
</form>