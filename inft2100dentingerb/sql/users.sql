/*
Name: Brody Dentinger
File: users.sql
Date: Nov 4, 2023
Course Code: INFT2100
*/

-- Create the database
-- CREATE DATABASE dentingerb_db;
CREATE EXTENSION IF NOT EXISTS pgcrypto;
-- Drop the users table along with its dependent objects
DROP TABLE IF EXISTS users CASCADE;

-- Drop the clients table
DROP TABLE IF EXISTS clients CASCADE;

-- Drop the calls table if exists
DROP TABLE IF EXISTS calls;

-- CREATE the table, note that id has to be unique, and you must have a name
CREATE TABLE users(
	id SERIAL PRIMARY KEY,
    email_address VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    password VARCHAR(200) NOT  NULL,
    created_time TIMESTAMP NOT NULL,
    last_access TIMESTAMP,
    phone_extension INT NOT NULL,
    user_type VARCHAR (2) NOT NULL
);

-- CREATE the clients table
CREATE TABLE clients(
	client_id SERIAL PRIMARY KEY,
    client_email_address VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    phone BIGINT NOT NULL,
    phone_extension INT,
    created_time TIMESTAMP NOT NULL,
    salesperson_id INT REFERENCES users(id),
    logo_path VARCHAR(100) NOT NULL
);

-- Create the calls table
CREATE TABLE calls(
    call_id SERIAL PRIMARY KEY,
    client_id INT REFERENCES clients(client_id) NOT NULL,
    call_time TIMESTAMP NOT NULL
);

ALTER TABLE users OWNER TO dentingerb;   --this will change the owner to your userid on your local/laptop database (over riding the default owner of postgres)
GRANT ALL ON users TO faculty;  --required when running the script on opentech to allow faculty members to access your databsae table

INSERT INTO users(email_address, first_name, last_name, password, created_time, last_access, phone_extension, user_type) 
VALUES
    -- first record
    (
	'jdoe@durhamcollege.ca',
    'John',
    'Doe',
    /*'$2y$10$0or/2wZSeWVsP.jAo0q5c.10ERNnerCKo2QwDEC0kHsmkv8V99klG',*/
    crypt('password', gen_salt('bf')),
    '20230911',
    '20230911',
    '123',
    'a'
    ),

    -- second record
    (
	'testemail@durhamcollege.ca',
    'Greg',
    'Douchette',
    /*password*/
    crypt('password', gen_salt('bf')),
    '20230911',
    '20230911',
    '123',
    's'
    ),

    -- third record
    (
	'anothertestemail@durhamcollege.ca',
    'Darren',
    'Puffer',
    crypt('password', gen_salt('bf')),
    '20230911',
    '20230911',
    '123',
    'a'
    ),

    -- fourth record
    (
	'sales@gmail.com',
    'Sales',
    'Guy',
    crypt('password', gen_salt('bf')),
    '20230911',
    '20230911',
    '123',
    's'
    );

INSERT INTO clients(client_email_address, first_name, last_name, phone, phone_extension, created_time, salesperson_id, logo_path) 
VALUES
    -- first record
    (
	'client@gmail.com',
    'Client',
    'Guy',
    123456789,
    123,
    '20231103',
    2,
    'company_logo.jpg'
    ),

    -- second record
    (
	'client2@gmail.com',
    'Bob',
    'Gaesian',
    123456789,
    123,
    '20231103',
    2,
    'company_logo.jpg'
    ),

    -- third record
    (
	'client3@gmail.com',
    'Fart',
    'McGhee',
    123456789,
    123,
    '20231103',
    4,
    'company_logo.jpg'
    );

INSERT INTO calls(client_id, call_time) 
VALUES
    -- first record
    (
	1,
    '20231103'
    );
