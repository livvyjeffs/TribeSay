<?php

include_once("lib/Stripe.php");
if (isset($_POST["stripeToken"])) {
    
    
// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://manage.stripe.com/account
Stripe::setApiKey("sk_test_L00tSGdOj0DcK52yqKShBoGO");
// Get the credit card details submitted by the form
$token = $_POST['stripeToken'];
$email = $_POST["stripeEmail"];
//
//
//          add customer to subscription plan and charge them:
//
$customer = Stripe_Customer::create(array(
  "card" => $token,
  "description" => "six month plan",  
  //"plan" => "wiredmonkeypig",
  "email" => $email)
);
echo $customer;
// 
// 
//          Above - subscripe customer to a plan and charge.
//          Below - Charge them this once.
// 
//
// Create the charge on Stripe's servers - this will charge the user's card
    
/*
    try {
        $charge = Stripe_Charge::create(array(
                    "amount" => 4500, // amount in cents, again
                    "currency" => "usd",
                    "card" => $token,
                    "description" => "payinguser@example.com")
        );
    } catch (Stripe_CardError $e) {
        // The card has been declined
        echo "card declined";
    }
    
    */
}

?>
