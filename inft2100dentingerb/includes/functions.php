<?php
/*
Name: Brody Dentinger
File: functions.php
Date: Nov 4, 2023
Course Code: INFT2100
*/


/*
*Will dumb passed argument using pre tags for debugging
*
*Takes 1 parameter, arg
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@returns nothing
*/


function dump($arg){
    echo '<pre>';
    print_r($arg);
    echo '</pre>';
}


/*
*Will redirect the user and flush the buffer
*
*Takes 1 paramter, url
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@returns nothing.
*/
function redirect($url){
    header("Location: " . $url);
    ob_flush();
}



/*
*Will flash a message to the screen
*
*Takes 2 parameters, message_type, and alert_type
*           Message Type is the actual message being flashed, 
            and alert_type is the css alert type (danger, success, etc)
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@returns nothing.
*/
function flash_message_print($message_type, $alert_type)
{
    // Initialize an empty message string to store all messages
    $message_string = '';

    // if logout_message is set and not empty, append it to the message string
    if(isset($_SESSION[$message_type]) && !empty($_SESSION[$message_type])) {
        // Split the session message into lines
        $messages = explode("\n", $_SESSION[$message_type]);

        // Concatenate each message with a line break
        $message_string = implode("<br>", $messages);

        // Clear the error message from the session
        $_SESSION[$message_type] = "";
    }

    // Output the combined messages in a single alert box
    if (!empty($message_string)) {
        echo '<div class="alert alert-' . $alert_type . '">' . $message_string . '</div>';
    }
}

/*
*Will log a specific activity performed by the user to an external file
*
*Takes 1 parameter, activity_message
*           Activity message is the message being logged to the text fiel 
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@returns nothing.
*/
function log_activity($activity_message)
{   
    
    // log sign out activity (Open file, append)
    $handle = fopen('./logs/'.date("Ymd").'_activity.log', 'a');

    // Sign out success at <time>. User <email> sign in.
    fwrite($handle,"$activity_message");
    fclose($handle);
}

