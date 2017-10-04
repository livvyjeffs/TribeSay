<?php
require($_SERVER["DOCUMENT_ROOT"]."/libs/aws/aws-autoloader.php");
$bucket = 'Tribe_Say_Users_Stream';
//create client
$client = Aws\S3\S3Client::factory(array(
    'key' => 'AKIAI3E72U4J2Q3264AA',
    'secret' => '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'
));
$sourceFile = $_SERVER["DOCUMENT_ROOT"].'/sourceImagery/BTlogo.png';
//upload an object
/*$result = $client->putObject(array(
    'Bucket' => $bucket,
    'Key' => 'user/name/filename.ext',
    'SourceFile' => $sourceFile,
    'Metadata' => array(
        'the_man_is' => 'Martin',
        'his dick is' => 'godly'
    )
));*/

// Access parts of the result object
echo $result['Expiration'] . "\n";
echo $result['ServerSideEncryption'] . "\n";
echo $result['ETag'] . "\n";
echo $result['VersionId'] . "\n";
echo $result['RequestId'] . "\n";

// Get the URL the object can be downloaded from
echo $result['ObjectURL'] . "\n";

?>
<img src="https://s3.amazonaws.com/Tribe_Say_Users_Stream/user/aaaaahhh/-556774767.jpg">