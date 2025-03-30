<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docid = $_POST['docid'];
    $scheduledate = $_POST['scheduledate'];
    $scheduletime = $_POST['scheduletime'];
    $nop = $_POST['nop'];

    // Validate inputs
    if (empty($docid) || empty($scheduledate) || empty($scheduletime) || empty($nop)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Check if schedule already exists for this doctor at this time
    $check_query = "SELECT COUNT(*) as count FROM schedule 
                   WHERE docid = ? AND scheduledate = ? AND scheduletime = ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("iss", $docid, $scheduledate, $scheduletime);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule already exists for this time slot']);
        exit();
    }

    // Insert new schedule
    $insert_query = "INSERT INTO schedule (docid, scheduledate, scheduletime, nop) VALUES (?, ?, ?, ?)";
    $stmt = $database->prepare($insert_query);
    $stmt->bind_param("issi", $docid, $scheduledate, $scheduletime, $nop);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding schedule']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
