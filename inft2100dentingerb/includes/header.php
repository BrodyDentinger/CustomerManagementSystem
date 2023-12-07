<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/styles.css" rel="stylesheet">

    <title>INFT2100 - <?php echo $title; ?></title>
	<!--
	Author: Brody Dentinger
	Filename: <?php echo $file . "\n" ?>
	Date: <?php echo $date . "\n"?>
	Description: <?php echo $desc; ?>
-->

<?php 
	/*
	* Course Code: INFT 2100
	* 
	* Description: This is the header page for Web Development - Intermediate (INFT2100). This page will be echo'd and included to each other web page.
	*
	* PHP version 7.1
	*
	* @author Brody Dentinger <brody.dentinger@dcmail.ca>
	* 
	* @version 1.0 (Septemeber, 29, 2023)
	*/

    require ("./includes/constants.php");
    require ("./includes/db.php");
    require ("./includes/functions.php");

    ob_start();
    session_start();

?>
	
  </head>
  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="./index.php">Home</a>


        <!-- 
        <div class="navbar-nav d-flex flex-row">
            <a class="nav-item nav-link mx-2" href="./index.php">Home</a>
            <?php if (!isset($_SESSION['user'])) { ?>
                <a class="nav-item nav-link mx-2" href="./register.php">Register</a>
                <a class="nav-item nav-link mx-2" href="./sign-in.php">Sign in</a>
            <?php } else { ?>
                <a class="nav-item nav-link mx-2" href="./update.php">Update</a>
                <a class="nav-item nav-link mx-2" href="./sign-out.php">Sign out</a>
            <?php } ?>
        </div>
            -->

        <div class="navbar-nav d-flex flex-row">

            <?php  
                   
                    $salespeople_or_calls_link = "";
                    $salespeople_or_calls_text = "";

                    // fetch current time
                    $currentTimestamp = $currentTimestamp = date("Y-m-d H:i:s");

                    // Dynamic Sign-in Button ------------------------
                    // if the user is signed in
                    if(isset($_SESSION['user']))
                    {   
                        // set the text to sign out
                        $sign_in_or_sign_out = "Sign Out";
                        // set the href to the logout page
                        $sign_in_or_out_href = "./logout.php";
                        // set the update link to update
                        $update_or_blank = "Update";


                        if($_SESSION['user_type'] == ADMINISTRATOR)
                        {
                            // Set the link and text to the salesperson page
                            $salespeople_or_calls_link = "./salespeople.php";
                            $salespeople_or_calls_text = "Sales People";
                        }
                        else if ($_SESSION['user_type'] == SALESPERSON)
                        {
                            // set the link and text to the calls page instead
                            $salespeople_or_calls_link = "./calls.php";
                            $salespeople_or_calls_text = "Calls";
                        }

                    }
                    // else they are not signed in
                    else{

                        // set the text to sign in 
                        $sign_in_or_sign_out = "Sign In";
                        // link the sign-in page
                        $sign_in_or_out_href = "./sign-in.php";
                        // set the update link to blank
                        $update_or_blank = "";
                    }

                ?>

            <a class="nav-item nav-link mx-2" href="./reset.php">Reset</a>
            <a class="nav-item nav-link mx-2" href="./change_password.php"><?php echo $update_or_blank?></a>
            <a class="nav-item nav-link mx-2" href="<?php echo($sign_in_or_out_href) ?> "> <?php echo $sign_in_or_sign_out ?> </a>
        </div>
    </nav>
    <div class="container-fluid">
      <div class="row">
        
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item" >
                <a class="nav-link active" href="./dashboard.php">
                    <span data-feather="home"></span>
                    Dashboard <span class="sr-only">(current)</span>
                </a>
                </li>
                
                <!--
                <li class="nav-item">
                <a class="nav-link" href="./salespeople.php">
                    <span data-feather="file"></span>
                    Sales People
                </a>
                </li>
                -->
                
                <li class="nav-item">
                <a class="nav-link" href="./clients.php">
                    <span data-feather="shopping-cart"></span>
                    Clients
                </a>
                </li>

                <li class="nav-item">
                <a class="nav-link" href="<?php echo($salespeople_or_calls_link)?>">
                    <span data-feather="bar-chart-2"></span>
                    <?php echo $salespeople_or_calls_text?>
                </a>
                </li>

                <!--
                <li class="nav-item">
                <a class="nav-link" href="./calls.php">
                    <span data-feather="users"></span>
                    Calls
                </a>
                </li>

                <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="layers"></span>
                    Integrations
                </a>
                </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Saved reports</span>
                <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
                </a>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="file-text"></span>
                    Current month
                </a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="file-text"></span>
                    Last quarter
                </a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="file-text"></span>
                    Social engagement
                </a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="file-text"></span>
                    Year-end sale
                </a>
                </li>
            
            -->

            </ul>
            </div>
        </nav>

        <main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">