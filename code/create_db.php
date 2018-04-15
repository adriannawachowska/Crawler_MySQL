<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS Crawler";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->select_db("Crawler");

// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS SitesViewed (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	site TEXT,
	date TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table SitesViewed created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

// sql to create table
$sql = "CREATE TABLE IF NOT EXISTS SitesAwaiting (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	site VARCHAR(200),
	date TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table SitesAwaiting created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

$result = mysqli_query($conn, "SHOW COLUMNS FROM `SitesViewed` LIKE 'content'");
$exists = (mysqli_num_rows($result)) ? TRUE:FALSE;

if (!$exists) {
    // sql to ALTER table
    $sql = "ALTER TABLE SitesViewed ADD content TEXT after site";

    if (mysqli_query($conn, $sql)) {
        echo "ALTER TABLE SitesViewed successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
    
    // sql to ALTER table
    $sql = "ALTER TABLE SitesViewed MODIFY site VARCHAR(2048)";
    if (mysqli_query($conn, $sql)) {
        echo "ALTER TABLE SitesViewed successfully";
    } else {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

$conn->close();

?>