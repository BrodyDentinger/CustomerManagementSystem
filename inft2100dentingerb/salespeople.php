<?php

$title = "Sales People";
$file = "salespeople.php";
$date = "Nov 02, 2023";
$desc= "Sales people page for lab 2.";

/*
Name: Brody Dentinger
File: salespeople.php
Date: Nov 02, 2023
Course Code: INFT2100
*/

/*
 * Course Code: INFT 2100
 * 
 * Description: Sales people page for lab 2.
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
        ob_flush();
    }

    // Check if the user is an administrator
    if($_SESSION['user_type'] != ADMINISTRATOR){
        header("Location: ./dashboard.php");
        $_SESSION['unauthorized_message'] = "You are not authorized to access that page.";
    }


    // When user loads page for the frist time, set all values to empty
    if($_SERVER["REQUEST_METHOD"] == "GET"){

        // Reset all the value fields in the array to ""
        $sales_person_fields[0]["value"] = "";
        $sales_person_fields[1]["value"] = "";
        $sales_person_fields[2]["value"] = "";
        $sales_person_fields[3]["value"] = "";

        
    // elif , the server request is POST ... therefore, the user has pressed submit
    }else if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Use the hidden field in the "is-active" form, to distinguish if the user has clicked the "update" button, (and therefore does not want to validate or enter a new 
        //sales person.)
        if($_POST['form_action'] != 'is_active_clicked')
        {

             // Collect variables from form POST
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $extension = trim($_POST['extension']);

            // Validate the data
            // Validate if both id and password are set, as a back-up validation method to "required" in the form.
            if(isset($first_name) && isset($last_name) && isset($email) && isset($extension))
            {   
                // Initialize an error counter
                $error_counter = 0;

                // Reset our validation message to empty
                $_SESSION['validation_message'] = "";

                // If extension is not numeric, or extension is not a positive number
                if (!is_numeric($extension) || $extension <= 0){
                    
                    // Increment error counter
                    $error_counter++; 

                    // Error message to user 
                    $_SESSION['validation_message'] .= "Extension must be a positive number. You entered: ". $extension . "\n";

                    // Empty out the post variable
                    $extension = "";
                }

                // If email is not in valid format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                {
                    // Increment error counter
                    $error_counter++;

                    // Error message to user
                    $_SESSION['validation_message'] .= "Email must be in the correct format. You entered: " . $email . "\n";

                    // Clear the post value
                    $email = "";
                } 

                // Set the values of our sales person fields array to the values collected OR removed via the validation process
                $sales_person_fields[0]["value"] = $first_name;
                $sales_person_fields[1]["value"] = $last_name;
                $sales_person_fields[2]["value"] = $email;
                $sales_person_fields[3]["value"] = $extension;
    
                if($error_counter == 0)
                {
                    // Connect to the DB
                    $connection = db_connect();

                    // Set values for data we don't gather but need for data base insertion
                    $currentTimestamp =  date("Y-m-d H:i:s");
                    // Execute Prepared SQL insertion
                    $result = pg_execute($connection, 'insert_salesperson', array($email, $first_name, $last_name, "password", $currentTimestamp, $currentTimestamp, $extension, SALESPERSON));

                    // if results of our executed query are more than 0?
                    if ($result)
                    {
                    // Create a successfully insertion message.
                        $_SESSION['insertion_message'] = "Record Inserted Successfully.";

                        // Clear all fields
                        $sales_person_fields[0]["value"] = "";
                        $sales_person_fields[1]["value"] = "";
                        $sales_person_fields[2]["value"] = "";
                        $sales_person_fields[3]["value"] = "";

                    }
                    // Else, record was not inserted successfully
                    else
                    {
                        $_SESSION['insertion_message'] = "Record Insertion Failed.";
                    }
                }    
            }
        }

        // Else if, the hidden field has been submitted from the is_active form, and the user wants to update the active status of that salesperson.
        else if ($_POST['form_action'] == 'is_active_clicked')
        {

            // Collect the user it's relevant for
            $user_to_status_change = $_POST['user_id'];
            //echo($user_to_status_change);
            // Collect the active versus inactive status
            $active_or_inactive = $_POST['user_status'];
            //echo($active_or_inactive);

            // Determine whether to change the user_type to SALESPERSON OR DISABLEDSALESPERSON based on active status
            if($active_or_inactive == 'active'){

                // Connect to the DB
                $connection = db_connect();

                // Execute prepared statement to update the users user_type
                $result = pg_execute($connection, 'update_salesperson_usertype', array(SALESPERSON, $user_to_status_change));

            }
            else if ($active_or_inactive == 'inactive'){

                // Connect to the DB
                $connection = db_connect();

                // Execute prepared statement to update the users user_type
                $result = pg_execute($connection, 'update_salesperson_usertype', array(DISABLED.SALESPERSON, $user_to_status_change));

            }

            // if resulting query was successful
            if ($result)
            {
                // Success Message.
                $_SESSION[''] = '';
            }


        }   
    }
?>

<?php 
// Dynamic table population section

// Set page and selected_page to 1 by default
$page = 1;
$selected_page = 1;

// Set results per page to however many you'd like, this needs to be in constants eventually 
$results_per_page = RESULTS_PER_PAGE;

// If the form is submitted and the 'page' parameter is set in the URL
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['page'])) {
    // Get the selected page from the URL
    $selected_page = intval($_GET['page']);
    
    // Validate that the selected page is a positive integer
    if ($selected_page > 0) {
        $page = $selected_page;
    } else {
        $selected_page = 1;
    }
}

// Calculate offset ... ... see if we need to move this calculation as well (to functions)
$offset = ($page - 1) * $results_per_page;

// Connect to DB and execute the statement to fetch all salespeople with the limit and offset 
$connection = db_connect();
$results = pg_execute($connection, 'select_page_of_salespeople', array(SALESPERSON, DISABLED.SALESPERSON,$results_per_page, $offset));



?>



<div class="container">
    <div class="text-center">
        <h1 class="cover-heading">Add Sales People</h1>
        <p class="lead">Use the form below to add sales people to the database.</p>
    </div>

    <div class="text-center">
        <?php flash_message_print('insertion_message', 'success');?>
        <?php flash_message_print('insertion_message', 'danger');?>
        <?php flash_message_print('validation_message', 'danger');?>
        <?php display_form($sales_person_fields, $queryResults = null); ?>
        

        <hr/>

        <h1 class="cover-heading">All Sales People</h1>
        <p class="lead">The Table Below Represents all Salespeople in the Database.</p>

        
        <?php display_table_test($salespeople_display_fields, $results); ?>
        
        <!-- The Dyanmic Page Navigation Form  -->
        <form method="GET" action="">
            <label for="page">Go to page:</label>
            <select id="page" name="page">
                <?php
                
                $connection = db_connect();

                // Fetch total number of salespeople (BOTH ADMIN AND SALESPEOPLE)
                $all_salespeople_query = pg_execute($connection,'select_all_salespeople', array(SALESPERSON, DISABLED.SALESPERSON));

                // Get total number of result rows by using pg_num_rows on the results set of our query
                $result_rows = pg_num_rows($all_salespeople_query); 

                // Total pages should equal result rows / records per page ..... 10 should be changed to match the records per page constant
                $totalPages = ceil($result_rows / $results_per_page);


                // for each number in total pages, create an option value for that page
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<option value=\"$i\"";
                    if ($i == $selected_page) {
                        echo " selected";
                    }
                    echo ">Page $i</option>";
                }
                ?>
            </select>
            <button type="submit">Go</button>
        </form> 
    </div>
</div>

<!-- <script>
    console.log("Result Rows: <?php echo $result_rows; ?>");
    console.log("Division Result: <?php echo $result_rows / $results_per_page; ?>");
    console.log("Total Pages: <?php echo $totalPages; ?>");
</script> -->


<?php
include "./includes/footer.php";
?>    