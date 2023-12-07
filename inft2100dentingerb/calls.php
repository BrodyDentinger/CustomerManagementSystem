<?php

$title = "Calls Page";
$file = "calls.php";
$date = "Nov 2, 2023";
$desc= "Calls page for lab 2.";

/*
Name: Brody Dentinger
File: calls.php
Date: Nov 2, 2023
Course Code: INFT2100
*/

/*
 * Course Code: INFT 2100
 * 
 * Description: This page acts as the calls page for lab 2.
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

    // Check if the user is a sales person
    if($_SESSION['user_type'] != SALESPERSON){
        header("Location: ./dashboard.php");
        $_SESSION['unauthorized_message'] = "You are not authorized to access that page.";
    }

    else if ($_SESSION['user_type'] == SALESPERSON){
        // Connect to the DB
        $connection = db_connect();


        // Fetch all clients belonging to the current salesperson
        $result = pg_execute($connection, 'fetch_clients_for_salesperson', array($_SESSION['id']));

        
        // If there are more than 0 results
        if(pg_num_rows($result) > 0){

            // Create an array
            $clients_array = array();

            // While there are results, add the row to the array
            while($row = pg_fetch_assoc($result))
            {
                $clients_array[] = $row;
            }

        }
    }

    // When user loads page for the frist time, set all values to empty
    if($_SERVER["REQUEST_METHOD"] == "GET"){

        
    // elif , the server request is POST ... therefore, the user has pressed submit
    }else if($_SERVER["REQUEST_METHOD"] == "POST"){

        
        // Collect variables from form POST
        $client = trim($_POST['dropdown_field']);

        // Fetch current time.
        $currentTimestamp =  date("Y-m-d H:i:s");

        // Execute query that inserts the time of the call, with the client who made it
        $execute_result = pg_execute($connection, 'insert_call', array($client, $currentTimestamp));

        // if results of our executed query are true
        if ($execute_result)
        {
            // Create a successfully insertion message.
            $_SESSION['insertion_message'] = "Record Inserted Successfully.";

        }
        // Else, record was not inserted successfully
        else
        {
            $_SESSION['insertion_message'] = "Record Insertion Failed.";
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
$results = pg_execute($connection, 'select_page_of_calls', array($_SESSION['id'], $results_per_page, $offset));



?>


<div class="container">
    <div class="text-center">
        <h1 class="cover-heading">Add Client Calls</h1>
        <p class="lead">Select the client that the call is for and click submit to record the client and time of call.</p>
    </div>

    <div class="text-center">

        <?php 
            flash_message_print('insertion_message', 'success');
            flash_message_print('insertion_message', 'danger');
            display_form($calls_fields, $clients_array); 
        ?> 

        <hr/>

        <h1 class="cover-heading">All Calls</h1>
        <p class="lead">The Table Below Represents all of the Calls Belonging to Your Clients in the Database.</p>

        
        <?php display_table_test($calls_display_fields, $results); ?>
        
        <!-- The Dyanmic Page Navigation Form  -->
        <form method="GET" action="">
            <label for="page">Go to page:</label>
            <select id="page" name="page">
                <?php
                
                $connection = db_connect();

                // Fetch total number of salespeople
                $all_calls_query = pg_execute($connection,'select_all_from_calls_for_user', array($_SESSION['id']));

                // Get total number of result rows by using pg_num_rows on the results set of our query
                $result_rows = pg_num_rows($all_calls_query); 

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

<?php
include "./includes/footer.php";
?>    