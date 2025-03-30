<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include("../connection.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM doctor WHERE docid = '$id'";
    $result = $database->query($query);
    
    if ($result && $result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($doctor);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Doctor not found']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No ID provided']);
}
?>
