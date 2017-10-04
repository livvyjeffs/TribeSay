<?php

$key = "130ce75a704544ad9007ea0d381c1d6b";
$url = "http://www.wired.com/2014/04/gmail-ten/";
$endpoint = "http://api.embed.ly/1/extract?key=" . $key . "&url=" . $url;


$r = file_get_contents($endpoint);

echo "<br><br><br>";

$json = json_decode($r);

//var_dump($json);

echo "<br><br><br>";

echo $json->provider_url;

echo "<br><br><br>";

echo $json->title;

echo "<br><br><br>";

echo $json->content;

?>