/*
*
*
*Takes 2 parameters
    $array = Should be an array of arrays with values to populate the form with
    $queryResults = is an array representative of an SQL query results. Used to dynamically populate the drop down menu elements with options.
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@returns nothing.
*/
function display_form($array, $queryResults = null, $client_id_from_get = null) {
    // Start creating the HTML form element.
    echo "<form class='form-signin' method='post' enctype='multipart/form-data' action='" . $_SERVER['PHP_SELF'] . "' >";
    
    // Loop through each form field in the $array.
    foreach ($array as $field) {
        // Check if the current field is a valid array with required attributes.
        if (is_array($field) && isset($field['name']) && isset($field['label']) && isset($field['type'])) {
            // Extract field attributes from the current array
            $name = $field['name'];
            $label = $field['label'];
            $type = $field['type'];

            // If the value of the "value" key in the current array (as field), and it's value is not set to "",
            if (isset($field['value']) && $field['value'] !== "") {
                // Then set value to the current value that is set in the array
                $value = $field['value'];
            } else {
                // If the 'value' key doesn't exist or is empty, set it to an empty string
                $value = '';
            }

            // Start creating a form element container.
            echo '<div class="form-label-group">';
            // Create a label for the form field.

            if($type != 'dynamic_select'){
                echo '<label for="' . $name . '">' . $label . '</label>';
            }

            // Generate text, email, and number fields --------------------------------------------------------------------------

            // If the input type is text, email, or number, then we can generate a form input of it's corresponding type
            if ($type === 'text' || $type === 'email' || $type === 'number' || $type === 'file' || $type === 'password') {
                echo '<input type="' . $type . '" id="' . $name . '" class="form-control" name="' . $name . '" value="' . $value . '" required>';


            // Generate Dynamic Select (Dynamic drop down menu Section ) --------------------------------------------------------

            // This top one is to generate the drop down menu for the clients page, since the 'id' in user table is 'id', the selection structure is different than below.
            } if ($type === 'dynamic_select' && is_array($queryResults) && $_SERVER["REQUEST_URI"] != "/calls.php" && $_SESSION["user_type"] === ADMINISTRATOR) {
                // Create a dynamic dropdown select element 
                echo '<label for="' . $name . '">' . $label . '</label>';
                echo '<select id="' . $name . '" class="form-control" name="' . $name . '">';
                
                // For each record (one salesperson), create an option in the drop down with the format "ID: 2, First Name Last Name"
                foreach ($queryResults as $resultRow) {
                    $combinedOptionLabel = "ID: " . $resultRow['id'] . " - " . $resultRow['first_name'] . " " . $resultRow['last_name'];
                    $combinedOptionValue = $resultRow['id'];
                    $selected = $combinedOptionValue === $value ? 'selected' : '';

                    echo '<option value="' . $combinedOptionValue . '" ' . $selected . '>' . $combinedOptionLabel . '</option>';
                }
            }

            // This section will check if the signed in user is a Salesperson and the URI is clients. 
            // The purpose of this section is to populate the drop down menu with ONLY the CURRENT salesperson data for insertion into the database record when a new client is created.
            if ($type === 'dynamic_select' && is_array($queryResults) && strtok(basename($_SERVER["REQUEST_URI"]) , '?') === "clients.php" && $_SESSION["user_type"] === SALESPERSON) {
                // Create a dynamic dropdown select element 
                echo '<select id="' . $name . '" class="form-control" name="' . $name . '" style="display: none;">';
                // For each record (one salesperson), create an option in the drop down with the format "ID: 2, First Name Last Name"
                foreach ($queryResults as $resultRow) {
                    $combinedOptionLabel = "ID: " . $resultRow['id'] . " - " . $resultRow['first_name'] . " " . $resultRow['last_name'];
                    $combinedOptionValue = $resultRow['id'];
                    $selected = $combinedOptionValue === $value ? 'selected' : '';

                    echo '<option value="' . $combinedOptionValue . '" ' . $selected . '>' . $combinedOptionLabel . '</option>';
                }
            }

            // Dynamic drop down for calls page... since ID is client_id in the DB insead of id... slightly different formatting required----------
            if ($type === 'dynamic_select' && is_array($queryResults) && strtok(basename($_SERVER["REQUEST_URI"]), '?') === "calls.php") {
                // Create a dynamic dropdown select element 
                echo '<select id="' . $name . '" class="form-control" name="' . $name . '">';
                
                // For each record in our clients belonging to a salesperson query, create an option in the drop down with the same format as above.
                foreach ($queryResults as $resultRow) {
                    $combinedOptionLabel = "ID: " . $resultRow['client_id'] . " - " . $resultRow['first_name'] . " " . $resultRow['last_name'];
                    $combinedOptionValue = $resultRow['client_id'];
                    $selected = $combinedOptionValue === $value ? 'selected' : '';

                    echo '<option value="' . $combinedOptionValue . '" ' . $selected . '>' . $combinedOptionLabel . '</option>';
                }
            }

            // close the select element
            echo'</select>';

            // If we on the clients page create a hidden text field to echo the client_id_from_get
            if(strtok(basename($_SERVER["REQUEST_URI"]) , '?') === "clients.php"){
                
                echo "<input type='hidden' name='hidden_client_id' id='hidden_client_id' value='" . (isset($client_id_from_get) ? htmlspecialchars($client_id_from_get) : "") . "'>";
            }

            // Close the form element container.
            echo '</div>';
        }
    }

    // Add a line break and a submit button to the form.
    echo '<br/>';
    echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>';

    // Close the HTML form.
    echo '</form>';
}


