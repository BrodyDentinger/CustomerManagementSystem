<?php

$title = "Reset/Forgot Password";
$file = "reset.php";
$date = "December 02, 2023";
$desc= "Reset password email link for lab 4.";

/*
Name: Brody Dentinger
File: reset.php
Date: Dec 2, 2023
Course Code: INFT2100
*/

/*
 * Course Code: INFT 2100
 * 
 * Description: Reset password email link for lab 4.
 *
 * PHP version 7.1
 *
 * @author Brody Dentinger <brody.dentinger@dcmail.ca>
 * 
 * @version 1.0 (Sept, 25, 2023)
*/
include "./includes/header.php";
?>


<?php 
    // if the user is loading the page for the first time 
    if($_SERVER["REQUEST_METHOD"] == "GET"){

        // Reset all the "value" fields in the password fields to blank
        $reset_form[0]["value"] = "";

    // elif , the server request is POST ... therefore, the user has pressed submit
    }else if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Collect variables from form POST
        $email = trim($_POST['email']);

        // Validation -------------------------------------------------------

        // If email is NOT in valid format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {

            // Error message to user
            $_SESSION['validation_message'] .= "Email must be in the correct format. You entered: ".$email. "\n";

            // Empty it out
            $email = "";
        } 
        // Else email is valid. 
        else{
            
            // Connect to DB
            $connection = db_connect();

            // Exectute Prep statement to search the users table for a matching email.
            $result = pg_execute($connection, 'search_users_for_email', array($email));

            // If matching email is found (results > 0 )
            if(pg_num_rows($result) > 0){

                // Create data for the mail function
                $to = 'nobody@example.com';
                $subject = 'Customer Relations - Account Reset';
                $message = "We have received a request to reset the account information associated with your Customer Relations account.\n";
                $message .= "For security reasons, we want to verify that this request was initiated by you. If you did not initiate this request or believe it is in error,\n";
                $message .= "please disregard this email. No changes will be made to your account.\n\n";
                $message .= "If you did request an account information reset, please use the following link to complete the process:\n";
                $message .= "[Insert Link to Account Reset Page]\n\n";
                $message .= "Please note that this link will expire in 24 hours for security purposes. If you are unable to complete the reset within this timeframe, you can initiate the process again.\n\n";
                $message .= "As always, it is important to use a strong and unique password for your account. If you have any concerns or questions,\n";
                $message .= "please do not hesitate to contact our support team.\n\n";
                $message .= "Thank you for your attention to this matter, and we appreciate your continued trust in Customer Relations.\n\n";
                $message .= "Best regards,\n\n";
                $message .= "Brody Dentinger\n";
                $message .= "CEO\n";
                $message .= "Customer Relations\n";
                $message .= "Brody.Dentinger@dcmail.ca";
                $headers = array(
                                "From: brody.dentinger@dcmail.com",
                                "Cc: ",
                                "Bcc: brodydentinger@gmail.com",
                                "Reply-To: brody.dentinger@dcmail.com",
                                );

                $email_for_log = "To: " . $to . "\n\r" .implode("\n\r", $headers) . "\n\r" ."Subject: " . $subject . "\n\r" .$message;

                // If the mail reaches the server
                // if (mail($to, $subject, $message, implode("\r\n", $headers))) {

                //     // Log the email to a log file
                //     log_activity("\n" . $currentTimestamp . ". Sent reset account info to: . User: " . $email . ". \n");
                // }
                
                log_activity("\n" . $currentTimestamp . ". Sent reset account info to: " . $email . ". \n");
                log_activity("\n" . $email_for_log . ". \n");

            }
        }
    }
?>



<div class="container">
    <div class="text-center">
        <h1 class="cover-heading">Reset Password</h1>
        <p class="lead">Use the form below to enter your email, a link will be sent to reset login information.</p>
    </div>

    <div class="text-center">
        <?php flash_message_print('validation_message', 'danger');?>
        <?php display_form($reset_form);?>
    </div>
</div>

<?php
include "./includes/footer.php";
?>    