<?php
include_once("lib/Stripe.php");
if (isset($_POST["stripeToken"])) {

    if($_GET["s"] === "six_month"){
        $description = "six month";
    }elseif($_GET["s"] === "one month"){
        $description = "one month";
    }

// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://manage.stripe.com/account
    Stripe::setApiKey("sk_test_L00tSGdOj0DcK52yqKShBoGO");
// Get the credit card details submitted by the form
    $token = $_POST['stripeToken'];
    $email = $_POST["stripeEmail"];
//
// Add customer to subscription plan and charge them:
    $customer = Stripe_Customer::create(array(
                "card" => $token,
                "description" => $description,
                "email" => $email)
    );
    echo "YAY you are now a customer";
}else{
    //print landing page
}
?>
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <form action="pay_stripe.php?s=six_month" method="POST">
            <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="pk_test_RQ4ns97m3O0ghCg8tAKrSbJL"
                data-amount="7500"
                data-name="Standard Subscription"
                data-description="TribeSay Marketing Subscription"
                data-image="/128x128.png"
                data-label="6 month plan">
            </script>
        </form>
    </body> 
</html>