<?php
// database.php

// Database connection settings
$host = 'localhost';       // Database server address
$dbname = 'Ragdoll_IaC_DB';    // Database name
$username = 'Ragdoll';          // Database username

// Password as MD5 hash of the string 'SAELINK'
$password = 'Ragdoll';

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Uncomment below line for debugging purpose
    // echo "Connected successfully"; 
} catch(PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}

?>