// Display Table Form
function display_table_test($array, $queryResults = null) {


    // Create the start of the table
    echo '<table class = "table">';

    // For each element in our array, enter the inner array
    foreach ($array as $innerArray){

        // Start a table row for the entire block of inner array values
        echo '<tr>';

        // For each element in the inner array, (as key => value pair), echo the value in a table header
        foreach($innerArray as $key => $value){
            echo '<th>'.$value.'</th>';

        }

        // If the user type is an admin, add the Is Active header
        if($_SESSION['user_type'] == ADMINISTRATOR){
            echo'<th> Is Active? </th>';
        }

        // End the table row for the block of inner array values
        echo '<tr/>';
    }

    // // While pg_fetch_assoc is still returning a row
    while ($row = pg_fetch_assoc($queryResults)) {
        //echo(strtok(basename($_SERVER["REQUEST_URI"]), '?') . "<br/>");

    // If on the salesperson page, populate this table (Uses strtok() to only fetch the value before the '?', therefore ignoring URI changes after GET changes it)
        if(strtok(basename($_SERVER["REQUEST_URI"]), '?') === "salespeople.php"){
        
            // Salesperson page: We want to create a table row, then encompass each indivudal column value into a td
            echo '<tr class = "table-row">' . '<td>' .$row['id']. '</td>' . '<td>' .$row['email_address']. '</td>' . '<td>' .$row['first_name']. '</td>' . '<td>' 
                    .$row['last_name']. '</td>' . '<td>' .$row['created_time'].'</td>' . '<td>' .$row['last_access'].'</td>' . '<td>' .$row['phone_extension']. '</td>' 
                    . '<td>' .$row['user_type'].'</td>'             . '<td>' . '<form method="post" action="salespeople.php">'
                    . '<input type="hidden" name="user_id" value="' . $row['id'] . '">'
                    . '<input type="hidden" name="form_action" value="is_active_clicked">'
                    . '<label><input type="radio" name="user_status" value="active" checked> Active</label> </br>'
                    . '<label><input type="radio" name="user_status" value="inactive"> Inactive</label> </br>'
                    . '<button type="submit">Update</button>'
                    . '</form>' . '</td>' . '</tr>';
        }

        // If on the clients page, create a table using the clients column names from the clients table
        if(strtok(basename($_SERVER["REQUEST_URI"]), '?') === "clients.php"){

            // Change the value of every ['logo_path'] to have <img> tags for displaying the actual image 
            $row['logo_path'] = "<img src = \"./uploads/". $row['logo_path'] . "\" width = \"75\" height = \"75\" >" ;

            // Clients page: We want to create a table row, then encompass each indivudal column value into a td   <a href="clients.php?id=1234">testuser@gmail.com</a>
            echo '<tr class = "table-row">' . '<td>' .$row['client_id']. '</td>' . '<td> <a href = "clients.php?client_id='.$row['client_id'] .' ">' .$row['client_email_address']. '</a></td>' . '<td>' .$row['first_name']. '</td>' . '<td>' 
                .$row['last_name']. '</td>' . '<td>' .$row['phone'].'</td>' . '<td>' .$row['phone_extension'].'</td>' . '<td>' .$row['created_time']. '</td>' 
                . '<td>' .$row['salesperson_id'].'</td>' . '<td>' .$row['logo_path'].'</td>' . '</tr>' ;
                '</tr>' ;
        }

        // If on the calls page, populate this table (Uses strtok() to only fetch the value before the '?', therefore ignoring URI changes after GET changes it)
        if(strtok(basename($_SERVER["REQUEST_URI"]), '?') === "calls.php"){

            // Salesperson page: We want to create a table row, then encompass each indivudal column value into a td
            echo '<tr class = "table-row">' . '<td>' .$row['call_id']. '</td>' . '<td>' .$row['client_id']. '</td>' . '<td>' .$row['call_time']. '</td>' . '<td>' 
                    .$row['first_name']. '</td>' . '<td>' .$row['last_name'].'</td>' . '<td>' .$row['phone'].'</td>' . '<td>' .$row['client_email_address']. '</td>' . '</tr>' ;
            }
    

    }

    // Close the table
    echo '</table>';
}


?>