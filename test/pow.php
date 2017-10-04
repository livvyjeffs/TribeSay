<?php
$section = 'politics';
$max_pages = 1;

// include the class
require_once("../libs/Reddit.com-Scraper-PHP-Class-master/reddit.class.php");

// perform a scrape on the appropriate section and pages
//$data = $reddit->scrape($section,$max_pages);

//print_r($data);

echo getenv('REMOTE_ADDR')."<br>";
$ip = '98.190.221.98';
$r = json_decode(file_get_contents("http://freegeoip.net/json/" . $ip));
echo "region: ".$r->region_name;

