<?php
session_start();
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['docid'];
    $name = $_POST['docname'];
    $specialties = $_POST['specialties'];
    $tel = $_POST['doctel'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    // Check if email exists for other doctors
    $check_email = $database->query("SELECT * FROM doctor WHERE email = '$email' AND docid != '$id'");
    if ($check_email->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }

    $query = "UPDATE doctor SET 
              docname = '$name',
              specialties = '$specialties',
              doctel = '$tel',
              email = '$email',
              address = '$address',
              status = $status
              WHERE docid = '$id'";

    if ($database->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$database->close();
?>
