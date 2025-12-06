<?php

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

/** connect to the database **/
try 
{
   // Create an instance of PDO (PHP Data Objects) which connects to a MySQL database
   // (PDO defines an interface for accessing databases)
   $db = new PDO($dsn, $username, $password);
}
catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
   $error_message = $e->getMessage();        
   // echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
catch (Exception $e)       // handle any type of exception
{
   $error_message = $e->getMessage();
   // echo "<p>Error message: $error_message </p>";
}

?>