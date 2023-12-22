<?php
session_start();

// Database connection
$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "helpdb";

$conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
  die('Could not connect to the database!' . $conn->connect_error);
}

// Check if user is logged in and has a role
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Fetch the user's role from the database
$user_id = $_SESSION['id'];
$sql = "SELECT role FROM user WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_role = $row['role'];
} else {
    // Handle error - user not found
    echo "User not found";
    exit;
}

// Check if user is trying to access a page outside their role
$current_role = basename(dirname($_SERVER['SCRIPT_FILENAME'])); // e.g., 'admin', 'donor', 'recipient'
if (strcasecmp(strtolower(substr($current_role, 0, 1)), strtolower(substr($user_role, 0, 1))) != 0){
    echo $current_role;
    echo $user_role;    

    // echo "<script>window.open('./unauthorized_access.php','_self')</script>"; // Redirect to an error/unauthorized access page
    exit;
}

// Close the database connection
$conn->close();
?>