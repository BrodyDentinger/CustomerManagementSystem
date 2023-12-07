<?php 

/*
Name: Brody Dentinger
File: constants.php
Date: Nov 4, 2023
Course Code: INFT2100
*/

define("HOST", "127.0.0.1"); 

define ("DBNAME", "dentingerb_db");

define ("USER", "dentingerb"); 

define ("PASSWORD", "Broddent94!");



define("ADMINISTRATOR", "a");
define("SALESPERSON", "s");
//define("c", "Administrator");
define("DISABLED", "x");

define("MIN_PASSWORD_LENGTH", 8);

// 1 MB 
define("MAX_IMAGE_SIZE", 1048576);

// Constant for determining how many records to display for each page of our table
define("RESULTS_PER_PAGE", 5);

// To be used in salesperson form
$sales_person_fields = [
    [
        "type" => "text",
        "name" => "first_name",
        "value" => "",
        "label" => "First Name",
    ],
    [
        "type" => "text",
        "name" => "last_name",
        "value" => "",
        "label" => "Last Name",
    ],
    [
        "type" => "email",
        "name" => "email",
        "value" => "",
        "label" => "Email",
    ],
    [
        "type" => "number",
        "name" => "extension",
        "value" => "",
        "label" => "Extension",
    ],
];


// To be used to popualte the client form
$client_fields = [
    [
        "type" => "text",
        "name" => "first_name",
        "value" => "",
        "label" => "First Name",
    ],
    [
        "type" => "text",
        "name" => "last_name",
        "value" => "",
        "label" => "Last Name",
    ],
    [
        "type" => "email",
        "name" => "email",
        "value" => "",
        "label" => "Email",
    ],
    [
        "type" => "number",
        "name" => "extension",
        "value" => "",
        "label" => "Extension",
    ],
    [
        "type" => "number",
        "name" => "phone",
        "value" => "",
        "label" => "Phone",
    ],
    [
        "type" => "dynamic_select", 
        "name" => "dropdown_field",
        "label" => "Select an Option",
    ],
    [
        "type" => "file", 
        "name" => "file_field",
        "label" => "Client Logo",
    ]
];

// To be used to populate the form on our calls page using the display_form() function
$calls_fields = [
    [
        "type" => "dynamic_select", 
        "name" => "dropdown_field",
        "label" => "Select an Option"
    ]
    ];

// To be used to populate the password change form.
$password_fields = [
    [
        "type" => "password",
        "name" => "password",
        "value" => "",
        "label" => "New Password"
    ],
    [
        "type" => "password",
        "name" => "confirm",
        "value" => "",
        "label" => "Re-type Password"

    ]
];

// Array for salespeople display table
$salespeople_display_fields = [
    [
        "id" => "ID",
        "email_address" => "Email Address",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "created_time" => "Created Time",
        "last_access" => "Last Access",
        "phone_extension" => "Extension",
        "user_type" => "User Type"
    ],
];

// Array for clients display table
$clients_display_fields = [
    [
        "client_id" => "ID",
        "client_email_address" => "Email Address",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "phone" => "Phone",
        "phone_extension" => "Extension",
        "created_time" => "Created Time",
        "salesperson_id" => "Assosiated Salesperson",
        "logo_path" => "Logo Path"
    ],
];

// Array for calls display table
$calls_display_fields = [
    [
        "call_id" => "Call ID",
        "client_id" => "Client ID",
        "call_time" => "Call Time",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "phone" => "Phone",
        "client_email_address" => "Email"
        
    ],
];

// Array of allowable file extensions
$allowed_image_extension_array = [ "gif", "png", "jpg", "jpeg", "svg", "bmp"];

// Array for displaying the table for email account information reset.
$reset_form = [
    [
        "type" => "email",
        "name" => "email",
        "value" => "",
        "label" => "Email Address"
    ],
];


// 
$is_active_form = [
    [
        "type" => "radiobutton",
        "name" => "Active",
        "value" => "",
        "label" => "Active"
    ],
    [
        "type" => "radiobutton",
        "name" => "Inactive",
        "value" => "",
        "label" => "Inactive"
    ],
];


?>



