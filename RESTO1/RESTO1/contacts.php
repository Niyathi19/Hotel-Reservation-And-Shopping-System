<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "order";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);

    // Insert data into contacts table
    $sql = "INSERT INTO contacts (name, phone_number) VALUES ('$name', '$phone_number')";

    if ($conn->query($sql) === TRUE) {
        header("Location: submit.html"); // Redirect to a success page
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
