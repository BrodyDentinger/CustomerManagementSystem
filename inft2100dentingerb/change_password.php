<?php

$title = "Password Update";
$file = "change_password.php";
$date = "Nov 09, 2023";
$desc= "Password update page for lab 3.";

/*
Name: Brody Dentinger
File: change_password.php
Date: Nov 09, 2023
Course Code: INFT2100
*/

/*
 * Course Code: INFT 2100
 * 
 * Description: Password update page for lab 3.
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
    // Check if user session variable has not been set , which means user is not signed in. Therefore, redirect them to sign-in page.
    if($_SESSION['user'] == ''){
        header("Location: ./sign-in.php");
    }

?>

<?php 
    // if the user is loading the page for the first time 
    if($_SERVER["REQUEST_METHOD"] == "GET"){

        // Reset all the "value" fields in the password fields to blank
        $password_fields[0]["value"] = "";
        $password_fields[1]["value"] = "";


    // elif , the server request is POST ... therefore, the user has pressed submit
    }else if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Collect variables from form POST
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm']);

        // Validation -------------------------------------------------------
        // Both password fields should be not empty, be >= 3 characters long, and match

        // if either password field is not set or is empty
        if(isset($password) && $password != "" && isset($confirm_password) && $confirm_password != "")
        {

            // then check is passwords match
            if($password === $confirm_password)
            {

                // if they match we can just validate for password now, since they are the same
                if(strlen($password) < 3 || strlen($password) > 50)
                {
                    // Error message
                    $_SESSION["validation_message"] .= "Error: Password Field Must Be between 3 and 50 characters. \n";
                    // Empty the invalid data
                    $password = "";
                }

                // Else, all validation has passed and we can update the password
                else
                {
                    // connect to db
                    $connection = db_connect();

                    // execute the sql update statement which updates the password field for the id === $_SESSION[id];
                    $result = pg_execute($connection, 'user_update_password', array($password ,$_SESSION['id'])); 

                    // if query was successful
                    if($result){

                        // Provide a success message.
                        $_SESSION['insertion_message'] = "Password Updated Successfully for " . $_SESSION['user'] . " " . $_SESSION['last_name'];

                        // Redirect to the dashboard with the successful message
                        header("Location: ./dashboard.php");
                
                    }
                    // else it wasn't successful and provide an error message.
                    else
                    {
                        $_SESSION['insertion_message_failed'] = "Record Insertion Failed.";
                    }
                }
            }
            // else passwords don't match
            else
            {
                // Error message
                $_SESSION["validation_message"] .= "Error: Passwords Must Match. \n";
                // Empty the invalid data
                $password = "";
                $confirm_password = "";
            }
        }

        // Else the passwords are either empty or not set.
        else{
            // Error Message
            $_SESSION["validation_message"] .= "Error: Both Password Fields Cannot be Empty. \n";
        }
    }
?>



<div class="container">
    <div class="text-center">
        <h1 class="cover-heading">Update Password</h1>
        <p class="lead">Use the form below to update your password.</p>
    </div>

    <div class="text-center">
        <?php flash_message_print('validation_message', 'danger');?>
        <?php flash_message_print('insertion_message_failed', 'danger');?>
        <?php display_form($password_fields);?>
    </div>
</div>

<?php
include "./includes/footer.php";
?>    