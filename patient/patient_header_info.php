<?php 

// Import database connection
include("../connection.php");

// Ensure $useremail is set
if (!isset($useremail)) {
    die("User email is not set.");
}

// Use a prepared statement to prevent SQL injection
$stmt = $database->prepare("SELECT pid, pnic FROM patient WHERE pemail = ?");
$stmt->bind_param("s", $useremail);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userId = $user["pid"];
    $userName = $user["pnic"];
} else {
    die("User not found.");
}

// Close statement
$stmt->close();

?>
