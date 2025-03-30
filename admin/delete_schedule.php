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

    // Validate input
    if (empty($scheduleid)) {
        echo json_encode(['success' => false, 'message' => 'Schedule ID is required']);
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

    // Check if there are any appointments for this schedule
    $check_appointments = "SELECT COUNT(*) as count FROM appointment WHERE scheduleid = ?";
    $stmt = $database->prepare($check_appointments);
    $stmt->bind_param("i", $scheduleid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete schedule with existing appointments']);
        exit();
    }

    // Delete schedule
    $delete_query = "DELETE FROM schedule WHERE scheduleid = ?";
    $stmt = $database->prepare($delete_query);
    $stmt->bind_param("i", $scheduleid);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting schedule']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
