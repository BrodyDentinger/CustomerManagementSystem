<!-- 

You will create a script called db.php.  This script contains a function named db_connect() that returns a connection to your database using the PHP function pg_connect() 
and creates your prepared statements using the PHP function pg_prepare().  You will include this db.php script in header.php.  
There will be no other way to access the database except through this script and the prepared statements it creates.

You will implement the following prepared statements:
•	user_select
•	user_update_login_time
user_select takes one parameter, an id, and retrieves that user.
user_update_login_time takes two arguments, id and the current time, and updates the appropriate user’s record so the last login timestamp is set to the current timestamp.

You will create a file called db.php.  This file will be the only place you will store your database functions (i.e. db_connect() and all of your prepared statements).  
You will have the following functions:
•	db_connect()
•	user_select()
•	user_authenticate()
-->

<?php
/*
*Will provide the connection information to use in our pg_connect() functions. This way, if we ever need to change the password/login information, we can just change it here.
*
*Takes no parameters.
*@author Brody Dentinger <brody.dentinger@dcmail.ca>
*@return connection $connection  This is the variable that represents our pg_connect() function.
*/
function db_connect(){
    $connection = pg_connect("host=127.0.0.1 dbname=dentingerb_db user=dentingerb password=Broddent94!");
    return $connection;
}

/*
user_select() takes one argument, id, and returns an associative array with that user’s information, or false if that user does not exist.
*/
function user_select($id){

    $conn = db_connect();

    // Selects full record for user matching the inputted email address id
    $statement3 = pg_prepare($conn, "user_select", 
                            'SELECT users.id, users.email_address, users.first_name, users.last_name, users.password, users.created_time, users.last_access, users.phone_extension, users.user_type 
                            FROM users 
                            WHERE users.email_address = $1');

    // executes the above prepared statement
    $user_select_execute = pg_execute($conn, "user_select", array($id));

    // store the results in an assoc. array
    $user_select_results = pg_fetch_assoc($user_select_execute, 0);
};

/*
user_authenticate() takes two arguments, id and password, and returns an associative array with that user’s information, or false if that user does not exist.  
If a record is retrieved (i.e. the user has authenticated) the last login time should be updated to the current timestamp.  
NOTE: user_authenticate(), when a record is returned based on the id provided, is to use password_verify() to check if the password entered matches the bcrypt hash of the password for the user.  
If the user is retrieved successful, the function is to update the user’s last_login time at this point (using the current timestamp). 
 Regarding the banner shown on successful sign-in, we want to see the last_login time, not the current time.

 You will implement the following prepared statements:
•	user_select
•	user_update_login_time
user_select takes one parameter, an id, and retrieves that user.
user_update_login_time takes two arguments, id and the current time, and updates the appropriate user’s record so the last login timestamp is set to the current timestamp.

*/
$statement1 = pg_prepare($conn, "user_select", 
                            'SELECT users.id, users.email_address, users.first_name, users.last_name, users.password, users.created_time, users.last_access, users.phone_extension, users.user_type 
                            FROM users 
                            WHERE users.email_address = $1 ');

// prepared SQL statement to update users table last access with current login time
$statement2 = pg_prepare($conn, "user_update_access_time", 
                        'UPDATE users
                        SET last_access = $1
                        WHERE email_address = $2');
 
function user_select1($id)
{   
    $conn = db_connect();
    $user = 1;
    $result = pg_execute($conn, "user_select", array($id));

    // check how many rows were returned from the result set. If it's more than 0....
    if(pg_num_rows($result) == 1){
        $user = pg_fetch_assoc($result, 0);
    }
    return $user;

} 

function user_authenticate($id, $password){

    //Connect to the database using predefined function
    $conn = db_connect();

    // Compare variables to database with prepared SQL statement
    
    
    // Execute the statement 1, which fetches alls results from a record if user and password match input. Store in $result
    $result = pg_execute($conn, "user_select", array($id));

    // check how many rows were returned from the result set. If it's more than 0....
    if(pg_num_rows($result) > 0){

        // fetch the entire record of the first result (index 0). Return as an assosiative array. Set the $user variable to the result of the first index (which is the username)
        $user = pg_fetch_assoc($result, 0);
        if(password_verify($password, $user['password']))
        {
            $currentTimestamp = date("Y-m-d H:i:s");

            // execute the update the last accessed field statement
            pg_execute($conn, "user_update_access_time", array($currentTimestamp, $id));
                $_SESSION["user"] = $user;
            redirect("./dashboard.php");
        }else{
            $message = "User not found in system";
        }
    }else{
        $id = "";
        $password = "";
        $_SESSION["error_message"] = "Email or password not found."; 
    }
/*
        // change the session user variable to reflect the user's username we just got
        //$_SESSION["user"] = $user["first_name"]; 
        // set the welcome_message session variable to include last_acess
        $_SESSION["last_access"] = $user["last_access"];

        // fetch the current time
        
        //user can now be redirected
        header("Location: ./dashboard.php");
        }
    
    // else, we have no results and therefore user doesn't exist
    
*/

    // If match, password_verify() 



}

?>