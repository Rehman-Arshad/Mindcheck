<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$appointment_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$appointment_id || !in_array($status, ['confirmed', 'cancelled'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

// Get doctor ID
$email = $_SESSION["user"];
$result = $database->query("SELECT docid FROM doctor WHERE docemail='$email'");
if (!$result || $result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Doctor not found']);
    exit();
}
$docid = $result->fetch_assoc()['docid'];

// Verify the appointment belongs to this doctor
$verify_query = "
    SELECT a.appoid 
    FROM appointment a
    JOIN schedule s ON a.scheduleid = s.scheduleid
    WHERE a.appoid = ? AND s.docid = ?
";

$stmt = $database->prepare($verify_query);
$stmt->bind_param("ii", $appointment_id, $docid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Appointment not found']);
    exit();
}

// Update the appointment status
$update_query = "UPDATE appointment SET status = ? WHERE appoid = ?";
$stmt = $database->prepare($update_query);
$stmt->bind_param("si", $status, $appointment_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment status']);
}
?>
