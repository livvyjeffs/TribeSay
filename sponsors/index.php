<?php

include_once("../php_includes/db_conx.php");
include_once("../libs/stripe/Stripe.php");
include_once("../email_tem/client_registration.php");
require("../libs/sendgrid-php/sendgrid-php.php");
if (isset($_POST["stripeToken"])) {

    if($_GET["x"] === "Gold"){
        $description = "Gold - $500";
        $type = "Gold";
    }elseif($_GET["x"] === "Platinum"){
        $description = "Platinum - $1,000";
        $type = "Platinum";
    }

// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://manage.stripe.com/account
    Stripe::setApiKey("sk_live_bRamaaQ3K8N7YcRzT5BUTAFf");
// Get the credit card details submitted by the form
    $token = $_POST['stripeToken'];
    $email = $_POST["stripeEmail"];
    //collect billing information
//
// Add customer to subscription plan and charge them:
    try {
        $customer = Stripe_Customer::create(array(
                    "card" => $token,
                    "description" => $description,
                    "email" => $email)
        );
        //echo "YAY you are now a customer";
        $confirmation_style = '';
        $landing_page = 'style="display: none"';
        //collect billing data
        $name = $_POST["stripeBillingName"];
        $address = $_POST["stripeBillingAddressLine1"];
        $zip = $_POST["stripeBillingAddressZip"];
        $state = $_POST["stripeBillingAddressState"];
        $city = $_POST["stripeBillingAddressCity"];
        $country = $_POST["stripeBillingAddressCountry"];
        //submit user info to database
        $sql = "INSERT INTO clients(name, email, address, zip, state, city, country, subscription_type, date) VALUES('$name', '$email', '$address', '$zip', '$state', '$city', '$country', '$type', now())";
        $query = mysqli_query($db_conx, $sql);
        
        /*$msg = client_registration($name);
        $sendgrid = new SendGrid('TribeSay', 'shitsocial8');
        $mail = new SendGrid\Email();
        $mail->addTo($email)->
               setFrom('JP@tribesay.com')->
               setReplyTo('JP@tribesay.com')->
               setFromName('TribeSay Sponsorship')->
               setSubject('Action Required')->
               setText('Welcome!')->
               setHtml($msg);
        
        $sendgrid->send($mail);
        */
        
        mail("martin@tribesay.com, jp@tribesay.com, olivia@tribesay.com, ", "SPONSOR ACQUIRED", "customer name: ".$name);
        
    } catch (Stripe_CardError $e) {
        // Since it's a decline, Stripe_CardError will be caught
        $body = $e->getJsonBody();
        $err = $body['error'];
        echo "there was an error with your transaction. You will not be charged. please contact jp@tribesay.com for assistance.";
    }
} else {
    $confirmation_style = 'style="display: none"';
    $landing_page = '';
}

if ($_SERVER["HTTP_HOST"] === 'localhost') {
    $root = 'http://localhost';
} 

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="<?php echo $root; ?>/sourceImagery/dot_icon.ico" />
        <title>TribeSay | FounderFest Sponsorship</title>
        <meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <link rel="stylesheet" href="<?php echo $root; ?>/sponsors/style/stripe_payment.css?version=<?php echo $version_variable; ?>"/>
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
        <script>


            jQuery.fn.overflow = function() {
                ////console.log(this.outerHeight(true) + " <==> " + this.prop('scrollHeight'))
                //alert(this.outerHeight(true) + " <==> " + this.prop('scrollHeight'))
                if (this.outerHeight(true) < this.prop('scrollHeight')) {

                    //||this.outerWidth(true) < this.prop('scrollWidth')
                    return true;
                } else {
                    return false;
                }
            };

        </script>
    </head>
    <body>
        <div id="main">
            <div class="option_container">
                <div id="logo">
                    <img src="<?php echo $root; ?>/sponsors/style/logo.png">
                </div>
                <div class="confirmation" <?php echo $confirmation_style; ?>>
                    <p>Thank you for becoming a FounderFest Sponsor.</p><p>We look forward to working with you!</p>
                    <br>
                    <p style='text-align: right'>&ndash; Team TribeSay</p>
                </div>
                <div class="option" type="Gold" <?php echo $landing_page; ?>>
                    <h1>Gold Sponsorship</h1>
                    <ul>
                        <li>Thumbnail logo placement on marketing materials:</li>
                        <ul>
                            <li>Flyers</li>
                            <li>Emails</li>
                            <li>Social Media Posts</li>
                            <li>Official Event Page</li>
                        </ul>
                        <li>Banner display real-estate at the event</li>
                        <li>Thank you shout out on mic at the end of the event</li>
                    </ul>
                    <form action="index.php?x=Gold" method="POST">
                        <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="pk_live_tRuD4HmioNRZpRdQ2JOAHenl"
                            data-amount="50000"
                            data-allow-remember-me='false'
                            data-name="Gold Sponsorship"
                            data-billing-address='true'
                            data-image="<?php echo $root; ?>/sponsors/style/logo_128.png">
                        </script>
                    </form>
                </div>
                <div class="option" type="Platinum" <?php echo $landing_page; ?>>
                    <h1>Platinum Sponsorship</h1>
                    <ul>
                        <li>Prime logo placement on all marketing materials, including:</li>
                        <ul>
                            <li>Flyers</li>
                            <li>Emails</li>
                            <li>Social Media Posts</li>
                            <li>Official Event Page</li>
                            <li>Note: Startup organizations in DC will be distributing these.</li>
                        </ul>
                        <li>Booth display real-estate at the event</li>
                        <li>Professional photographs of the event featuring your logo/display in them</li>
                        <li>A warm introduction on the mic at the beginning of the event as well as a shout out at the end</li>
                    </ul>
                    <form action="index.php?x=Platinum" method="POST">
                        <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="pk_live_tRuD4HmioNRZpRdQ2JOAHenl"
                            data-amount="100000"        
                            data-allow-remember-me='false'
                            data-name="Platinum Sponsorship"
                            data-billing-address='true'
                            data-image="<?php echo $root; ?>/sponsors/style/logo_128.png">
                        </script>
                    </form>
                </div>
            </div>
        </div>

        <script>

            $(document).ready(function() {
                if ($('#main').overflow() === false) {
                    $('body').css('overflow', 'hidden');
                }
            });

            $(window).resize(function() {
                if ($('#main').overflow() === false) {
                    $('body').css('overflow', 'hidden');
                } else {
                    $('body').css('overflow-y', 'scroll');
                }
            });

        </script>

    </body> 
</html>