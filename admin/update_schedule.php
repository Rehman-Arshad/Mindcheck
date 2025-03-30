<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'a') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleid = $_POST['scheduleid'];
    $docid = $_POST['docid'];
    $scheduledate = $_POST['scheduledate'];
    $scheduletime = $_POST['scheduletime'];
    $nop = $_POST['nop'];

    // Validate inputs
    if (empty($scheduleid) || empty($docid) || empty($scheduledate) || empty($scheduletime) || empty($nop)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Check if schedule exists
    $check_query = "SELECT COUNT(*) as count FROM schedule WHERE scheduleid = ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("i", $scheduleid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule not found']);
        exit();
    }

    // Check if new time slot is available (excluding current schedule)
    $check_query = "SELECT COUNT(*) as count FROM schedule 
                   WHERE docid = ? AND scheduledate = ? AND scheduletime = ? AND scheduleid != ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("issi", $docid, $scheduledate, $scheduletime, $scheduleid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule already exists for this time slot']);
        exit();
    }

    // Update schedule
    $update_query = "UPDATE schedule 
                    SET docid = ?, scheduledate = ?, scheduletime = ?, nop = ? 
                    WHERE scheduleid = ?";
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("issii", $docid, $scheduledate, $scheduletime, $nop, $scheduleid);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating schedule']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
