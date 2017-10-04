<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

$data = json_encode(array("success" => "Connection valid"));
print_r($data);
exit();
