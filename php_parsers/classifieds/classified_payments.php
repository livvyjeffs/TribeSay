<?php
include_once("../../php_includes/check_login_status.php");
include_once("../../libs/stripe/Stripe.php");
include_once("../../email_tem/client_registration.php");
require("../../libs/sendgrid-php/sendgrid-php.php");

//collect payment type
if(isset($_GET["x"])){
    $payment_type = $_GET["x"];
}elseif(isset($_POST["x"])){
    $payment_type = $_POST["x"];
}else{
    echo "payment type needs to be set";
}

if (isset($_GET["initialize_payment"]) || isset($_POST["initialize_payment"])) {
    //check if card is on file
    $sql = "SELECT credit_confirmed, customer_id FROM classified_clients WHERE username='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $row = mysqli_fetch_row($query);
    $credit_status = $row[0];
    $customer_id = $row[1]; //put in security here if this is not set then deactivate card info.
    //set API key
    Stripe::setApiKey("sk_test_L00tSGdOj0DcK52yqKShBoGO");//change this for production
    
    //If card not active, add customer to subscription plan and charge them:
    if ($credit_status !== "yes" && isset($_POST["stripeToken"])) {
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here https://manage.stripe.com/account      
        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];
        $email = $_POST["stripeEmail"];
        //collect billing information
        try {
            $customer = Stripe_Customer::create(array(
                        "card" => $token,
                        "description" => $payment_type,
                        "email" => $email)
            );
            //echo "YAY you are now a customer";
            //collect billing data
            /*$name = $_POST["stripeBillingName"];
            $address = $_POST["stripeBillingAddressLine1"];
            $zip = $_POST["stripeBillingAddressZip"];
            //$state = $_POST["stripeBillingAddressState"];
            $city = $_POST["stripeBillingAddressCity"];
            $country = $_POST["stripeBillingAddressCountry"];*/
            $customer_id = $customer->id;
            //test in response if credit confirmed
            $credit_confirmed = 'yes';
            //submit user info to database
            $sql = "INSERT INTO classified_clients (username, name, email, credit_confirmed, subscription_active, pin_count, post_count, signup_date, active_regular, active_paid, customer_id)
                                             VALUES('$log_username', '$credit_confirmed', 'no', '0', '0', now(), '0', '0', '$customer_id')";
            $query = mysqli_query($db_conx, $sql);
            
        } catch (Stripe_CardError $e) {
            // Since it's a decline, Stripe_CardError will be caught
            $body = $e->getJsonBody();
            $err = $body['error'];
            echo "there was an error with your transaction. You will not be charged. please contact jp@tribesay.com for assistance.";
        }
    }elseif($credit_status !== "yes"){
        echo "please input your payment information";
        exit();
    }
 
    
    //chose what sort of payment to execute
    if ($payment_type === "regular_subscription") {
        //add cutomer to plan
        $cu = Stripe_Customer::retrieve($customer_id);
        $cu->plan = "regular_events";
        $cu->save();
        //exit
        exit();
    } elseif ($payment_type === "regular_post") {
        $amount = 500;
    } elseif ($payment_type === "pinned_post") {
        $amount = 10000;
    } else {
        echo "incorrect charge type: ".$payment_type;
        exit();
    }
    if (isset($amount)) {
        Stripe_Charge::create(array(
            "amount" => $amount,
            "currency" => "usd",
            "customer" => $customer_id)
        );
    }

    echo "success";
    exit();
}