<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit;
}

include("../connection.php");

// Get patient ID
$email = $_SESSION["user"];
$result = $database->query("SELECT pid FROM patient WHERE pemail='$email'");
if (!$result || $result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Patient not found']);
    exit;
}
$pid = $result->fetch_assoc()['pid'];

// Verify schedule ID was sent
if (!isset($_POST['scheduleid'])) {
    echo json_encode(['success' => false, 'message' => 'No schedule selected']);
    exit;
}

$scheduleid = $_POST['scheduleid'];

// Start transaction
$database->begin_transaction();

try {
    // Check if slot is still available
    $check_query = "SELECT s.* FROM schedule s 
                   LEFT JOIN appointment a ON s.scheduleid = a.scheduleid 
                   WHERE s.scheduleid = ? AND a.scheduleid IS NULL
                   AND s.scheduledate >= CURDATE()
                   FOR UPDATE";
    
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("i", $scheduleid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("This time slot is no longer available");
    }

    // Create the appointment
    $insert_query = "INSERT INTO appointment (pid, scheduleid, status) VALUES (?, ?, 'pending')";
    $stmt = $database->prepare($insert_query);
    $stmt->bind_param("ii", $pid, $scheduleid);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to create appointment");
    }

    // Commit transaction
    $database->commit();
    
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);

} catch (Exception $e) {
    // Rollback on error
    $database->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
