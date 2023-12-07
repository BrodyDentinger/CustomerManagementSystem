<?php

$title = "Clients";
$file = "clients.php";
$date = "Nov 02, 2023";
$desc= "Clients people page for lab 2.";

/*
Name: Brody Dentinger
File: clients.php
Date: Nov 02, 2023
Course Code: INFT2100
*/

/*
 * Course Code: INFT 2100
 * 
 * Description: clients page for lab 2.
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


    // Check authorization level
    // If admin, then pass client_fields
    if($_SESSION['user_type'] == ADMINISTRATOR)
    {

        // Connect to the DB
        $connection = db_connect();
        // Execute query to fetch all salespeople and put it into an assosiative array
        $result = pg_execute($connection, 'select_all_salespeople', array(SALESPERSON, DISABLED.SALESPERSON));

        // returns an assoc array if there are any matches
        if(pg_num_rows($result) > 0)
        {
            // Initialize an empty array to store the results
            $all_salesperson_info = array();

            // While there are results, continue to store them as a row in our array (array of sales people)
            while ($row = pg_fetch_assoc($result)) 
            {
                $all_salesperson_info[] = $row;
            }

            // set the array we will be using for the form_display to be all sales people (since admin need to be able to select from any in the drop down menu)
            $admin_or_salesperson_array = $all_salesperson_info;
        }
    }
    // If salesperson, then pass salesperson_client_fields
    else if ($_SESSION['user_type'] == SALESPERSON)
    {   
        
        // Connect to the DB
        $connection = db_connect();
        // query the database to return whichever salesperson is currently sign-in
        $user_id_selection = pg_execute($connection,'select_current_salesperson', array($_SESSION['id']));

        // returns an assoc array if there are any matches
        if(pg_num_rows($user_id_selection) > 0)
        {   
            // Initialize an empty array to store the results
            $salesperson_info = array();

            // While there are results, continue to store them as a row in our array (array of sales people)
            while ($row = pg_fetch_assoc($user_id_selection)) 
            {
                $salesperson_info[] = $row;
            }

            // set the array we will be using for the form_display to be the current sales person
            $admin_or_salesperson_array = $salesperson_info;
        }
    } 

    if($_SERVER["REQUEST_METHOD"] == "GET"){

        // Reset all the "value" fields in the client_fields array to ""
        $client_fields[0]["value"] = "";
        $client_fields[1]["value"] = "";
        $client_fields[2]["value"] = "";
        $client_fields[3]["value"] = "";
        $client_fields[4]["value"] = "";
        $client_fields[6]["value"] = "";

        // If we have a client_id in our GET super global (EG. A user has clicked on the URL in the table and now the GET URL contains the ID specific to that clicked client)
        if(isset($_GET['client_id']) && $_GET['client_id'] != ""){

            // Set the value of the id to a variable
            $client_id_from_get = isset($_GET['client_id']) ? $_GET['client_id'] : "";

            // Execute SELECT statement for the clicked client
            $connection = db_connect();
            $client_result = pg_execute($connection,'select_client_from_id', array($client_id_from_get));
            
            // Fetch the result as an associative array
            $client_data = pg_fetch_assoc($client_result);

            // Dump the fetched data
            //var_dump($client_data);

            // Change the array values of our clients form to match the record of whichever client was clicked
            $client_fields[0]["value"] = $client_data['first_name'];
            $client_fields[1]["value"] = $client_data['last_name'];
            $client_fields[2]["value"] = $client_data['client_email_address'];
            $client_fields[3]["value"] = $client_data['phone_extension'];
            $client_fields[4]["value"] = $client_data['phone'];
            $client_fields[6]["value"] = $client_data['logo_path'];

            // Create a boolean variable to check for to produce an update statement instead of an insert statement after validation
            $do_update_statement = true;

        }
        // If there is no client_id in the GET super global, set our do update statement to false
        else
        {
            $do_update_statement = false;
            $client_id_from_get = "";
        }
        
        
    // elif , the server request is POST ... therefore, the user has pressed submit
    }else if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Collect variables from form POST
        $hidden_client_id = isset($_POST["hidden_client_id"]) ? intval($_POST["hidden_client_id"]) : "";
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $extension = trim($_POST['extension']);
        $phone = trim($_POST['phone']);
        $salesperson = trim($_POST['dropdown_field']);
        //$logo = trim(strtolower(($_FILES['uploaded_file'])));
        //$client_id_from_get = isset($_GET['client_id']) ? $_GET['client_id'] : "";
        $client_id_from_get = $_POST['hidden_client_id'];


        // Validate the data
        // Validate if both id and password are set, as a back-up validation method to "required" in the form.
        if(isset($first_name) && isset($last_name) && isset($email) && isset($extension) && isset($phone) && isset($salesperson))
        {   

            // Create a string 
            $allowable_extension_string = "";

            // Loop through our allowable image extension array and add each item to the above string
            foreach($allowed_image_extension_array as $value){
                $allowable_extension_string .= $value . ", ";
            }

            // Initialize an error counter
            $error_counter = 0;

            // Reset our validation message to empty
            $_SESSION['validation_message'] = "";
            
            // Upload validation ------------------------------------------------

            // If a file has been sent to the temporary storage area after submission
            if(isset($_FILES['file_field'])){

                // Fetch the extension type
                $file_extension = strtolower(pathinfo($_FILES['file_field']['name'], PATHINFO_EXTENSION));

                // var_dump($temp_file_name . "<br/>");
                // var_dump($target_file_path . "<br/>");
                // var_dump($file_extension . "<br/>");

                // Validate the size
                if ($_FILES['file_field']['size'] > MAX_IMAGE_SIZE) {
                    
                    // Increment error
                    $error_counter++;
                    // Append to our validation error message
                    $_SESSION['validation_message'] .= "Error: File size should not exceed 2MB. Your file size was: ". ($_FILES['file_field']['size']/1000000) . " MB.";
                    // Empty the variable
                    //$logo = "";
                }
                // Validate the extension
                // If the extension is not in our "allowed extension" array
                if(!in_array($file_extension, $allowed_image_extension_array)){

                    // Increment error counter
                    $error_counter++;
                    // Error message
                    $_SESSION['validation_message'] .= "Error: File upload should only include the following formats: " . $allowable_extension_string; 
                    // Empty it
                    //$logo = "";
                }
    
            }
            // else 
            // {
            //     // Increment error counter
            //     $error_counter++;
            //     // Error message
            //     $_SESSION['validation_message'] .= "Error: Must Include a Logo Path."; 
            // }

            

            // If extension is not numeric, or extension is not a positive number
            if (!is_numeric($extension) || $extension <= 0){
                
                // Increment error counter
                $error_counter++; 

                // Error message to user 
                $_SESSION['validation_message'] .= "Extension must be a positive number. You entered: " .$extension ."\n";

                // Empty it out
                $extension = "";
            }

            // If extension is not numeric, or extension is not a positive number
            if (!is_numeric($phone) || $phone <= 0){
                
                // Increment error counter
                $error_counter++; 

                // Error message to user 
                $_SESSION['validation_message'] .= "Phone number must be a positive number. You entered: " .$phone. "\n";

                // Empty it out
                $phone = "";
            }

            // If email is not in valid format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                // Increment error counter
                $error_counter++;

                // Error message to user
                $_SESSION['validation_message'] .= "Email must be in the correct format. You entered: ".$email. "\n";

                // Empty it out
                $email = "";
            } 
            

            // Set all the "value" fields in the array to the variables collected / set to empty via the validation process (for sticky forms)
            $client_fields[0]["value"] = $first_name;
            $client_fields[1]["value"] = $last_name;
            $client_fields[2]["value"] = $email;
            $client_fields[3]["value"] = $extension;
            $client_fields[4]["value"] = $phone;
            
            // If there are no errors
            if($error_counter == 0)
            {   

                // Moving the File to the new Sub Folder from wherever it was uploaded
                if (isset($_FILES['file_field'])){

                    // Set the automatically assigned temporary file name of the uploaded file to a variable
                    $temp_file_name = $_FILES['file_field']['tmp_name'];

                    // Set the target path we will be sending all uploaded photos too
                    $target_file_path = "./uploads/";

                    // Set the full path, file name, and extension
                    $target_file_path_and_name = $target_file_path . $_FILES['file_field']['name'];
                    
                    // move the uploaded file from temp storage to the uploads sub folder
                    if(move_uploaded_file($temp_file_name, $target_file_path_and_name))
                    {
                        // If the file move is successful
                        $file_moved_success = true;
                    }
                    else
                    {
                        $file_moved_success = false;
                        $_SESSION['validation_message'] .= "Error: File move to directory failed.";
                    }
                }

                // if the file is moved successfully, then continue to query performances (update OR insert)
                if($file_moved_success)
                {
                    
                    // If no existing record has been clicked, our do_update_statement will be false, which indicates we want to create a NEW insert record **********
                    if (!isset($hidden_client_id) || $hidden_client_id == "" || $hidden_client_id == NULL)
                    {

                        // Execute the query to insert client into DB
                        // Connect to the DB
                        $connection = db_connect();

                        // Set values for data we don't gather but need for data base insertion
                        $currentTimestamp =  date("Y-m-d H:i:s");

                        // Execute Prepared SQL insertion
                        $result = pg_execute($connection, 'insert_client', array($email, $first_name, $last_name, $phone, $extension, $currentTimestamp, $salesperson, $_FILES['file_field']['name']));

                        $inserted_or_updated_string = "Inserted";

                    }

                    // If our do_update_statement is true, that means the user has clicked an existing client and wants to update the data
                    elseif(isset($hidden_client_id) || $hidden_client_id != "")
                    {
                        $connection = db_connect();

                        $result = pg_execute($connection, 'update_client', array($first_name, $last_name, $email, $extension, $phone, $_FILES['file_field']['name'], $_POST['hidden_client_id']));
                        
                        //$_SESSION['insertion_message'] = "Record for " . . " Updated Successfully.";
                        $inserted_or_updated_string = "Updated";

                    }

                    // if results of our executed query are more than 0?
                    if ($result)
                    {

                        // Create a successfully insertion message.
                        $_SESSION['insertion_message'] = "Record " .$inserted_or_updated_string . " Successfully for " . $first_name . " " . $last_name;
                        
                        // Clear all fields
                        $client_fields[0]["value"] = "";
                        $client_fields[1]["value"] = "";
                        $client_fields[2]["value"] = "";
                        $client_fields[3]["value"] = "";
                        $client_fields[4]["value"] = "";

                    }
                    // Else, record was not inserted successfully
                    else
                    {
                        $_SESSION['insertion_message'] = "Record Insertion Failed.";
                    }

                }
        }
    }
}
                

?>

<?php 
// Dynamic table population section

// Set page and selected_page to 1 by default
$page = 1;
$selected_page = 1;

// Set results per page to however many you'd like
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

// If the signed-in user is a salesperson, results should be only their clients
if($_SESSION['user_type'] === SALESPERSON){
    $connection = db_connect();
    $results = pg_execute($connection, 'select_page_of_clients', array($_SESSION['id'], $results_per_page, $offset));
}

// Else if the signed-in user is an admin, results should show all clients
elseif($_SESSION['user_type'] === ADMINISTRATOR){
    $connection = db_connect();
    $results = pg_execute($connection, 'select_page_of_client_for_admin', array($results_per_page, $offset) );
}

?>


<div class="container">
    <div class="text-center">
        <h1 class="cover-heading">Add or Update Clients</h1>
        <p class="lead">Use the form below to add or update clients. To update a client, simply click on their email address in the clients table, then click submit.</p>
    </div>

    <div class="text-center">


        <?php flash_message_print('validation_message', 'danger');?>
        <?php flash_message_print('insertion_message', 'success');?>
        <?php flash_message_print('insertion_message', 'danger');?>
        <?php display_form($client_fields, $admin_or_salesperson_array, $client_id_from_get);?>
        
        <hr/>

        <h1 class="cover-heading">All Clients</h1>
        <p class="lead">The Table Below Represents all Clients in the Database.</p>


        <?php display_table_test($clients_display_fields, $results); ?>

        <!-- The Dyanmic Page Navigation Form  -->
        <form method="GET" action="">
            <label for="page">Go to page:</label>
            <select id="page" name="page">
                <?php
                
                $connection = db_connect();
                
                if($_SESSION['user_type'] === SALESPERSON){
                // Fetch clients belonging to the current client
                $all_clients_query = pg_execute($connection,'fetch_clients_for_salesperson', array($_SESSION['id']));

                // Get total number of result rows by using pg_num_rows on the results set of our query
                $result_rows = pg_num_rows($all_clients_query);
                }
                elseif($_SESSION['user_type'] === ADMINISTRATOR){
                    // Fetch ALL clients (Since admin has access to all clients)
                    $all_clients_query = pg_execute($connection,'select_all_clients', array());

                    // Get total number of result rows by using pg_num_rows on the results set of our query
                    $result_rows = pg_num_rows($all_clients_query);
                } 

                // Total pages should equal result rows / records per page ..... 10 should be changed to match the records per page constant
                $totalPages = ceil($result_rows / RESULTS_PER_PAGE);


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