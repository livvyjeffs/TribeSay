<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!empty($_POST['data'])){
$data = $_POST['data'];
$fname = mktime() . ".txt";//generates random name

$file = fopen("upload/" .$fname, 'w');//creates new file
fwrite($file, $data);
fclose($file);
}

?>