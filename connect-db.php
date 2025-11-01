<?php
// Remember to start the database server (or GCP SQL instance) before trying to connect to it

// https://www.cs.virginia.edu/~adb2xp/template/connect-db.php

/** F25, PHP (on GCP, local XAMPP, or CS server) connect to MySQL (on CS server) **/
$username = 'adb2xp'; // adb2xp
$password = 'Fall2025'; //Fall2025
$host = 'mysql01.cs.virginia.edu';
//$host='localhost';
//$host = '127.0.0.1';
$dbname = 'adb2xp';
$dsn = "mysql:host=$host;dbname=$dbname";
////////////////////////////////////////////

// To find a hostname, access phpMyAdmin
// - select tob "User accounts"
// - locate the username you created, by default, the Host name is localhost

// To find a port number, access phpMyAdmin
// - use Console (bottom)
// - type     SHOW VARIABLES WHERE Variable_name = 'port';
// - execute the query    press Ctrl+Enter
// (default port to mySQL database in XAMPP is 3306)

// Be sure to use the correct database name (also case-sensitive)
//   Note: Looking in the wrong database and/or wrong table may results in either
//         cannot connect to the database, not find table, or no result set.
//         Thus, specify the correct database name


// DSN (Data Source Name) specifies the host computer for the MySQL datbase 
// and the name of the database. If the MySQL datbase is running on the same server
// as PHP, use the localhost keyword to specify the host computer

// To connect to a MySQL database named db-demo, need three arguments: 
// - specify a DSN, username, and password

// Create an instance of PDO (PHP Data Objects) which connects to a MySQL database
// (PDO defines an interface for accessing databases)
// Syntax: 
//    new PDO(dsn, username, password);


/** connect to the database **/
try 
{
//  $db = new PDO("mysql:host=$hostname;dbname=db-demo", $username, $password);
   $db = new PDO($dsn, $username, $password);
   
   // dispaly a message to let us know that we are connected to the database 

   // echo "<p>You are connected to the database -- host=$host</p>";
}
catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
   // Call a method from any object, use the object's name followed by -> and then method's name
   // All exception objects provide a getMessage() method that returns the error message 
   $error_message = $e->getMessage();        
   // echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
catch (Exception $e)       // handle any type of exception
{
   $error_message = $e->getMessage();
   // echo "<p>Error message: $error_message </p>";
}

?>