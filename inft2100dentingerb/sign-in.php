<?php

/*
Name: Brody Dentinger
File: sign-in.php
Date: September 11, 2023
Course Code: INFT2100
*/

/*
 * Course Code: INFT 2100
 * 
 * Description: This page acts as the sign in page for lab 1.
 *
 * PHP version 7.1
 *
 * @author Brody Dentinger <brody.dentinger@dcmail.ca>
 * 
 * @version 1.0 (Sept, 25, 2023)
*/


$title = "Sign In";
$file = "sign-in.php";
$date = "Sept 20, 2023";
$desc= "Sign in Page for lab 1 web dev";

include "./includes/header.php";
?>   


<?php 


if(isset($_SESSION['user'])){
    header("Location: ./dashboard.php");
}

// Empty out the error and welcome message every time the page loads 
$welcome_message = "";
$message = "";

// When user loads page for the frist time, set all values to empty
if($_SERVER["REQUEST_METHOD"] == "GET"){
    $id = "";
    $password = "";
    $error_message = "";
    $welcome_message = "";
    //$conn = "";
    
// elif , the server request is POST ... therefore, the user has pressed submit
}else if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Collect variables from form POST
    $id = trim($_POST['id']);
    $password = trim($_POST['password']);

    // Validate if both id and password are set, as a back-up validation method to "required" in the form.
    if(isset($id) && isset($password)){
        // Use our prepared SQL statements to collect variables from form, connect to DB and then to check if the username and password BOTH match. Updates timestamp if successful.
        user_authenticate($id, $password);
    }
}   
   
?>


<form class="form-signin" action="<?php echo $_SERVER['PHP_SELF'];  ?>" method="post">

    <?php 
        
        // if logout_message is set and not empty, echo it, then delete the session variable
        flash_message_print('logout_message', 'success');

        // if error message is set and is not empty, echo it, then delete the session variable
        flash_message_print('error_message', 'danger');

        // if account has been inactivated.
        flash_message_print('inactive_message', 'danger');

        // if unauthorized message is set and not empty, echo it, then delete the session variable
        //flash_message_print('unauthorized_message','danger');
    ?>

    <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>

    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" class="form-control" placeholder="Email address" name = "id" required autofocus value="<?php echo $id; ?>" >

    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" class="form-control" placeholder="Password" name = "password" required value="<?php echo $password; ?>">

    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>

</form>

<?php
include "./includes/footer.php";
?>    