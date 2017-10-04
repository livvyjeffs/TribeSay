<?php
echo "Here start the get contents jank check<br>";

$link = "http://media.npr.org/assets/img/2013/04/03/arcimboldodetail_slide-b73bbb4dd07a60ccf60e807c68890a4e32b17ca8-s6-c30.jpg";
echo $link;
echo "<br>";
$getContents = file_get_contents($link);
if($getContents !== false){
    echo "get not false <br>";
}else{
    echo "file get contents returned false <br>";
}
$fileName = basename($link);
$destination = "/tmp/".$fileName;
echo "this is the destination: ".$destination."<br>";
$bytes = file_put_contents($destination, $getContents);
if($bytes !== false){
    echo $bytes;
}else{
    echo "file put contents returned false <br>";
}
$moveResult = move_uploaded_file($destination, $s3root."/stream/poopman/$fileName");
if($moveResult !== true){
    echo "<br>move failed<br>";
}
$uploadCheck = is_uploaded_file($destination);
if($uploadCheck === true){
    echo "file upload success";
}else{
    echo "upload not correct silly!";
}
?>