<?php
require("../libs/sendgrid-php/sendgrid-php.php");
include_once("../email_tem/no_mobile.php");
 
$sendgrid = new SendGrid('TribeSay', 'shitsocial8');

$mail = new SendGrid\Email();
$mail->addTo('martinmolina147@gmail.com')->
       setFrom('me@bar.com')->
       setSubject('new pw')->
       setText('Hello World!')->
       setHtml(no_mobile_html());

$sendgrid->send($mail);
?>
