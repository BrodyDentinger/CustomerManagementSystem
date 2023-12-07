<?php
/*
Name: Brody Dentinger
File: db.php
Date: Nov 4, 2023
Course Code: INFT2100
*/

/*
*Will provide the connection information to use in our pg_connect() functions. This way, if we ever need to change the password/login information, we can just change it here.
*
*Takes no parameters.
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@return connection $connection  This is the variable that represents our pg_connect() function.
*/
function db_connect(){
    $connection = pg_connect("host=" . HOST . " dbname=" . DBNAME . " user=" . USER . " password=" . PASSWORD);
    return $connection;
}

// prepared statement 1
// user_select takes one parameter, an id, and retrieves that user.
pg_prepare(db_connect(), "user_select", 
            'SELECT users.id, users.email_address, users.first_name, users.last_name, users.password, users.created_time, users.last_access, users.phone_extension, users.user_type 
            FROM users 
            WHERE users.email_address = $1');


// prep statement 2
// user_update_login_time takes two arguments, id and the current time, and updates the appropriate user’s record so the last login timestamp is set to the current timestamp.
pg_prepare(db_connect(), "user_update_login_time", 
            'UPDATE users
            SET last_access = $1
            WHERE email_address = $2');

// Prep statement 3
// insert_salesperson will insert a new record into the users table of user_type "s".
pg_prepare(db_connect(), "insert_salesperson", 
            'INSERT INTO users(email_address, first_name, last_name, password, created_time, last_access, phone_extension, user_type)
            VALUES ($1, $2, $3, crypt($4, gen_salt(\'bf\')), $5, $6, $7, $8)');

// Prep statement 4
// Select all users with a specific user type. 
pg_prepare(db_connect(), "select_all_salespeople",
            'SELECT id, first_name, last_name 
             FROM users 
             WHERE user_type = $1
             OR user_type = $2');

// Prep Statement 4.5
// Simply select all users regardless of user type
pg_prepare(db_connect(), "select_all_salespeople_of_all_user_types",
            'SELECT id, first_name, last_name 
             FROM users' );

// Prep statement 5
// Select a user based on a given id
pg_prepare(db_connect(), 'select_current_salesperson', 
            'SELECT id, first_name, last_name 
            FROM users 
            WHERE id = $1');

// Prep Statement 6
// Insert a new client record
pg_prepare(db_connect(), 'insert_client', 
            'INSERT INTO clients(client_email_address, first_name, last_name, phone, phone_extension, created_time, salesperson_id, logo_path) 
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8)');

// Prep Statement 7
// Fetch all clients belonging to a given salesperson
pg_prepare(db_connect(), 'fetch_clients_for_salesperson', 
            'SELECT client_id, first_name, last_name 
            FROM clients 
            WHERE salesperson_id = $1');

// Prep Statement 8
// Insert a call record
pg_prepare(db_connect(), 'insert_call', 
            'INSERT INTO calls(client_id, call_time) 
            VALUES ($1, $2)');

// Prep Statement 9
// Update the password of a signed-in user
pg_prepare(db_connect(), 'user_update_password', 
            'UPDATE users 
            SET password = crypt($1, gen_salt(\'bf\'))
            WHERE id = $2' );    
            
// Prep Statement 10 **
// Select all salespeople, using limit and offset. To be used in the table display function to display x amount of results at a time based on page number.
pg_prepare(db_connect(), 'select_page_of_salespeople', 
            'SELECT id, email_address, first_name, last_name, created_time, last_access, phone_extension, user_type
            FROM users
            WHERE user_type = $1
            OR user_type = $2
            ORDER BY id
            LIMIT $3
            OFFSET $4' );

// Prep Statement 11
// Select all clients FOR ONE SALESPERSON using limit and offset, To be used in the table display function to display x amount of results at a time based on page number.
pg_prepare(db_connect(), 'select_page_of_clients', 
            'SELECT client_id, client_email_address, first_name, last_name, phone, phone_extension, created_time, salesperson_id, logo_path
            FROM clients
            WHERE salesperson_id = $1
            ORDER BY client_id
            LIMIT $2
            OFFSET $3' );

// Prep Statement 12
// Select ALL clients FOR ADMIN using limit and offset, To be used in the table display function to display x amount of results at a time based on page number.
pg_prepare(db_connect(), 'select_page_of_client_for_admin', 
            'SELECT client_id, client_email_address, first_name, last_name, phone, phone_extension, created_time, salesperson_id, logo_path
            FROM clients
            ORDER BY client_id
            LIMIT $1
            OFFSET $2' );

// Prep Statement 13
// Select All Clients currently in the Database
pg_prepare(db_connect(), 'select_all_clients', 
            'SELECT client_id, client_email_address, first_name, last_name, phone, phone_extension, created_time, salesperson_id, logo_path
            FROM clients
            ORDER BY client_id' );

// Prep Statement 14
// Join the clients and calls table on their FK
// Then Select all from Calls where the the salesperson id is equal to the currently signed on salesperson
pg_prepare(db_connect(), 'select_all_from_calls_for_user', 
            'SELECT calls.call_id, calls.client_id, calls.call_time 
            FROM calls 
            JOIN clients ON calls.client_id = clients.client_id
            WHERE clients.salesperson_id = $1' );

// Prep Statement 15
// Join the clients and calls table on their FK
// Then Select all from Calls where the the salesperson id is equal to the currently signed on salesperson
// Uses limit and offset for table display
pg_prepare(db_connect(), 'select_page_of_calls', 
            'SELECT calls.call_id, calls.client_id, calls.call_time, first_name, last_name, phone, client_email_address 
            FROM calls
            JOIN clients ON calls.client_id = clients.client_id
            WHERE clients.salesperson_id = $1 
            ORDER BY calls.call_id 
            LIMIT $2
            OFFSET $3' );

// Prep Statement 16
// A select statement to fetch all client data related to ONE given client id
pg_prepare(db_connect(), 'select_client_from_id', 
            'SELECT first_name, last_name, client_email_address, phone_extension, created_time, phone, logo_path 
            FROM clients 
            WHERE client_id = $1' );

// Prep Statement 17
// Updates the clients table where client id is given
pg_prepare(db_connect(), 'update_client', 
            'UPDATE clients 
            SET first_name = $1, last_name = $2, client_email_address = $3, phone_extension = $4, phone = $5, logo_path = $6
            WHERE client_id = $7' );

// Prep Statement 18
// Checks the users table for the existance of a given email address
pg_prepare(db_connect(), 'search_users_for_email', 
            'SELECT email_address 
            FROM users 
            WHERE email_address = $1' );

// Prep Statement 19 
// Updates the user type to a given status in the users table given a user's (salespersons) id
pg_prepare(db_connect(), 'update_salesperson_usertype', 
            'UPDATE users
            SET user_type = $1
            WHERE id = $2' );

// function 1
//user_select() takes one argument, id, and returns an associative array with that user’s information, or false if that user does not exist.
function user_select($id){

    // connect to DB
    $connection = db_connect();

    // exectutes user_select 
    $result = pg_execute($connection, "user_select", array($id));

    // returns an assoc array if there are any matches
    if(pg_num_rows($result) > 0){

        // returns an assoc array of the results stored in $user
        $user = pg_fetch_assoc($result, 0);
        return $user;

    // else false if does not exist
    }else{
        $_SESSION["error_message"] = "Email or password not found.";
        return false;
    }
};

// function 2
//user_authenticate() takes two arguments, id and password, and returns an associative array with that user’s information, or false if that user does not exist.  
// If a record is retrieved (i.e. the user has authenticated) the last login time should be updated to the current timestamp. 
// NOTE: user_authenticate(), when a record is returned based on the id provided, is to use password_verify() to check if the password entered matches the bcrypt hash of the password for the user.  
function user_authenticate($id, $password){

    // connec to DB
    $connection = db_connect();

    // Check if the user with the provided ID exists
    $user = user_select($id);

    // if user is not equal to false (if user select has returned an assoc array matching our user name)
    if($user !== false)
    {
        
        if($user['user_type'] == DISABLED.SALESPERSON){
            $_SESSION['inactive_message'] = 'Your Account has been Inactivated. Please contact an administrator.';
            // unset($_SESSION['user']);
            // $_SESSION['user'] = '';
        }

        // check if entered plain password matches the encrypted password from the database fetch
        else if(password_verify($password, $user['password']))
        {
            
            // set session variable to last access time
            $_SESSION['last_access'] = $user['last_access'];
    
            // set session variable to user name
            $_SESSION['user'] = $user['first_name'];

            // Set session variable for user's last name
            $_SESSION['last_name'] = $user['last_name'];

            // set the email session variable for use in activity logging
            $_SESSION['email_address'] = $user['email_address'];

            //set the user_type session variable for use in lab 2 ************************************
            $_SESSION['user_type'] = $user['user_type'];

            // Set the session variable id to the signed in user's id
            $_SESSION['id'] = $user['id'];
            
            // set the welcome message to a session variable
            $_SESSION['welcome_message'] = "Welcome ".$_SESSION["user"] . "! Last Accessed: " . $_SESSION['last_access'];

            // update last access time 
            $currentTimestamp = date("Y-m-d H:i:s");
    
            // execute the update the last accessed field statement
            pg_execute($connection, "user_update_login_time", array($currentTimestamp, $id));
            
            // log successful login
            // call log activity, which opens/creates a file with the current date, and logs a specific message based on what we want to log. 
            log_activity("\n" . $currentTimestamp . ". Successful sign in. User: " . $_SESSION['email_address'] . ".\n");

            // If the user is disabled
            if($_SESSION['user_type'] == DISABLED.SALESPERSON){

                // Redirect to the sign-in page.
                header("Location: ./sign-in.php");
                // Provide a message saying their account is inactive.
                $_SESSION['inactive_message'] = 'Your Account has been Inactivated. Please contact an administrator.';
            }
            else
            {
                // redirect to dashboard
                header("Location: ./dashboard.php");
            }

        }
        else
        {
            $_SESSION["error_message"] = "Email or password not found.";

            // fetch current time
            $currentTimestamp = $currentTimestamp = date("Y-m-d H:i:s");

            // log unsuccessful login
            // call log activity, which opens/creates a file with the current date, and logs a specific message based on what we want to log. 
            log_activity("\n" . $currentTimestamp . ". Failed login attempt. User: ". trim($_POST['id']) . ".\n");
            
        } 

    // else false if does not exist
    }
    else
    {
        $id = "";
        $password = "";
        $_SESSION["error_message"] = "Email or password not found.";

        // fetch current time
        $currentTimestamp = $currentTimestamp = date("Y-m-d H:i:s");

        // log unsuccessful login
        // call log activity, which opens/creates a file with the current date, and logs a specific message based on what we want to log.
        log_activity("\n" . $currentTimestamp . ". Failed login attempt. User: ". trim($_POST['id']) . ".\n");

    }

    
}
?>